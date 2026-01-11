<?php

namespace App\Filament\Resources\HomePageFocusAreaResource\Pages;

use App\Filament\Resources\HomePageFocusAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomePageFocusArea extends EditRecord
{
    protected static string $resource = HomePageFocusAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure image_path is properly formatted for Filament FileUpload
        if (isset($data['image_path']) && !empty($data['image_path'])) {
            // If it's a full URL, extract just the path relative to storage
            if (str_starts_with($data['image_path'], 'http')) {
                // Extract path from URL (e.g., storage/images/home/file.jpg)
                $data['image_path'] = str_replace(asset('storage/'), '', $data['image_path']);
            }
        }
        
        return $data;
    }
}

