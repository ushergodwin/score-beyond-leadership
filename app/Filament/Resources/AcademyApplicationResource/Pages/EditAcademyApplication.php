<?php

namespace App\Filament\Resources\AcademyApplicationResource\Pages;

use App\Filament\Resources\AcademyApplicationResource;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\EditRecord;

class EditAcademyApplication extends EditRecord
{
    protected static string $resource = AcademyApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\ViewAction::make(),
            FilamentActions\DeleteAction::make(),
        ];
    }
}

