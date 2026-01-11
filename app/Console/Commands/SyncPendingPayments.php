<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\AccountCreationService;
use App\Services\NotificationService;
use App\Services\Payments\PesapalPaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:sync-pending 
                            {--minutes=30 : Only check payments pending for at least this many minutes}
                            {--max-age=1440 : Maximum age in minutes (default 24 hours)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync payment status for pending orders from Pesapal (backup for missed callbacks)';

    public function __construct(
        private readonly PesapalPaymentService $pesapal,
        private readonly NotificationService $notificationService,
        private readonly AccountCreationService $accountCreationService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minMinutes = (int) $this->option('minutes');
        $maxAgeMinutes = (int) $this->option('max-age');

        $this->info("Syncing pending payments (min age: {$minMinutes} minutes, max age: {$maxAgeMinutes} minutes)...");

        // Find orders with pending payments that are at least X minutes old
        $cutoffTime = now()->subMinutes($minMinutes);
        $maxAgeTime = now()->subMinutes($maxAgeMinutes);

        $pendingOrders = Order::query()
            ->where('payment_status', 'pending')
            ->whereNotNull('pesapal_order_tracking_id')
            ->where('created_at', '>=', $maxAgeTime)
            ->where('created_at', '<=', $cutoffTime)
            ->with('customer')
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending payments found to sync.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingOrders->count()} pending order(s) to check.");

        $synced = 0;
        $completed = 0;
        $failed = 0;
        $stillPending = 0;

        foreach ($pendingOrders as $order) {
            try {
                $transaction = $order->paymentTransactions()
                    ->where('pesapal_tracking_id', $order->pesapal_order_tracking_id)
                    ->latest()
                    ->first();

                if (!$transaction) {
                    $this->warn("No transaction found for order {$order->order_number}");
                    continue;
                }

                $oldStatus = $order->payment_status;

                // Sync status from Pesapal
                $this->pesapal->syncTransactionStatus($transaction);
                $order->refresh();

                // Update order status based on transaction
                $this->syncOrderFromTransaction($order, (string) $transaction->status);
                $order->refresh();
                $order->load('customer');

                $synced++;

                if ($order->payment_status === 'completed' && $oldStatus === 'pending') {
                    $completed++;

                    // Handle inline account creation after successful payment
                    if ($this->shouldCreateAccount($order)) {
                        $this->handleAccountCreation($order);
                    }

                    // Create notification
                    $this->notificationService->notifyOrderStatus($order, $order->payment_status);

                    $this->info("✓ Order {$order->order_number}: Payment completed");
                } elseif ($order->payment_status === 'failed' && $oldStatus === 'pending') {
                    $failed++;
                    $this->notificationService->notifyOrderStatus($order, $order->payment_status);
                    $this->warn("✗ Order {$order->order_number}: Payment failed");
                } else {
                    $stillPending++;
                    $this->line("  Order {$order->order_number}: Still pending");
                }
            } catch (\Exception $e) {
                Log::error("Failed to sync payment for order {$order->order_number}", [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to sync order {$order->order_number}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Sync complete:");
        $this->line("  Total checked: {$synced}");
        $this->line("  Completed: {$completed}");
        $this->line("  Failed: {$failed}");
        $this->line("  Still pending: {$stillPending}");

        return Command::SUCCESS;
    }

    /**
     * Syncs order status based on transaction status from Pesapal.
     */
    private function syncOrderFromTransaction(Order $order, string $transactionStatus): void
    {
        $normalized = strtolower($transactionStatus);

        if ($normalized === 'completed') {
            $order->payment_status = 'completed';
            $order->status = $order->status === 'pending' ? 'processing' : $order->status;
            $order->placed_at = $order->placed_at ?? now();
        } elseif ($normalized === 'failed') {
            $order->payment_status = 'failed';
            $order->status = 'payment_failed';
        } elseif ($normalized === 'reversed') {
            $order->payment_status = 'failed';
            $order->status = 'payment_failed';
        } else {
            $order->payment_status = 'pending';
        }

        $order->save();
    }

    /**
     * Check if account creation should be triggered for this order.
     */
    private function shouldCreateAccount(Order $order): bool
    {
        if (!config('services.checkout.inline_account_creation', true)) {
            return false;
        }

        if ($order->customer?->user_id !== null) {
            return false;
        }

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
                return;
            }

            if ($customer->user_id !== null) {
                return;
            }

            $metadata = $order->metadata ?? [];
            $password = $metadata['account_password'] ?? null;

            if (!$password) {
                return;
            }

            $user = $this->accountCreationService->createFromCheckout(
                $customer,
                $password,
                trim("{$customer->first_name} {$customer->last_name}")
            );

            Log::info('Account created from pending payment sync', [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            // Remove password from metadata
            $metadata['account_password'] = null;
            $order->metadata = $metadata;
            $order->save();
        } catch (\Exception $e) {
            Log::error('Failed to create account from pending payment sync', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

