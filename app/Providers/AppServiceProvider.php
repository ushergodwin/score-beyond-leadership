<?php

namespace App\Providers;

use App\Support\Cart\CartManager;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Inertia::share('appName', config('app.name', 'Score Beyond Leadership'));
        Inertia::share('cart', function () {
            /** @var CartManager $cart */
            $cart = app(CartManager::class);

            $items = $cart->items()->map(function (array $item) {
                return [
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'name' => $item['name'],
                    'variant_name' => $item['variant_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'currency' => $item['currency'],
                    'display_price' => $item['display_price'],
                    'image' => $item['image'],
                    'slug' => $item['slug'],
                    'sku' => $item['sku'],
                    'stock' => $item['stock'] ?? 1,
                ];
            })->values();

            return [
                'count' => $cart->count(),
                'items' => $items,
            ];
        });
    }
}
