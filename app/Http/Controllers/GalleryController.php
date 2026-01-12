<?php

namespace App\Http\Controllers;

use App\Models\GallerySection;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    public function index(): Response
    {
        $sections = GallerySection::activeWithImages()
            ->with(['images' => fn($query) => $query->orderBy('display_order')])
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'description' => $section->description,
                    'images' => $section->images->map(fn($image) => [
                        'id' => $image->id,
                        'path' => $image->url, // Use the URL accessor instead of raw path
                        'caption' => $image->caption,
                        'alt_text' => $image->alt_text ?? $section->title,
                    ]),
                ];
            });

        return Inertia::render('Gallery/Index', [
            'sections' => $sections,
            'title' => 'Gallery',
        ]);
    }

    public function projects(): Response
    {
        $sections = GallerySection::activeWithImages()
            ->with(['images' => fn($query) => $query->orderBy('display_order')])
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'description' => $section->description,
                    'images' => $section->images->map(fn($image) => [
                        'id' => $image->id,
                        'path' => $image->url, // Use the URL accessor instead of raw path
                        'caption' => $image->caption,
                        'alt_text' => $image->alt_text ?? $section->title,
                    ]),
                ];
            });

        return Inertia::render('Gallery/Index', [
            'sections' => $sections,
            'title' => 'Projects',
        ]);
    }
}
