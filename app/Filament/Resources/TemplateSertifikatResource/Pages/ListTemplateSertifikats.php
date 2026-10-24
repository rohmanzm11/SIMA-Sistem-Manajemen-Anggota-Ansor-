<?php

namespace App\Filament\Resources\TemplateSertifikatResource\Pages;

use App\Filament\Resources\TemplateSertifikatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateSertifikats extends ListRecords
{
    protected static string $resource = TemplateSertifikatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
