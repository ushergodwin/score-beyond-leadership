<?php

namespace App\Filament\Resources\VolunteerRoleResource\Pages;

use App\Filament\Resources\VolunteerRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVolunteerRoles extends ManageRecords
{
    protected static string $resource = VolunteerRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

