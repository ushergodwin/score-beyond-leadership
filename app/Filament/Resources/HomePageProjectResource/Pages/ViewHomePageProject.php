<?php

namespace App\Filament\Resources\HomePageProjectResource\Pages;

use App\Filament\Resources\HomePageProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHomePageProject extends ViewRecord
{
    protected static string $resource = HomePageProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

