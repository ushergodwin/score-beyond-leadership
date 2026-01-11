<?php

namespace App\Filament\Resources\GallerySectionResource\Pages;

use App\Filament\Resources\GallerySectionResource;
use App\Models\GalleryImage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGallerySection extends CreateRecord
{
    protected static string $resource = GallerySectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extract images from data
        $images = $data['images'] ?? [];
        unset($data['images']);

        // Create the gallery section
        $section = static::getModel()::create($data);

        // Save gallery images
        if (!empty($images)) {
            foreach ($images as $index => $imagePath) {
                GalleryImage::create([
                    'gallery_section_id' => $section->id,
                    'path' => $imagePath,
                    'display_order' => $index,
                    'alt_text' => $section->title . ' - Image ' . ($index + 1),
                ]);
            }
        }

        return $section;
    }
}

