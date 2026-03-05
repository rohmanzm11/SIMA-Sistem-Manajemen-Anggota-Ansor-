<?php

namespace App\Filament\Resources\OrganisasiResource\Pages;

use App\Filament\Resources\OrganisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrganisasis extends ManageRecords
{
    protected static string $resource = OrganisasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
