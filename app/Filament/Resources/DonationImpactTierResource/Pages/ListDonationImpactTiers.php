<?php

namespace App\Filament\Resources\DonationImpactTierResource\Pages;

use App\Filament\Resources\DonationImpactTierResource;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\ListRecords;

class ListDonationImpactTiers extends ListRecords
{
    protected static string $resource = DonationImpactTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\CreateAction::make(),
        ];
    }
}

