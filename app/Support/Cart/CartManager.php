<?php

declare(strict_types=1);

namespace App\Support\Cart;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\Currency\PriceDisplay;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class CartManager
{
    private const SESSION_KEY = 'shop.cart';

    public function __construct(private readonly Session $session)
    {
    }

    public function items(): Collection
    {
        /** @var array<int, array<string, mixed>> $items */
        $items = $this->session->get(self::SESSION_KEY . '.items', []);

        return collect($items);
    }

    public function add(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        if ($quantity < 1) {
            throw new RuntimeException('Quantity must be at least 1.');
        }

        // If product has variants but none provided, throw error
        if ($product->variants()->count() > 0 && $variant === null) {
            throw new RuntimeException('Variant is required for this product.');
        }

        // If product has no variants, use null variant_id
        $variantId = $variant?->id;

        $items = $this->items()->map(function (array $item) use ($variantId, $quantity) {
            // Match by variant_id (or null if no variant)
            $itemVariantId = $item['variant_id'] ?? null;
            if ($itemVariantId === $variantId) {
                $item['quantity'] = min(99, $item['quantity'] + $quantity);
            }

            return $item;
        });

        $exists = $items->firstWhere('variant_id', $variantId);
        if ($exists === null) {
            $items->push($this->formatLine($product, $variant, $quantity));
        }

        $this->storeItems($items);
    }

    public function update(string $lineId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($lineId);

            return;
        }

        $updated = $this->items()->map(function (array $item) use ($lineId, $quantity) {
            if ($item['id'] === $lineId) {
                $item['quantity'] = $quantity;
            }

            return $item;
        });

        $this->storeItems($updated);
    }

    public function remove(string $lineId): void
    {
        $filtered = $this->items()->reject(fn (array $item) => $item['id'] === $lineId);

        $this->storeItems($filtered);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    public function totals(): array
    {
        $subtotal = $this->items()->reduce(
            fn (float $carry, array $item) => $carry + ($item['unit_price'] * $item['quantity']),
            0.0
        );

        $display = PriceDisplay::forUgx($subtotal);

        return [
            'currency' => 'UGX',
            'subtotal' => $display,
            'shipping' => PriceDisplay::forUgx(0),
            'grand_total' => $display,
        ];
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /**
     * Restore cart items from stored data (e.g., localStorage).
     * Validates products/variants still exist and updates prices/stock.
     *
     * @param  array<int, array<string, mixed>>  $storedItems
     */
    public function restore(array $storedItems): void
    {
        $validItems = collect($storedItems)->filter(function (array $item) {
            $product = Product::query()
                ->where('id', $item['product_id'])
                ->where('status', 'published')
                ->first();

            if ($product === null) {
                return false;
            }

            // If variant_id is provided, validate it exists
            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::query()
                    ->where('id', $item['variant_id'])
                    ->where('product_id', $product->id)
                    ->first();

                return $variant !== null;
            }

            // If no variant_id, product should have no variants
            return $product->variants()->count() === 0;
        })->map(function (array $item) {
            $product = Product::query()->findOrFail($item['product_id']);
            $variant = null;
            
            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::query()
                    ->where('id', $item['variant_id'])
                    ->where('product_id', $product->id)
                    ->firstOrFail();
            }

            // Update with current data (prices, stock, etc.)
            return [
                'id' => $item['id'] ?? (string) Str::uuid(),
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'name' => $product->name,
                'variant_name' => $variant?->name ?? 'Standard',
                'quantity' => min(
                    (int) ($item['quantity'] ?? 1),
                    99,
                    $variant?->inventory ?? $product->inventory
                ),
                'unit_price' => $variant?->price ?? $product->base_price,
                'currency' => $product->base_currency,
                'display_price' => PriceDisplay::forUgx($variant?->price ?? $product->base_price),
                'image' => $product->images()->orderBy('display_order')->first()?->url,
                'slug' => $product->slug,
                'sku' => $variant?->sku ?? $product->sku,
                'stock' => $variant?->inventory ?? $product->inventory,
            ];
        });

        if ($validItems->isNotEmpty()) {
            $this->storeItems($validItems);
        }
    }

    private function storeItems(Collection $items): void
    {
        $this->session->put(self::SESSION_KEY, [
            'items' => $items->values()->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatLine(Product $product, ?ProductVariant $variant, int $quantity): array
    {
        return [
            'id' => (string) Str::uuid(),
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'name' => $product->name,
            'variant_name' => $variant?->name ?? 'Standard',
            'quantity' => $quantity,
            'unit_price' => $variant?->price ?? $product->base_price,
            'currency' => $product->base_currency,
            'display_price' => PriceDisplay::forUgx($variant?->price ?? $product->base_price),
            'image' => $product->images()->orderBy('display_order')->first()?->url,
            'slug' => $product->slug,
            'sku' => $variant?->sku ?? $product->sku,
            'stock' => $variant?->inventory ?? $product->inventory,
        ];
    }
}


