<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'donation_number',
        'customer_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'organization',
        'address',
        'amount',
        'currency',
        'exchange_rate',
        'is_recurring',
        'frequency',
        'impact_tier',
        'payment_status',
        'pesapal_tracking_id',
        'tax_receipt_requested',
        'tax_receipt_path',
        'consent_to_contact',
        'communications_opt_in',
        'message',
        'meta',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'is_recurring' => 'boolean',
        'tax_receipt_requested' => 'boolean',
        'consent_to_contact' => 'boolean',
        'communications_opt_in' => 'boolean',
        'meta' => 'array',
        'paid_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentTransactions(): MorphMany
    {
        return $this->morphMany(PaymentTransaction::class, 'payable');
    }
}

