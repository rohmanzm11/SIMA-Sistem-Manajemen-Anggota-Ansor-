<?php

namespace App\Filament\Resources\TemplateSertifikatResource\Pages;

use App\Filament\Resources\TemplateSertifikatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateSertifikat extends EditRecord
{
    protected static string $resource = TemplateSertifikatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
