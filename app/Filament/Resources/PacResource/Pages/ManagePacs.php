<?php

namespace App\Filament\Resources\PacResource\Pages;

use App\Filament\Resources\PacResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePacs extends ManageRecords
{
    protected static string $resource = PacResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
