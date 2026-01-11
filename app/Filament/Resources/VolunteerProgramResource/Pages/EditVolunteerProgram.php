<?php

namespace App\Filament\Resources\VolunteerProgramResource\Pages;

use App\Filament\Resources\VolunteerProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerProgram extends EditRecord
{
    protected static string $resource = VolunteerProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

