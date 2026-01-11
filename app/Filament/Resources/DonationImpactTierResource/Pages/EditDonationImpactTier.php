<?php

namespace App\Filament\Resources\DonationImpactTierResource\Pages;

use App\Filament\Resources\DonationImpactTierResource;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\EditRecord;

class EditDonationImpactTier extends EditRecord
{
    protected static string $resource = DonationImpactTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\ViewAction::make(),
            FilamentActions\DeleteAction::make(),
        ];
    }
}

