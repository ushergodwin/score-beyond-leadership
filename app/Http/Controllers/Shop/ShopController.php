<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Currency\PriceDisplay;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['category', 'sort', 'search']);

        // If search or category filter is applied, show filtered results
        if (($filters['search'] ?? false) || ($filters['category'] ?? false)) {
            return $this->showFilteredResults($request, $filters);
        }

        // Otherwise, show products grouped by category
        $categories = ProductCategory::query()
            ->where('is_active', true)
            ->whereHas('products', function ($query) {
                $query->where('status', 'published');
            })
            ->orderBy('display_order')
            ->get(['id', 'name', 'slug']);

        $categoriesWithProducts = $categories->map(function ($category) {
            $products = Product::query()
                ->with([
                    'images' => fn ($imageQuery) => $imageQuery->orderBy('display_order'),
                ])
                ->where('product_category_id', $category->id)
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->limit(15)
                ->get()
                ->map(fn (Product $product) => $this->transformProductCard($product));

            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'products' => $products,
            ];
        })->filter(fn ($category) => $category['products']->count() > 0);

        // Fetch featured and limited edition products for the banner
        $featuredProducts = Product::query()
            ->with([
                'images' => fn ($imageQuery) => $imageQuery->orderBy('display_order'),
            ])
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('is_featured', true)
                    ->orWhere('is_limited_edition', true);
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(10)
            ->get()
            ->map(fn (Product $product) => $this->transformProductCard($product));

        return Inertia::render('Shop/Index', [
            'filters' => $filters,
            'categories' => $categories->map(fn ($cat) => ['id' => $cat->id, 'name' => $cat->name, 'slug' => $cat->slug]),
            'categoriesWithProducts' => $categoriesWithProducts,
            'featuredProducts' => $featuredProducts,
        ]);
    }

    private function showFilteredResults(Request $request, array $filters): Response
    {
        $query = Product::query()
            ->with([
                'category:id,name,slug',
                'images' => fn ($imageQuery) => $imageQuery->orderBy('display_order'),
            ])
            ->where('status', 'published');

        if ($filters['category'] ?? false) {
            $query->whereHas('category', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('slug', $filters['category']);
            });
        }

        if ($filters['search'] ?? false) {
            $searchTerm = $filters['search'];
            $query->where(function ($searchQuery) use ($searchTerm) {
                $searchQuery
                    ->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        $sort = $filters['sort'] ?? 'newest';
        $query->when($sort === 'price_asc', fn ($q) => $q->orderBy('base_price'))
            ->when($sort === 'price_desc', fn ($q) => $q->orderByDesc('base_price'))
            ->when($sort === 'popularity', fn ($q) => $q->orderByDesc('inventory'))
            ->when(!in_array($sort, ['price_asc', 'price_desc', 'popularity'], true), fn ($q) => $q->orderByDesc('published_at'));

        $products = $query->paginate(12)->withQueryString();

        $categoryOptions = ProductCategory::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Shop/Index', [
            'filters' => $filters,
            'categories' => $categoryOptions,
            'products' => [
                'data' => $products->getCollection()->map(fn (Product $product) => $this->transformProductCard($product)),
                'meta' => Arr::except($products->toArray(), ['data']),
            ],
        ]);
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::query()
            ->where('status', 'published')
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'slug']);

        return response()->json(
            $products->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
            ])
        );
    }

    public function show(string $slug): Response
    {
        $product = Product::query()
            ->with([
                'category:id,name,slug',
                'images' => fn ($imageQuery) => $imageQuery->orderBy('display_order'),
                'variants' => fn ($variantQuery) => $variantQuery->orderBy('name'),
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $related = Product::query()
            ->with([
                'images' => fn ($imageQuery) => $imageQuery->orderBy('display_order'),
            ])
            ->where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'published')
            ->limit(4)
            ->get()
            ->map(fn (Product $relatedProduct) => $this->transformProductCard($relatedProduct));

        return Inertia::render('Shop/Show', [
            'product' => $this->transformProductDetail($product),
            'related' => $related,
        ]);
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
            'category' => $product->category?->name,
            'price' => $price,
            'is_limited_edition' => $product->is_limited_edition,
            'limited_badge_label' => $product->limited_badge_label,
            'image' => $primaryImage?->url,
            'image_alt' => $primaryImage?->alt_text,
            'stock_status' => $product->inventory > 10 ? 'In stock' : ($product->inventory > 0 ? 'Limited stock' : 'Sold out'),
        ];
    }

    private function transformProductDetail(Product $product): array
    {
        $primaryImage = $product->images->first();
        $price = PriceDisplay::forUgx($product->base_price);
        $productUrl = route('shop.show', $product->slug, absolute: true);
        $imageUrl = $primaryImage?->url ?? null;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'category' => $product->category?->name,
            'description' => $product->description,
            'subtitle' => $product->subtitle,
            'materials' => $product->materials,
            'care_instructions' => $product->care_instructions,
            'artisan_story' => $product->artisan_story,
            'price' => $price,
            'is_limited_edition' => $product->is_limited_edition,
            'limited_badge_label' => $product->limited_badge_label,
            'inventory' => $product->inventory,
            'url' => $productUrl,
            'image_url' => $imageUrl,
            'images' => $product->images->map(fn ($image) => [
                'url' => $image->url,
                'alt_text' => $image->alt_text,
            ]),
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'price' => PriceDisplay::forUgx($variant->price ?? $product->base_price),
                'attributes' => $variant->attributes,
                'is_default' => $variant->is_default,
                'inventory' => $variant->inventory,
            ]),
        ];
    }
}
