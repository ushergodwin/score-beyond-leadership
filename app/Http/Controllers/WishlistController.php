<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        $wishlistItems = $user->wishlistItems()
            ->with(['product.images', 'variant'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Dashboard/Wishlist/Index', [
            'wishlistItems' => [
                'data' => $wishlistItems->getCollection()->map(function ($item) {
                    $product = $item->product;
                    $primaryImage = $product->images()->orderBy('display_order')->first();
                    
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'sku' => $product->sku,
                            'base_price' => $product->base_price,
                            'base_currency' => $product->base_currency,
                            'inventory' => $product->inventory,
                            'image' => $primaryImage?->url,
                            'image_alt' => $primaryImage?->alt_text,
                        ],
                        'variant' => $item->variant ? [
                            'id' => $item->variant->id,
                            'name' => $item->variant->name,
                            'sku' => $item->variant->sku,
                            'price' => $item->variant->price,
                            'currency' => $item->variant->currency,
                            'inventory' => $item->variant->inventory,
                        ] : null,
                        'added_at' => $item->created_at->format('M d, Y'),
                    ];
                }),
                'meta' => [
                    'current_page' => $wishlistItems->currentPage(),
                    'last_page' => $wishlistItems->lastPage(),
                    'per_page' => $wishlistItems->perPage(),
                    'total' => $wishlistItems->total(),
                    'links' => $wishlistItems->linkCollection()->toArray(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
        ]);

        $user = $request->user();

        // Check if item already exists
        $existing = WishlistItem::where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->where('product_variant_id', $data['product_variant_id'] ?? null)
            ->first();

        if ($existing) {
            return back()->with('flash.error', 'Item is already in your wishlist');
        }

        WishlistItem::create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'] ?? null,
        ]);

        return back()->with('flash.success', 'Item added to wishlist');
    }

    public function destroy(Request $request, WishlistItem $wishlistItem)
    {
        if ($wishlistItem->user_id !== $request->user()->id) {
            abort(403);
        }

        $wishlistItem->delete();

        return back()->with('flash.success', 'Item removed from wishlist');
    }
}
