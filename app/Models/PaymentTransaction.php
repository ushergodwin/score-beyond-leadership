<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $provider
 * @property string|null $provider_reference
 * @property string $status
 * @property string $currency
 * @property float $amount
 * @property array|null $payload
 */
class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'provider_reference',
        'status',
        'currency',
        'amount',
        'pesapal_tracking_id',
        'pesapal_merchant_reference',
        'error_message',
        'payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}


