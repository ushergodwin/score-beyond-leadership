<?php

namespace App\Http\Controllers;

use App\Models\HomePageSuccessStory;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = config('app.url');
        $routes = $this->getPublicRoutes();
        $products = $this->getProducts();
        $successStories = $this->getSuccessStories();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Add static routes
        foreach ($routes as $route) {
            $xml .= $this->urlElement($baseUrl . $route['url'], $route['priority'], $route['changefreq']);
        }

        // Add products
        foreach ($products as $product) {
            $xml .= $this->urlElement(
                $baseUrl . route('shop.show', $product->slug, false),
                '0.8',
                'weekly'
            );
        }

        // Add success stories
        foreach ($successStories as $story) {
            $xml .= $this->urlElement(
                $baseUrl . route('success-stories.show', $story->id, false),
                '0.7',
                'monthly'
            );
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function getPublicRoutes(): array
    {
        return [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/shop', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/volunteer', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/volunteer/apply', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/academy', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/academy/apply', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/donate', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/gallery', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/privacy-policy', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['url' => '/terms-of-service', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['url' => '/refund-policy', 'priority' => '0.5', 'changefreq' => 'yearly'],
        ];
    }

    private function getProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    private function getSuccessStories(): \Illuminate\Database\Eloquent\Collection
    {
        return HomePageSuccessStory::where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }

    private function urlElement(string $url, string $priority, string $changefreq): string
    {
        $lastmod = date('Y-m-d');
        return "  <url>\n" .
               "    <loc>" . htmlspecialchars($url, ENT_XML1) . "</loc>\n" .
               "    <lastmod>{$lastmod}</lastmod>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>\n";
    }
}

