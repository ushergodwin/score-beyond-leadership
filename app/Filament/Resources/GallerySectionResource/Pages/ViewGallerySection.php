<?php

namespace App\Filament\Resources\GallerySectionResource\Pages;

use App\Filament\Resources\GallerySectionResource;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\ViewRecord;

class ViewGallerySection extends ViewRecord
{
    protected static string $resource = GallerySectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\EditAction::make(),
        ];
    }
}

