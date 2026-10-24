<?php

namespace App\Filament\Resources\KtaResource\Pages;

use App\Filament\Resources\KtaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKtas extends ListRecords
{
    protected static string $resource = KtaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
