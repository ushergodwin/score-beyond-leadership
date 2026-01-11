<?php

namespace App\Filament\Resources\HomePageSuccessStoryResource\Pages;

use App\Filament\Resources\HomePageSuccessStoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomePageSuccessStories extends ListRecords
{
    protected static string $resource = HomePageSuccessStoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

