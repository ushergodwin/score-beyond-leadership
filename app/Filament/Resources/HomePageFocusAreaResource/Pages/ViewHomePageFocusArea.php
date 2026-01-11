<?php

namespace App\Filament\Resources\HomePageFocusAreaResource\Pages;

use App\Filament\Resources\HomePageFocusAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHomePageFocusArea extends ViewRecord
{
    protected static string $resource = HomePageFocusAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

