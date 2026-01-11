<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Donation;
use App\Models\User;
use App\Models\VolunteerApplication;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification for an order status change.
     */
    public function notifyOrderStatus(Order $order, string $status): void
    {
        $user = $order->customer?->user;
        
        // Only create in-app notification if user has an account
        if ($user) {
            $notifications = match ($status) {
                'completed' => [
                    'type' => 'order_payment_confirmed',
                    'title' => 'Payment Confirmed',
                    'message' => "Your payment for order {$order->order_number} has been confirmed. We're preparing your order for shipment.",
                    'action_url' => route('dashboard.orders.show', $order->order_number),
                ],
                'processing' => [
                    'type' => 'order_processing',
                    'title' => 'Order Processing',
                    'message' => "Your order {$order->order_number} is now being processed.",
                    'action_url' => route('dashboard.orders.show', $order->order_number),
                ],
                'shipped' => [
                    'type' => 'order_shipped',
                    'title' => 'Order Shipped',
                    'message' => "Great news! Your order {$order->order_number} has been shipped.",
                    'action_url' => route('dashboard.orders.show', $order->order_number),
                ],
                'delivered' => [
                    'type' => 'order_delivered',
                    'title' => 'Order Delivered',
                    'message' => "Your order {$order->order_number} has been delivered. We hope you love your purchase!",
                    'action_url' => route('dashboard.orders.show', $order->order_number),
                ],
                'failed' => [
                    'type' => 'order_payment_failed',
                    'title' => 'Payment Failed',
                    'message' => "Payment for order {$order->order_number} failed. Please try again or contact support.",
                    'action_url' => route('dashboard.orders.show', $order->order_number),
                ],
                default => null,
            };

            if ($notifications) {
                $this->create($user, $notifications['type'], $notifications['title'], $notifications['message'], $notifications['action_url'], [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }
        }
    }

    /**
     * Create a notification for a donation status change.
     */
    public function notifyDonationStatus(Donation $donation, string $status): void
    {
        $user = $donation->customer?->user;
        if (!$user) {
            // Try to find user by email
            $user = User::where('email', $donation->email)->first();
        }

        if (!$user) {
            return;
        }

        $notifications = match ($status) {
            'completed' => [
                'type' => 'donation_confirmed',
                'title' => 'Donation Confirmed',
                'message' => "Thank you! Your donation {$donation->donation_number} has been confirmed. Your generosity makes a difference.",
                'action_url' => route('dashboard.donations.show', $donation->donation_number),
            ],
            'failed' => [
                'type' => 'donation_failed',
                'title' => 'Donation Failed',
                'message' => "Payment for donation {$donation->donation_number} failed. Please try again or contact support.",
                'action_url' => route('dashboard.donations.show', $donation->donation_number),
            ],
            default => null,
        };

        if ($notifications) {
            $this->create($user, $notifications['type'], $notifications['title'], $notifications['message'], $notifications['action_url'], [
                'donation_id' => $donation->id,
                'donation_number' => $donation->donation_number,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
            ]);
        }
    }

    /**
     * Create a notification for a volunteer application status change.
     */
    public function notifyVolunteerApplicationStatus(VolunteerApplication $application, string $status): void
    {
        // Try to find user by email
        $user = User::where('email', $application->email)->first();

        if (!$user) {
            return; // Only create notifications for users with accounts
        }

        $notifications = match ($status) {
            'approved' => [
                'type' => 'volunteer_application_approved',
                'title' => 'Application Approved',
                'message' => "Congratulations! Your volunteer application #{$application->id} has been approved. We'll be in touch soon with next steps.",
                'action_url' => null, // Could add a route to view application if needed
            ],
            'rejected' => [
                'type' => 'volunteer_application_rejected',
                'title' => 'Application Update',
                'message' => "Your volunteer application #{$application->id} status has been updated. Please check your email for details.",
                'action_url' => null,
            ],
            'reviewing' => [
                'type' => 'volunteer_application_reviewing',
                'title' => 'Application Under Review',
                'message' => "Your volunteer application #{$application->id} is now under review. We'll notify you once we have an update.",
                'action_url' => null,
            ],
            default => null,
        };

        if ($notifications) {
            $this->create($user, $notifications['type'], $notifications['title'], $notifications['message'], $notifications['action_url'], [
                'application_id' => $application->id,
                'status' => $status,
            ]);
        }
    }

    /**
     * Create a notification.
     */
    public function create(User $user, string $type, string $title, string $message, ?string $actionUrl = null, ?array $data = null): Notification
    {
        try {
            return Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

