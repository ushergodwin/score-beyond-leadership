<?php

namespace App\Filament\Resources\HomePageFocusAreaResource\Pages;

use App\Filament\Resources\HomePageFocusAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomePageFocusAreas extends ListRecords
{
    protected static string $resource = HomePageFocusAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

