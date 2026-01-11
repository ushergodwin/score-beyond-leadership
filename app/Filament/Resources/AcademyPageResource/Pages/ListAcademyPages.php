<?php

namespace App\Filament\Resources\AcademyPageResource\Pages;

use App\Filament\Resources\AcademyPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademyPages extends ListRecords
{
    protected static string $resource = AcademyPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

