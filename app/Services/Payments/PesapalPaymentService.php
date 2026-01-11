<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use App\Services\Pesapal\PesapalClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Log;

/**
 * Handles higher-level payment orchestration against the Pesapal API.
 */
class PesapalPaymentService
{
    public function __construct(
        private readonly PesapalClient $client,
    ) {
    }

    /**
     * Creates a Pesapal order for the provided payable model.
     *
     * @param  Model  $payable  Typically an Order or Donation model.
     * @param  array<string, mixed>  $payload
     */
    public function createPaymentIntent(Model $payable, array $payload): PaymentTransaction
    {
        if (!method_exists($payable, 'getAttribute')) {
            throw new RuntimeException('Invalid payable provided to Pesapal payment service.');
        }

        /** @var PaymentTransaction $transaction */
        $transaction = DB::transaction(function () use ($payable, $payload) {
            $submitResponse = $this->client->submitOrderRequest($payload);

            Log::info('Pesapal submit response', $submitResponse);
            $orderTrackingId = Arr::get($submitResponse, 'order_tracking_id');
            $redirectUrl = Arr::get($submitResponse, 'redirect_url');

            if (!is_string($orderTrackingId) || $orderTrackingId === '') {
                throw new RuntimeException('Pesapal response missing order tracking id.');
            }

            $transaction = new PaymentTransaction();
            $transaction->payable()->associate($payable);
            $transaction->provider = 'pesapal';
            $transaction->provider_reference = $redirectUrl; // This is the redirect_url from Pesapal
            $transaction->pesapal_tracking_id = $orderTrackingId; // order_tracking_id from Pesapal
            $transaction->pesapal_merchant_reference = (string) Arr::get($submitResponse, 'merchant_reference', Arr::get($payload, 'id'));
            $transaction->status = 'pending';
            $transaction->currency = (string) Arr::get($payload, 'currency');
            $transaction->amount = (float) Arr::get($payload, 'amount');
            $transaction->payload = $submitResponse;
            $transaction->save();

            return $transaction;
        });

        return $transaction;
    }

    /**
     * Refreshes the transaction status from Pesapal and updates the local record.
     * According to API 3.0: status_code 0=INVALID, 1=COMPLETED, 2=FAILED, 3=REVERSED
     * We use status_code as the primary source of truth for payment status.
     */
    public function syncTransactionStatus(PaymentTransaction $transaction): PaymentTransaction
    {
        if ($transaction->pesapal_tracking_id === null || $transaction->pesapal_tracking_id === '') {
            throw new RuntimeException('Cannot sync Pesapal transaction without a tracking id.');
        }

        $statusResponse = $this->client->getTransactionStatus($transaction->pesapal_tracking_id);
        
        // Use status_code as the primary source of truth
        // status_code: 0=INVALID, 1=COMPLETED, 2=FAILED, 3=REVERSED
        $statusCode = (int) Arr::get($statusResponse, 'status_code', -1);
        
        Log::info('Pesapal transaction status sync', [
            'tracking_id' => $transaction->pesapal_tracking_id,
            'status_code' => $statusCode,
            'payment_status_description' => Arr::get($statusResponse, 'payment_status_description'),
        ]);
        
        // Map status_code to our internal status
        $status = match ($statusCode) {
            1 => 'completed', // COMPLETED - Payment successful
            2 => 'failed', // FAILED - Payment failed
            3 => 'reversed', // REVERSED - Payment reversed
            0 => 'pending', // INVALID - Transaction invalid or not yet processed
            default => 'pending', // Unknown status code, treat as pending
        };

        $transaction->status = $status;
        
        // Store the full response in payload
        $transaction->payload = $statusResponse;
        
        // Extract and store important fields from GetTransactionStatus response
        // According to API 3.0 docs: payment_method, confirmation_code, payment_account, created_date
        $paymentMethod = Arr::get($statusResponse, 'payment_method');
        $confirmationCode = Arr::get($statusResponse, 'confirmation_code');
        $paymentAccount = Arr::get($statusResponse, 'payment_account');
        
        // Store these in the payload for easy access (they're already in payload, but we can add them as metadata)
        // Note: If you want to add dedicated columns for these, you can create a migration
        // For now, they're accessible via $transaction->payload['payment_method'], etc.
        
        // Set paid_at timestamp when payment is completed
        if ($status === 'completed' && $transaction->paid_at === null) {
            $createdDate = Arr::get($statusResponse, 'created_date');
            $transaction->paid_at = $createdDate ? \Carbon\Carbon::parse($createdDate) : now();
        }
        
        // Log important payment details for completed transactions
        if ($status === 'completed') {
            Log::info('Payment completed via GetTransactionStatus', [
                'tracking_id' => $transaction->pesapal_tracking_id,
                'merchant_reference' => Arr::get($statusResponse, 'merchant_reference'),
                'payment_method' => $paymentMethod,
                'confirmation_code' => $confirmationCode,
                'amount' => Arr::get($statusResponse, 'amount'),
                'currency' => Arr::get($statusResponse, 'currency'),
            ]);
        }
        
        $transaction->save();

        return $transaction;
    }
}


