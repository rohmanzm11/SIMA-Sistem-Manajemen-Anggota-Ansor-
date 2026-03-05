<?php

namespace App\Filament\Resources\RantingResource\Pages;

use App\Filament\Resources\RantingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRantings extends ManageRecords
{
    protected static string $resource = RantingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
