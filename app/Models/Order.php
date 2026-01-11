<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'tracking_token',
        'customer_id',
        'shipping_address_id',
        'billing_address_id',
        'shipping_method_id',
        'status',
        'payment_status',
        'currency',
        'exchange_rate',
        'subtotal',
        'discount_total',
        'shipping_total',
        'tax_total',
        'grand_total',
        'pesapal_order_tracking_id',
        'ip_address',
        'metadata',
        'customer_note',
        'placed_at',
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'subtotal' => 'float',
        'discount_total' => 'float',
        'shipping_total' => 'float',
        'tax_total' => 'float',
        'grand_total' => 'float',
        'metadata' => 'array',
        'placed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            if ($order->order_number === null || $order->order_number === '') {
                $order->order_number = self::generateOrderNumber();
            }
            
            if ($order->tracking_token === null || $order->tracking_token === '') {
                $order->tracking_token = self::generateTrackingToken();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'SB';
        $timestamp = now()->format('ymdHis');
        $random = random_int(100, 999);

        return "{$prefix}-{$timestamp}-{$random}";
    }

    /**
     * Generate a secure, non-guessable tracking token.
     */
    public static function generateTrackingToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32)); // 64 character hex string
        } while (self::where('tracking_token', $token)->exists());

        return $token;
    }

    /**
     * Get the public tracking URL for this order.
     */
    public function getTrackingUrlAttribute(): string
    {
        return route('orders.track', [
            'order_number' => $this->order_number,
            'token' => $this->tracking_token,
        ]);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentTransactions(): MorphMany
    {
        return $this->morphMany(PaymentTransaction::class, 'payable');
    }
}
