<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_category_id',
        'name',
        'slug',
        'sku',
        'status',
        'subtitle',
        'description',
        'care_instructions',
        'materials',
        'artisan_story',
        'base_price',
        'base_currency',
        'is_limited_edition',
        'limited_badge_label',
        'inventory',
        'meta',
        'published_at',
        'is_featured',
    ];

    protected $casts = [
        'base_price' => 'float',
        'is_limited_edition' => 'boolean',
        'inventory' => 'integer',
        'meta' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('display_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('name');
    }
}
