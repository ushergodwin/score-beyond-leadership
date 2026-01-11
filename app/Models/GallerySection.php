<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GallerySection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the images for this gallery section.
     */
    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('display_order');
    }

    /**
     * Scope to get only active sections with images.
     */
    public function scopeActiveWithImages($query)
    {
        return $query->where('is_active', true)
            ->whereHas('images')
            ->orderBy('display_order');
    }
}
