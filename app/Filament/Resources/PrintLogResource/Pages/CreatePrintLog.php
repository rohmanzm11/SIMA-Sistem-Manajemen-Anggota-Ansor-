<?php
// app/Filament/Resources/PrintLogResource/Pages/CreatePrintLog.php

namespace App\Filament\Resources\PrintLogResource\Pages;

use App\Filament\Resources\PrintLogResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePrintLog extends CreateRecord
{
    protected static string $resource = PrintLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dicetak_oleh'] = Auth::id();
        $data['tanggal_cetak'] = now();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
