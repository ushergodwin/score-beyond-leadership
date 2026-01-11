<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'region',
        'carrier',
        'base_rate',
        'currency',
        'estimated_min_days',
        'estimated_max_days',
        'is_pickup',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'base_rate' => 'float',
        'estimated_min_days' => 'integer',
        'estimated_max_days' => 'integer',
        'is_pickup' => 'boolean',
        'is_active' => 'boolean',
        'meta' => 'array',
    ];
}
