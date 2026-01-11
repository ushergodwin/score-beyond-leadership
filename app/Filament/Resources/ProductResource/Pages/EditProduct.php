<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

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
            ProductImage::where('product_id', $this->record->id)
                ->where('path', $imagePath)
                ->delete();
        }
        
        // Find new images to add (in new but not in current)
        $imagesToAdd = array_diff($newImages, $currentImages);
        
        // Add new images
        foreach ($imagesToAdd as $imagePath) {
            // Find the position in the new array
            $newIndex = array_search($imagePath, $newImages);
            ProductImage::create([
                'product_id' => $this->record->id,
                'path' => $imagePath,
                'is_primary' => $newIndex === 0,
                'display_order' => $newIndex,
                'alt_text' => $this->record->name . ' - Image ' . ($newIndex + 1),
            ]);
        }
        
        // Update display_order and is_primary for all images based on new order
        foreach ($newImages as $index => $imagePath) {
            ProductImage::where('product_id', $this->record->id)
                ->where('path', $imagePath)
                ->update([
                    'display_order' => $index,
                    'is_primary' => $index === 0,
                ]);
        }
        
        // Remove images from data as it's not a direct field
        unset($data['images']);

        return $data;
    }
}

