<?php

namespace App\Filament\Resources\GallerySectionResource\Pages;

use App\Filament\Resources\GallerySectionResource;
use App\Models\GalleryImage;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditGallerySection extends EditRecord
{
    protected static string $resource = GallerySectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\ViewAction::make(),
            FilamentActions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing images into form data
        $data['images'] = $this->record->images()->orderBy('display_order')->pluck('path')->toArray();
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Get current images
        $currentImages = $this->record->images()->pluck('path')->toArray();
        
        // Get new images from form
        $newImages = $data['images'] ?? [];
        
        // Find images to delete (in current but not in new)
        $imagesToDelete = array_diff($currentImages, $newImages);
        
        // Delete removed images from storage and database
        foreach ($imagesToDelete as $imagePath) {
            // Delete from storage
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            // Delete from database
            GalleryImage::where('gallery_section_id', $this->record->id)
                ->where('path', $imagePath)
                ->delete();
        }
        
        // Find new images to add (in new but not in current)
        $imagesToAdd = array_diff($newImages, $currentImages);
        
        // Add new images
        foreach ($imagesToAdd as $imagePath) {
            // Find the position in the new array
            $newIndex = array_search($imagePath, $newImages);
            GalleryImage::create([
                'gallery_section_id' => $this->record->id,
                'path' => $imagePath,
                'display_order' => $newIndex,
                'alt_text' => $this->record->title . ' - Image ' . ($newIndex + 1),
            ]);
        }
        
        // Update display_order for all images based on new order
        foreach ($newImages as $index => $imagePath) {
            GalleryImage::where('gallery_section_id', $this->record->id)
                ->where('path', $imagePath)
                ->update([
                    'display_order' => $index,
                ]);
        }
        
        // Remove images from data as it's not a direct field
        unset($data['images']);

        return $data;
    }
}

