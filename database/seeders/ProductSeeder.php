<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Apparel',
                'slug' => 'apparel',
                'description' => 'Performance-ready apparel for leagues, trainings, and community events.',
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Hydration, bags, and everyday essentials built for active lifestyles.',
            ],
            [
                'name' => 'Handmade Crafts',
                'slug' => 'handmade-crafts',
                'description' => 'Artisan-made goods from Score Beyond partner communities.',
            ],
        ];

        $categoryMap = collect($categories)->mapWithKeys(function (array $category) {
            $record = ProductCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );

            return [$category['slug'] => $record->id];
        });

        $products = [
            [
                'name' => 'SB Heritage Performance Tee',
                'category_slug' => 'apparel',
                'subtitle' => 'Ultra-soft athletic tee in Score Beyond palette.',
                'price' => 65000,
                'materials' => '60% Organic Cotton, 40% Recycled Polyester',
                'care' => 'Machine wash cold, tumble dry low, do not bleach.',
                'story' => 'Designed with players from the Girls\' League to keep you comfortable on and off the court.',
                'images' => [
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab',
                    'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb',
                ],
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                'sku_prefix' => 'SB-TEE',
            ],
            [
                'name' => 'Empower Stainless Bottle',
                'category_slug' => 'accessories',
                'subtitle' => 'Insulated bottle with “Transforming Lives” motif.',
                'price' => 85000,
                'materials' => 'Double-wall stainless steel with powder coat finish.',
                'care' => 'Hand wash only. Not microwave safe.',
                'story' => 'Every bottle supports clean water efforts for Score Beyond partner schools.',
                'images' => [
                    'https://images.unsplash.com/photo-1489515217757-5fd1be406fef',
                ],
                'sizes' => ['750ml'],
                'sku_prefix' => 'SB-BOT',
            ],
            [
                'name' => 'Adjumani Artisan Tote',
                'category_slug' => 'handmade-crafts',
                'subtitle' => 'Handwoven raffia tote with leather handles.',
                'price' => 120000,
                'materials' => 'Locally sourced raffia, vegetable-tanned leather.',
                'care' => 'Spot clean with damp cloth, air dry in shade.',
                'story' => 'Crafted by women-led collectives powering livelihood programs in Adjumani.',
                'images' => [
                    'https://images.unsplash.com/photo-1521572267360-ee0c2909d518',
                ],
                'sizes' => ['Standard'],
                'sku_prefix' => 'SB-TOTE',
            ],
        ];

        foreach ($products as $index => $productData) {
            $categoryId = $categoryMap->get($productData['category_slug']);

            if ($categoryId === null) {
                continue;
            }

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'product_category_id' => $categoryId,
                    'name' => $productData['name'],
                    'sku' => $productData['sku_prefix'] . sprintf('%03d', $index + 1),
                    'status' => 'published',
                    'subtitle' => $productData['subtitle'],
                    'description' => $productData['story'],
                    'care_instructions' => $productData['care'],
                    'materials' => $productData['materials'],
                    'artisan_story' => $productData['story'],
                    'base_price' => $productData['price'],
                    'base_currency' => 'UGX',
                    'is_limited_edition' => $productData['category_slug'] === 'handmade-crafts',
                    'limited_badge_label' => $productData['category_slug'] === 'handmade-crafts' ? 'Artisan Limited Release' : null,
                    'inventory' => 50,
                    'meta' => ['theme' => 'shopify-inspired'],
                    'published_at' => now()->subDays($index),
                    'is_featured' => $index === 0,
                ]
            );

            $product->images()->delete();
            foreach ($productData['images'] as $order => $imageUrl) {
                $product->images()->create([
                    'path' => $imageUrl,
                    'caption' => Arr::get($productData, 'subtitle'),
                    'alt_text' => "{$productData['name']} product image",
                    'is_primary' => $order === 0,
                    'display_order' => $order,
                ]);
            }

            $product->variants()->delete();
            foreach ($productData['sizes'] as $order => $size) {
                $product->variants()->create([
                    'name' => $size,
                    'sku' => $productData['sku_prefix'] . '-' . Str::slug((string) $size, '-'),
                    'price' => $productData['price'],
                    'currency' => 'UGX',
                    'inventory' => 10 + ($order * 5),
                    'attributes' => ['size' => $size],
                    'is_default' => $order === 0,
                ]);
            }
        }
    }
}
