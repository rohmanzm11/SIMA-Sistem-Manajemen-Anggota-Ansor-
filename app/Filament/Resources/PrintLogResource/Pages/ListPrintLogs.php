<?php

namespace App\Filament\Resources\PrintLogResource\Pages;

use App\Filament\Resources\PrintLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrintLogs extends ListRecords
{
    protected static string $resource = PrintLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
