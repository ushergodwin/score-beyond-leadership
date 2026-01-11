<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationImpactTier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'label',
        'amount',
        'currency',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'float',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];
}

