<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AccountCreationService;
use App\Services\EmailService;
use App\Services\NotificationService;
use App\Services\Payments\PesapalPaymentService;
use App\Support\Currency\PriceDisplay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PaymentStatusController extends Controller
{
    public function __construct(
        private readonly PesapalPaymentService $pesapal,
        private readonly NotificationService $notificationService,
        private readonly AccountCreationService $accountCreationService,
        private readonly EmailService $emailService,
    ) {
    }

    /**
     * Handles Pesapal callback after payment.
     * According to API 3.0: Callback includes OrderTrackingId, OrderMerchantReference, OrderNotificationType=CALLBACKURL
     */
    public function callback(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'OrderTrackingId' => ['required', 'string'],
            'OrderMerchantReference' => ['required', 'string'],
            'OrderNotificationType' => ['nullable', 'string'],
        ]);

        // OrderMerchantReference is the 'id' we sent in SubmitOrderRequest (order_number or donation_number)
        // But for donations, we also store the donation ID in pesapal_merchant_reference
        $order = Order::with('customer')->where('order_number', $data['OrderMerchantReference'])->first();
        $donation = null;

        if ($order === null) {
            // Try to find donation by donation_number
            $donation = \App\Models\Donation::where('donation_number', $data['OrderMerchantReference'])->first();
            
            // If not found, try by ID (stored in pesapal_merchant_reference)
            if ($donation === null) {
                $transaction = \App\Models\PaymentTransaction::where('pesapal_merchant_reference', $data['OrderMerchantReference'])
                    ->where('pesapal_tracking_id', $data['OrderTrackingId'])
                    ->first();
                
                if ($transaction && $transaction->payable_type === \App\Models\Donation::class) {
                    $donation = $transaction->payable;
                }
            }
        }

        if ($order === null && $donation === null) {
            return redirect()
                ->route('shop.index')
                ->withErrors(['payment' => 'We could not find your transaction. Please contact support.']);
        }

        $payable = $order ?? $donation;
        $transaction = $payable->paymentTransactions()
            ->where('pesapal_tracking_id', $data['OrderTrackingId'])
            ->latest()
            ->first();

        if ($transaction === null) {
            $amount = $order ? $order->grand_total : $donation->amount;
            $currency = $order ? $order->currency : $donation->currency;
            $reference = $order ? $order->order_number : $donation->donation_number;

            $transaction = $payable->paymentTransactions()->create([
                'provider' => 'pesapal',
                'provider_reference' => null,
                'status' => 'pending',
                'currency' => $currency,
                'amount' => $amount,
                'pesapal_tracking_id' => $data['OrderTrackingId'],
                'pesapal_merchant_reference' => $reference,
            ]);
        }

        // Sync status from Pesapal
        $transaction = $this->pesapal->syncTransactionStatus($transaction);

        if ($order) {
            $oldStatus = $order->payment_status;
            $this->syncOrderFromTransaction($order, (string) $transaction->status);
            $order->refresh();
            $order->load('customer'); // Ensure customer relationship is loaded
            
            // Handle inline account creation after successful payment
            if ($order->payment_status === 'completed' && $this->shouldCreateAccount($order)) {
                $this->handleAccountCreation($order);
            }
            
            // Create notification if status changed
            if ($oldStatus !== $order->payment_status) {
                $this->notificationService->notifyOrderStatus($order, $order->payment_status);
                
                // Send email notifications
                if ($order->payment_status === 'completed') {
                    $this->emailService->sendOrderConfirmation($order);
                }
            }
            
            return redirect()->route('checkout.result', $order->order_number)
                ->with('success', $order->payment_status === 'completed' 
                    ? 'Payment successful! A confirmation email has been sent to your email address.' 
                    : 'Payment status updated.');
        } else {
            $oldStatus = $donation->payment_status;
            $this->syncDonationFromTransaction($donation, (string) $transaction->status);
            $donation->refresh();
            
            // Create notification if status changed
            if ($oldStatus !== $donation->payment_status) {
                $this->notificationService->notifyDonationStatus($donation, $donation->payment_status);
                
                // Send email notification for completed donations
                if ($donation->payment_status === 'completed') {
                    $this->emailService->sendDonationConfirmation($donation);
                }
            }
            
            return redirect()->route('donate.result', $donation->donation_number);
        }
    }

    public function result(string $order): Response
    {
        $record = Order::with(['items'])->where('order_number', $order)->firstOrFail();

        $statusLabel = $this->statusLabel($record->payment_status);
        $statusIntent = $this->statusIntent($record->payment_status);

        return Inertia::render('Shop/CheckoutResult', [
            'order' => [
                'order_number' => $record->order_number,
                'status' => $record->status,
                'payment_status' => $record->payment_status,
                'status_label' => $statusLabel,
                'status_intent' => $statusIntent,
                'total' => PriceDisplay::forUgx($record->grand_total),
                'items' => $record->items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->product_name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'subtotal' => PriceDisplay::forUgx($item->subtotal),
                ]),
            ],
        ]);
    }

    /**
     * Syncs order status based on transaction status from Pesapal.
     * Transaction status is determined by status_code: 1=completed, 2=failed, 3=reversed, 0=invalid/pending
     */
    private function syncOrderFromTransaction(Order $order, string $transactionStatus): void
    {
        $normalized = Str::lower($transactionStatus);

        // Map transaction status (from status_code) to order payment_status
        // status_code 1 -> 'completed', 2 -> 'failed', 3 -> 'reversed', 0 -> 'pending'
        if ($normalized === 'completed') {
            $order->payment_status = 'completed';
            $order->status = $order->status === 'pending' ? 'processing' : $order->status;
            $order->placed_at = $order->placed_at ?? now();
        } elseif ($normalized === 'failed') {
            $order->payment_status = 'failed';
            $order->status = 'payment_failed';
        } elseif ($normalized === 'reversed') {
            $order->payment_status = 'failed'; // Treat reversed as failed
            $order->status = 'payment_failed';
        } else {
            // 'pending' or unknown status
            $order->payment_status = 'pending';
        }

        $order->save();
    }

    /**
     * Returns human-readable label for payment status.
     * Status values come from Pesapal status_code mapping: completed, failed, reversed, pending
     */
    private function statusLabel(?string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'completed' => 'Payment successful',
            'failed' => 'Payment failed',
            'reversed' => 'Payment reversed',
            default => 'Payment pending',
        };
    }

    /**
     * Returns UI intent (color/styling) for payment status.
     * Status values come from Pesapal status_code mapping: completed, failed, reversed, pending
     */
    private function statusIntent(?string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'completed' => 'success',
            'failed', 'reversed' => 'danger',
            default => 'warning',
        };
    }

    /**
     * Syncs donation status based on transaction status from Pesapal.
     * Transaction status is determined by status_code: 1=completed, 2=failed, 3=reversed, 0=invalid/pending
     */
    private function syncDonationFromTransaction(\App\Models\Donation $donation, string $transactionStatus): void
    {
        $normalized = Str::lower($transactionStatus);

        // Map transaction status (from status_code) to donation payment_status
        // status_code 1 -> 'completed', 2 -> 'failed', 3 -> 'reversed', 0 -> 'pending'
        if ($normalized === 'completed') {
            $donation->payment_status = 'completed';
            $donation->paid_at = $donation->paid_at ?? now();
        } elseif ($normalized === 'failed') {
            $donation->payment_status = 'failed';
        } elseif ($normalized === 'reversed') {
            $donation->payment_status = 'failed'; // Treat reversed as failed
        } else {
            // 'pending' or unknown status
            $donation->payment_status = 'pending';
        }

        $donation->save();
    }

    /**
     * Check if account creation should be triggered for this order.
     */
    private function shouldCreateAccount(Order $order): bool
    {
        // Check if feature is enabled
        if (!config('services.checkout.inline_account_creation', true)) {
            return false;
        }

        // Check if user is already authenticated
        if (auth()->check()) {
            return false;
        }

        // Check if account creation was requested
        $metadata = $order->metadata ?? [];
        return (bool) ($metadata['create_account'] ?? false);
    }

    /**
     * Handle account creation after successful payment.
     */
    private function handleAccountCreation(Order $order): void
    {
        try {
            $customer = $order->customer;
            
            if (!$customer) {
                Log::warning('Cannot create account: order has no customer', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
                return;
            }

            // Check if customer already has a user account
            if ($customer->user_id !== null) {
                Log::info('Account creation skipped: customer already has user account', [
                    'customer_id' => $customer->id,
                    'user_id' => $customer->user_id,
                ]);
                return;
            }

            $metadata = $order->metadata ?? [];
            $password = $metadata['account_password'] ?? null;

            if (!$password) {
                Log::warning('Cannot create account: password not found in order metadata', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
                return;
            }

            // Create user account and link to customer
            $user = $this->accountCreationService->createFromCheckout(
                $customer,
                $password,
                trim("{$customer->first_name} {$customer->last_name}")
            );

            // Authenticate the new user
            $this->accountCreationService->authenticateUser($user);

            Log::info('Account created successfully from checkout', [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            // Remove password from metadata for security
            $metadata['account_password'] = null;
            $order->metadata = $metadata;
            $order->save();
        } catch (\Exception $e) {
            Log::error('Failed to create account from checkout', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - account creation failure shouldn't break the payment flow
        }
    }
}
