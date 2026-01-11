<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
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
                Log::warning('Cannot send order confirmation email: order has no customer', [
                    'order_id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                ]);
                return;
            }

            if ($this->order->customer->user) {
                $this->order->customer->load('user');
            }

            $email = $this->order->customer->email ?? $this->order->customer->user?->email;

            if (!$email) {
                Log::warning('Cannot send order confirmation email: no email address found', [
                    'order_id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                ]);
                return;
            }

            Mail::to($email)->send(new OrderConfirmationMail($this->order));

            Log::info('Order confirmation email sent', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

