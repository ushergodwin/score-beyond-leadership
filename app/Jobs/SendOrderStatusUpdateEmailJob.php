<?php

namespace App\Jobs;

use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusUpdateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        public string $status,
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->order->load(['customer', 'items', 'shippingAddress', 'billingAddress']);
            
            if (!$this->order->customer) {
                Log::warning('Cannot send order status update email: order has no customer', [
                    'order_id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                    'status' => $this->status,
                ]);
                return;
            }

            if ($this->order->customer->user) {
                $this->order->customer->load('user');
            }

            $email = $this->order->customer->email ?? $this->order->customer->user?->email;

            if (!$email) {
                Log::warning('Cannot send order status update email: no email address found', [
                    'order_id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                    'status' => $this->status,
                ]);
                return;
            }

            Mail::to($email)->send(new OrderStatusUpdateMail($this->order, $this->status));

            Log::info('Order status update email sent', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->status,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order status update email', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

