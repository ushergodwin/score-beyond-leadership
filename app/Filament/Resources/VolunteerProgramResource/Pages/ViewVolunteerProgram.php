<?php

namespace App\Filament\Resources\VolunteerProgramResource\Pages;

use App\Filament\Resources\VolunteerProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVolunteerProgram extends ViewRecord
{
    protected static string $resource = VolunteerProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

