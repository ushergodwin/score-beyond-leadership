<?php

namespace App\Filament\Resources\HomePageProjectResource\Pages;

use App\Filament\Resources\HomePageProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomePageProjects extends ListRecords
{
    protected static string $resource = HomePageProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

