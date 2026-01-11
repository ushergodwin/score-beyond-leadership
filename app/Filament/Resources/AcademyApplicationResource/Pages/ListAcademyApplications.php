<?php

namespace App\Filament\Resources\AcademyApplicationResource\Pages;

use App\Filament\Resources\AcademyApplicationResource;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\ListRecords;

class ListAcademyApplications extends ListRecords
{
    protected static string $resource = AcademyApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Academy applications are created from the client-facing form
        ];
    }
}

