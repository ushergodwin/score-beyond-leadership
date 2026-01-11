<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerProgram extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'badge',
        'summary',
        'description',
        'benefits',
        'logistics',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'benefits' => 'array',
        'logistics' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];
}
