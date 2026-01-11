<?php

namespace App\Filament\Resources\VolunteerProgramResource\Pages;

use App\Filament\Resources\VolunteerProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVolunteerPrograms extends ListRecords
{
    protected static string $resource = VolunteerProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

