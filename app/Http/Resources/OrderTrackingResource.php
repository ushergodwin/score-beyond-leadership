<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Only returns non-sensitive data (no PII).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'timeline' => $this->buildTimeline(),
            'items' => $this->items->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'sku' => $item->sku,
                ];
            }),
            'estimated_delivery' => $this->getEstimatedDelivery(),
            'currency' => $this->currency,
            'grand_total' => $this->grand_total,
            'shipping_method' => $this->shippingMethod ? [
                'name' => $this->shippingMethod->name,
                'estimated_min_days' => $this->shippingMethod->estimated_min_days,
                'estimated_max_days' => $this->shippingMethod->estimated_max_days,
            ] : null,
        ];
    }

    /**
     * Build the order status timeline.
     */
    private function buildTimeline(): array
    {
        $timeline = [];

        // Order placed
        $timeline[] = [
            'status' => 'order_placed',
            'label' => 'Order Placed',
            'date' => ($this->placed_at ?? $this->created_at)?->toIso8601String(),
            'completed' => true,
        ];

        // Payment confirmed
        if ($this->payment_status === 'completed') {
            $paymentTransaction = $this->paymentTransactions
                ->where('status', 'completed')
                ->first();

            $timeline[] = [
                'status' => 'payment_confirmed',
                'label' => 'Payment Confirmed',
                'date' => $paymentTransaction?->paid_at?->toIso8601String() 
                    ?? ($this->placed_at ?? $this->created_at)?->toIso8601String(),
                'completed' => true,
            ];
        } else {
            $timeline[] = [
                'status' => 'payment_confirmed',
                'label' => 'Payment Confirmed',
                'date' => null,
                'completed' => false,
            ];
        }

        // Processing
        if (in_array($this->status, ['processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'processing',
                'label' => 'Processing',
                'date' => $this->updated_at->toIso8601String(),
                'completed' => true,
            ];
        } else {
            $timeline[] = [
                'status' => 'processing',
                'label' => 'Processing',
                'date' => null,
                'completed' => false,
            ];
        }

        // Shipped
        if (in_array($this->status, ['shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'shipped',
                'label' => 'Shipped',
                'date' => $this->updated_at->toIso8601String(),
                'completed' => true,
            ];
        } else {
            $timeline[] = [
                'status' => 'shipped',
                'label' => 'Shipped',
                'date' => null,
                'completed' => false,
            ];
        }

        // Delivered
        if ($this->status === 'delivered') {
            $timeline[] = [
                'status' => 'delivered',
                'label' => 'Delivered',
                'date' => $this->updated_at->toIso8601String(),
                'completed' => true,
            ];
        } else {
            $timeline[] = [
                'status' => 'delivered',
                'label' => 'Delivered',
                'date' => null,
                'completed' => false,
            ];
        }

        return $timeline;
    }

    /**
     * Calculate estimated delivery date.
     */
    private function getEstimatedDelivery(): ?string
    {
        if (!$this->placed_at || $this->status === 'delivered') {
            return null;
        }

        $shippingMethod = $this->shippingMethod;
        // Use average of min and max days, or default to 5 days
        $estimatedDays = $shippingMethod 
            ? (int) round(($shippingMethod->estimated_min_days + $shippingMethod->estimated_max_days) / 2)
            : 5;

        $estimatedDate = $this->placed_at->copy()->addDays($estimatedDays);

        return $estimatedDate->toIso8601String();
    }
}

