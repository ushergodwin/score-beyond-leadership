<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\AccountCreationService;
use App\Services\EmailService;
use App\Services\NotificationService;
use App\Services\Payments\PesapalPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles Pesapal IPN (Instant Payment Notification) callbacks.
 * According to API 3.0: IPN includes OrderTrackingId, OrderMerchantReference, OrderNotificationType=IPNCHANGE
 * Must respond with JSON: {"orderNotificationType":"IPNCHANGE","orderTrackingId":"...","orderMerchantReference":"...","status":200}
 */
class IpnController extends Controller
{
    public function __construct(
        private readonly PesapalPaymentService $pesapal,
        private readonly NotificationService $notificationService,
        private readonly AccountCreationService $accountCreationService,
        private readonly EmailService $emailService,
    ) {
    }

    /**
     * Handles Pesapal IPN (Instant Payment Notification) callbacks.
     * 
     * According to API 3.0 documentation:
     * - IPN can be GET or POST depending on registration
     * - IPN includes: OrderTrackingId, OrderMerchantReference, OrderNotificationType=IPNCHANGE
     * - Must respond with JSON: {"orderNotificationType":"IPNCHANGE","orderTrackingId":"...","orderMerchantReference":"...","status":200}
     * - After receiving IPN, we call GetTransactionStatus to get the actual payment status
     * 
     * @see https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/gettransactionstatus
     */
    public function handle(Request $request): JsonResponse
    {
        // IPN can be GET or POST depending on registration
        // Extract parameters from both input (POST) and query (GET)
        $orderTrackingId = $request->input('OrderTrackingId') ?? $request->query('OrderTrackingId');
        $orderMerchantReference = $request->input('OrderMerchantReference') ?? $request->query('OrderMerchantReference');
        $orderNotificationType = $request->input('OrderNotificationType') ?? $request->query('OrderNotificationType') ?? 'IPNCHANGE';

        // Validate required parameters
        if (!$orderTrackingId || !$orderMerchantReference) {
            Log::warning('Pesapal IPN received with missing parameters', [
                'request_all' => $request->all(),
                'orderTrackingId' => $orderTrackingId,
                'orderMerchantReference' => $orderMerchantReference,
            ]);
            
            // Return error response as per API 3.0 requirements
            return response()->json([
                'orderNotificationType' => $orderNotificationType,
                'orderTrackingId' => $orderTrackingId ?? '',
                'orderMerchantReference' => $orderMerchantReference ?? '',
                'status' => 500, // 500 = IPN request was received but there was an issue
            ], 500);
        }

        try {
            // Find the transaction
            $transaction = PaymentTransaction::where('pesapal_tracking_id', $orderTrackingId)
                ->where('pesapal_merchant_reference', $orderMerchantReference)
                ->first();

            if (!$transaction) {
                // Try to find by order/donation number
                $order = Order::where('order_number', $orderMerchantReference)->first();
                $donation = null;

                if (!$order) {
                    $donation = Donation::where('donation_number', $orderMerchantReference)->first();
                }

                if (!$order && !$donation) {
                    Log::warning('Pesapal IPN: Transaction not found', [
                        'orderTrackingId' => $orderTrackingId,
                        'orderMerchantReference' => $orderMerchantReference,
                    ]);

                    return response()->json([
                        'orderNotificationType' => $orderNotificationType ?? 'IPNCHANGE',
                        'orderTrackingId' => $orderTrackingId,
                        'orderMerchantReference' => $orderMerchantReference,
                        'status' => 500,
                    ], 500);
                }

                $payable = $order ?? $donation;
                $amount = $order ? $order->grand_total : $donation->amount;
                $currency = $order ? $order->currency : $donation->currency;

                $transaction = $payable->paymentTransactions()->create([
                    'provider' => 'pesapal',
                    'provider_reference' => null,
                    'status' => 'pending',
                    'currency' => $currency,
                    'amount' => $amount,
                    'pesapal_tracking_id' => $orderTrackingId,
                    'pesapal_merchant_reference' => $orderMerchantReference,
                ]);
            }

            // Sync status from Pesapal
            $transaction = $this->pesapal->syncTransactionStatus($transaction);

            // Update the payable model
            $payable = $transaction->payable;
            if ($payable instanceof Order) {
                $oldStatus = $payable->payment_status;
                $this->syncOrderFromTransaction($payable, (string) $transaction->status);
                $payable->refresh();
                $payable->load('customer'); // Ensure customer relationship is loaded
                
                // Handle inline account creation after successful payment
                if ($payable->payment_status === 'completed' && $this->shouldCreateAccount($payable)) {
                    $this->handleAccountCreation($payable);
                }
                
                // Create notification if status changed
                if ($oldStatus !== $payable->payment_status) {
                    $this->notificationService->notifyOrderStatus($payable, $payable->payment_status);
                    
                    // Send email notifications
                    if ($payable->payment_status === 'completed') {
                        $this->emailService->sendOrderConfirmation($payable);
                    }
                }
                
                // Send status update emails for shipped/delivered
                if ($payable->status === 'shipped' || $payable->status === 'delivered') {
                    $this->emailService->sendOrderStatusUpdate($payable, $payable->status);
                }
            } elseif ($payable instanceof Donation) {
                $oldStatus = $payable->payment_status;
                $this->syncDonationFromTransaction($payable, (string) $transaction->status);
                $payable->refresh();
                
                // Create notification if status changed
                if ($oldStatus !== $payable->payment_status) {
                    $this->notificationService->notifyDonationStatus($payable, $payable->payment_status);
                    
                    // Send email notification for completed donations
                    if ($payable->payment_status === 'completed') {
                        $this->emailService->sendDonationConfirmation($payable);
                    }
                }
                
                // If this is a recurring donation and payment is completed, 
                // Pesapal will automatically handle future recurring payments
                // The customer will receive an email with subscription management link
                if ($transaction->status === 'completed' && $payable->is_recurring) {
                    Log::info('Recurring donation payment completed', [
                        'donation_id' => $payable->id,
                        'donation_number' => $payable->donation_number,
                        'frequency' => $payable->frequency,
                    ]);
                }
            }

            Log::info('Pesapal IPN processed successfully', [
                'orderTrackingId' => $orderTrackingId,
                'orderMerchantReference' => $orderMerchantReference,
                'payment_status' => $transaction->status,
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
            ]);

            // Return success response as per Pesapal API 3.0 requirements
            // Status 200 = request was received and processed successfully
            return response()->json([
                'orderNotificationType' => $orderNotificationType,
                'orderTrackingId' => $orderTrackingId,
                'orderMerchantReference' => $orderMerchantReference,
                'status' => 200,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Pesapal IPN processing failed', [
                'orderTrackingId' => $orderTrackingId,
                'orderMerchantReference' => $orderMerchantReference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response as per API 3.0 requirements
            // Status 500 = IPN request was received but there was an issue completing the process
            return response()->json([
                'orderNotificationType' => $orderNotificationType ?? 'IPNCHANGE',
                'orderTrackingId' => $orderTrackingId ?? '',
                'orderMerchantReference' => $orderMerchantReference ?? '',
                'status' => 500,
            ], 500);
        }
    }

    private function syncOrderFromTransaction(Order $order, string $transactionStatus): void
    {
        $normalized = strtolower($transactionStatus);

        if (in_array($normalized, ['completed', 'paid', 'success', 'approved'], true)) {
            $order->payment_status = 'completed';
            $order->status = $order->status === 'pending' ? 'processing' : $order->status;
            $order->placed_at = $order->placed_at ?? now();
        } elseif (in_array($normalized, ['failed', 'cancelled', 'denied'], true)) {
            $order->payment_status = 'failed';
            $order->status = 'payment_failed';
        } else {
            $order->payment_status = 'pending';
        }

        $order->save();
    }

    private function syncDonationFromTransaction(Donation $donation, string $transactionStatus): void
    {
        $normalized = strtolower($transactionStatus);

        if (in_array($normalized, ['completed', 'paid', 'success', 'approved'], true)) {
            $donation->payment_status = 'completed';
            $donation->paid_at = $donation->paid_at ?? now();
        } elseif (in_array($normalized, ['failed', 'cancelled', 'denied'], true)) {
            $donation->payment_status = 'failed';
        } else {
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

        // Check if user is already authenticated (not applicable for IPN, but kept for consistency)
        // IPN is server-to-server, so we check if customer already has a user_id

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

            // Note: We don't authenticate the user in IPN context since it's server-to-server
            // Authentication will happen when the user visits the result page via callback

            Log::info('Account created successfully from checkout (IPN)', [
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
            Log::error('Failed to create account from checkout (IPN)', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - account creation failure shouldn't break the payment flow
        }
    }
}

