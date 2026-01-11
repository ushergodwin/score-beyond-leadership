<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'currency',
        'inventory',
        'attributes',
        'is_default',
    ];

    protected $casts = [
        'price' => 'float',
        'inventory' => 'integer',
        'attributes' => 'array',
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProductVariant $variant) {
            // If this variant is being set as default, unset other defaults for the same product
            if ($variant->is_default) {
                static::where('product_id', $variant->product_id)
                    ->where('id', '!=', $variant->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
