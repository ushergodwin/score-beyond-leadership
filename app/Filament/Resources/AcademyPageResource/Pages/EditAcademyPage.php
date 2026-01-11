<?php

namespace App\Filament\Resources\AcademyPageResource\Pages;

use App\Filament\Resources\AcademyPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademyPage extends EditRecord
{
    protected static string $resource = AcademyPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

