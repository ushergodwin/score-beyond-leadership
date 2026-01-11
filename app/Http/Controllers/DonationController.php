<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DonationRequest;
use App\Models\Donation;
use App\Models\DonationImpactTier;
use App\Services\Payments\PesapalPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class DonationController extends Controller
{
    public function __construct(
        private readonly PesapalPaymentService $pesapalPaymentService,
    ) {
    }

    public function index(): Response
    {
        // Fetch active impact tiers from database, ordered by display_order
        $impactTiers = DonationImpactTier::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('currency')
            ->orderBy('amount')
            ->get()
            ->map(fn ($tier) => [
                'amount' => (float) $tier->amount,
                'label' => $tier->label,
                'description' => $tier->description,
                'currency' => $tier->currency,
            ])
            ->toArray();

        return Inertia::render('Donate/Index', [
            'impactTiers' => $impactTiers,
            'prefill' => [
                'first_name' => auth()->user()?->name,
                'email' => auth()->user()?->email,
            ],
        ]);
    }

    public function store(DonationRequest $request): RedirectResponse|HttpResponse
    {
        $data = $request->validated();

        try {
            $redirectUrl = DB::transaction(function () use ($data) {
                $donation = Donation::create([
                    'donation_number' => 'DON-' . strtoupper(uniqid()),
                    'customer_id' => auth()->id() ? \App\Models\Customer::where('user_id', auth()->id())->first()?->id : null,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'country' => $data['country'] ?? null,
                    'organization' => $data['organization'] ?? null,
                    'address' => $data['address'] ?? null,
                    'amount' => $data['amount'],
                    'currency' => $data['currency'] === 'EUR' ? 'EUR' : ($data['currency'] ?? 'UGX'), // AURO is EUR
                    'exchange_rate' => 1.0, // TODO: Get actual exchange rate
                    'is_recurring' => $data['is_recurring'] ?? false,
                    'frequency' => ($data['frequency'] ?? null) === 'one-time' ? null : ($data['frequency'] ?? null),
                    'impact_tier' => $data['impact_tier'] ?? null,
                    'payment_status' => 'pending',
                    'tax_receipt_requested' => $data['tax_receipt_requested'] ?? false,
                    'consent_to_contact' => $data['consent_to_contact'] ?? false,
                    'communications_opt_in' => $data['communications_opt_in'] ?? false,
                    'message' => $data['message'] ?? null,
                ]);

                // Build payload according to Pesapal API 3.0 documentation
                // https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/submitorderrequest
                $pesapalPayload = [
                    'id' => $donation->donation_number, // Merchant reference (max 50 chars, must be unique)
                    'currency' => $donation->currency, // ISO Currency Code
                    'amount' => (float) $donation->amount,
                    'description' => 'Donation to Score Beyond Leadership', // Max 100 chars
                    'callback_url' => route('payments.pesapal.callback'),
                    'notification_id' => (string) config('services.pesapal.ipn_id'), // IPN notification ID (GUID)
                    'billing_address' => [
                        'phone_number' => $donation->phone ?? '',
                        'email_address' => $donation->email,
                        'country_code' => $donation->country ?? 'UG',
                        'first_name' => $donation->first_name,
                        'last_name' => $donation->last_name,
                        'line_1' => $donation->address ?? '',
                        'line_2' => '',
                        'city' => '',
                        'state' => '',
                        'postal_code' => '',
                    ],
                ];

                // If recurring donation, add account_number field to enable recurring payments
                // According to Pesapal API 3.0: account_number enables recurring payment option
                // The customer will be able to set up recurring payments during checkout
                if ($donation->is_recurring) {
                    // Use donation ID as account number for tracking
                    $pesapalPayload['account_number'] = (string) $donation->id;
                }

                $transaction = $this->pesapalPaymentService->createPaymentIntent($donation, $pesapalPayload);

                // Store merchant reference as donation ID for callback matching
                $transaction->pesapal_merchant_reference = (string) $donation->id;
                $transaction->save();

                return $transaction->provider_reference; // Redirect URL
            });

            return Inertia::location($redirectUrl);
        } catch (RuntimeException $e) {
            Log::error('Donation processing failed: ' . $e->getMessage(), ['exception' => $e]);

            return back()->withErrors(['payment' => 'Donation processing failed. Please try again or contact support.']);
        }
    }

    public function result(string $donationNumber): Response
    {
        $donation = Donation::where('donation_number', $donationNumber)->firstOrFail();

        // Status values come from Pesapal status_code mapping: completed, failed, reversed, pending
        $statusLabel = match ($donation->payment_status) {
            'completed' => 'Donation confirmed',
            'failed' => 'Donation failed',
            'reversed' => 'Donation reversed',
            default => 'Donation pending',
        };

        $statusIntent = match ($donation->payment_status) {
            'completed' => 'success',
            'failed', 'reversed' => 'danger',
            default => 'warning',
        };

        return Inertia::render('Donate/Result', [
            'donation' => [
                'donation_number' => $donation->donation_number,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'payment_status' => $donation->payment_status,
                'status_label' => $statusLabel,
                'status_intent' => $statusIntent,
                'donor_name' => $donation->first_name . ' ' . $donation->last_name,
                'donor_email' => $donation->email,
                'paid_at' => $donation->paid_at?->format('M d, Y H:i'),
                'message' => $donation->message,
            ],
        ]);
    }
}

