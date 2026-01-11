<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_section_id',
        'path',
        'caption',
        'alt_text',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    protected $appends = [
        'url',
    ];

    /**
     * Get the full URL for the image.
     */
    public function getUrlAttribute(): string
    {
        // If path starts with http, return the path as-is
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        // Otherwise, generate URL from storage
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Get the gallery section that owns this image.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(GallerySection::class, 'gallery_section_id');
    }
}
