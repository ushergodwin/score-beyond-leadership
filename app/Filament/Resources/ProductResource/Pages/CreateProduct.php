<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extract images from data
        $images = $data['images'] ?? [];
        unset($data['images']);

        // Create the product
        $product = static::getModel()::create($data);

        // Save product images
        if (!empty($images)) {
            foreach ($images as $index => $imagePath) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $imagePath,
                    'is_primary' => $index === 0,
                    'display_order' => $index,
                    'alt_text' => $product->name . ' - Image ' . ($index + 1),
                ]);
            }
        }

        return $product;
    }
}

