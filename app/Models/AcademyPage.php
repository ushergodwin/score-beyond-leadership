<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyPage extends Model
{
    protected $fillable = [
        'slug',
        'hero_title',
        'hero_subtitle',
        'offers_heading',
        'offers_description',
        'offerings',
        'location',
        'why_matters_heading',
        'why_matters_description',
        'join_heading',
        'join_description',
        'join_cta_text',
        'is_active',
    ];

    protected $casts = [
        'offerings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the single Academy page instance
     */
    public static function getAcademyPage(): ?self
    {
        return static::where('slug', 'academy')
            ->where('is_active', true)
            ->first();
    }
}
