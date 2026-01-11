<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\Cart\CartManager;
use App\Support\Currency\PriceDisplay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private readonly CartManager $cart)
    {
    }

    public function index(): Response
    {
        $items = $this->cart->items()->map(function (array $item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'variant_name' => $item['variant_name'],
                'quantity' => $item['quantity'],
                'display_price' => $item['display_price'],
                'unit_price' => $item['unit_price'],
                'currency' => $item['currency'],
                'image' => $item['image'],
                'slug' => $item['slug'],
                'sku' => $item['sku'],
                'stock' => $item['stock'] ?? 1,
                'product_id' => $item['product_id'] ?? null,
            ];
        })->values();

        // Get "People who bought this also bought" products
        $cartProductIds = $items->pluck('product_id')->filter()->unique()->toArray();
        $alsoBoughtProducts = collect();

        if (!empty($cartProductIds)) {
            // Find products that were bought together with cart products
            $alsoBoughtProductIds = OrderItem::query()
                ->whereIn('order_items.product_id', $cartProductIds)
                ->whereHas('order', function ($query) {
                    $query->where('payment_status', 'paid');
                })
                ->join('order_items as other_items', function ($join) {
                    $join->on('order_items.order_id', '=', 'other_items.order_id')
                        ->whereColumn('order_items.product_id', '!=', 'other_items.product_id');
                })
                ->whereNotIn('other_items.product_id', $cartProductIds)
                ->select('other_items.product_id')
                ->selectRaw('COUNT(*) as purchase_count')
                ->groupBy('other_items.product_id')
                ->orderByDesc('purchase_count')
                ->limit(10)
                ->pluck('other_items.product_id')
                ->toArray();

            if (!empty($alsoBoughtProductIds)) {
                $alsoBoughtProducts = Product::query()
                    ->with(['images' => fn ($query) => $query->orderBy('display_order')])
                    ->whereIn('id', $alsoBoughtProductIds)
                    ->where('status', 'published')
                    ->get()
                    ->map(fn (Product $product) => $this->transformProductCard($product))
                    ->take(10);
            }
        }

        // If no "also bought" products, show featured products instead
        if ($alsoBoughtProducts->isEmpty()) {
            $alsoBoughtProducts = Product::query()
                ->with(['images' => fn ($query) => $query->orderBy('display_order')])
                ->where('status', 'published')
                ->where('is_featured', true)
                ->whereNotIn('id', $cartProductIds)
                ->limit(10)
                ->get()
                ->map(fn (Product $product) => $this->transformProductCard($product));
        }

        return Inertia::render('Shop/Cart', [
            'cart' => [
                'items' => $items,
                'totals' => $this->cart->totals(),
            ],
            'alsoBoughtProducts' => $alsoBoughtProducts->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $product = Product::query()
            ->where('id', $data['product_id'])
            ->where('status', 'published')
            ->firstOrFail();

        // If variant_id is provided, validate it belongs to the product
        $variant = null;
        if (!empty($data['variant_id'])) {
            $variant = ProductVariant::query()
                ->where('id', $data['variant_id'])
                ->where('product_id', $product->id)
                ->firstOrFail();
        } else {
            // Check if product has variants - if it does, variant_id is required
            if ($product->variants()->count() > 0) {
                return back()->withErrors(['variant_id' => 'Please select a variant for this product.']);
            }
        }

        $this->cart->add($product, $variant, (int) $data['quantity']);

        $message = $variant 
            ? "{$variant->name} added to cart."
            : "{$product->name} added to cart.";

        return back()->with('flash.cart_message', $message);
    }

    public function update(Request $request, string $lineId): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $this->cart->update($lineId, (int) $data['quantity']);

        return back()->with('flash.cart_message', 'Cart updated.');
    }

    public function destroy(string $lineId): RedirectResponse
    {
        $this->cart->remove($lineId);

        return back()->with('flash.cart_message', 'Item removed.');
    }

    /**
     * Restore cart from localStorage backup.
     */
    public function restore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variant_id' => ['nullable', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.id' => ['nullable', 'string'],
        ]);

        // Only restore if current cart is empty
        if ($this->cart->count() === 0) {
            $this->cart->restore($data['items']);
        }

        return back();
    }

    private function transformProductCard(Product $product): array
    {
        $primaryImage = $product->images->first();
        $price = PriceDisplay::forUgx($product->base_price);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'subtitle' => $product->subtitle,
            'category' => $product->category?->name ?? '',
            'price' => $price,
            'is_limited_edition' => $product->is_limited_edition,
            'limited_badge_label' => $product->limited_badge_label,
            'image' => $primaryImage?->url,
            'image_alt' => $primaryImage?->alt_text,
            'stock_status' => $product->inventory > 10 ? 'In stock' : ($product->inventory > 0 ? 'Limited stock' : 'Sold out'),
        ];
    }
}
