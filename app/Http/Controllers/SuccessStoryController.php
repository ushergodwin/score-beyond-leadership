<?php

namespace App\Http\Controllers;

use App\Models\HomePageSuccessStory;
use Inertia\Inertia;
use Inertia\Response;

class SuccessStoryController extends Controller
{
    public function show(int $id): Response
    {
        $story = HomePageSuccessStory::where('is_active', true)
            ->findOrFail($id);

        // Get related stories (exclude current one)
        $relatedStories = HomePageSuccessStory::where('is_active', true)
            ->where('id', '!=', $id)
            ->orderBy('display_order')
            ->limit(3)
            ->get()
            ->map(function ($relatedStory) {
                $shortDescription = mb_strlen($relatedStory->description) > 100 
                    ? mb_substr($relatedStory->description, 0, 100) . '...' 
                    : $relatedStory->description;
                
                return [
                    'id' => $relatedStory->id,
                    'title' => $relatedStory->title,
                    'description' => $shortDescription,
                    'quote' => $relatedStory->quote,
                    'image_url' => $relatedStory->image_url,
                    'image_alt' => $relatedStory->image_alt ?? $relatedStory->title,
                ];
            })
            ->toArray();

        return Inertia::render('SuccessStories/Show', [
            'story' => [
                'id' => $story->id,
                'title' => $story->title,
                'description' => $story->description,
                'quote' => $story->quote,
                'image_url' => $story->image_url,
                'image_alt' => $story->image_alt ?? $story->title,
            ],
            'relatedStories' => $relatedStories,
        ]);
    }
}
