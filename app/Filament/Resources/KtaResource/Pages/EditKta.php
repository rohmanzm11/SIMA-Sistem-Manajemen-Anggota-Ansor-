<?php

namespace App\Filament\Resources\KtaResource\Pages;

use App\Filament\Resources\KtaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKta extends EditRecord
{
    protected static string $resource = KtaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
