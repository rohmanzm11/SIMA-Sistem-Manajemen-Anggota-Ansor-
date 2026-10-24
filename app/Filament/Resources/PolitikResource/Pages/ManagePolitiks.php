<?php

namespace App\Filament\Resources\PolitikResource\Pages;

use App\Filament\Resources\PolitikResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePolitiks extends ManageRecords
{
    protected static string $resource = PolitikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
