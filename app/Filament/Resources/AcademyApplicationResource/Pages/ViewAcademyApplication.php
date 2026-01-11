<?php

namespace App\Filament\Resources\AcademyApplicationResource\Pages;

use App\Filament\Resources\AcademyApplicationResource;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\ViewRecord;

class ViewAcademyApplication extends ViewRecord
{
    protected static string $resource = AcademyApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\EditAction::make(),
        ];
    }
}

