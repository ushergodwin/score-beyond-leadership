<?php

namespace App\Http\Controllers;

use App\Models\HomePageFocusArea;
use App\Models\HomePageProject;
use App\Models\HomePageSuccessStory;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $projects = HomePageProject::where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->description,
                    'location' => $project->location,
                    'image_url' => $project->image_url,
                    'image_alt' => $project->image_alt ?? $project->title,
                ];
            })
            ->toArray();

        $focusAreas = HomePageFocusArea::where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->map(function ($area) {
                return [
                    'id' => $area->id,
                    'title' => $area->title,
                    'description' => $area->description,
                    'image_url' => $area->image_url,
                    'image_alt' => $area->image_alt ?? $area->title,
                ];
            })
            ->toArray();

        $successStories = HomePageSuccessStory::where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->map(function ($story) {
                // Truncate description to 150 characters for home page preview
                $shortDescription = mb_strlen($story->description) > 150 
                    ? mb_substr($story->description, 0, 150) . '...' 
                    : $story->description;
                
                return [
                    'id' => $story->id,
                    'title' => $story->title,
                    'description' => $shortDescription,
                    'full_description' => $story->description, // Keep full for detail page
                    'quote' => $story->quote,
                    'image_url' => $story->image_url,
                    'image_alt' => $story->image_alt ?? $story->title,
                    'has_more' => mb_strlen($story->description) > 150,
                ];
            })
            ->toArray();

        return Inertia::render('Home/Index', [
            'projects' => $projects,
            'focusAreas' => $focusAreas,
            'successStories' => $successStories,
        ]);
    }
}
