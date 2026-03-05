<?php

namespace App\Filament\Resources\AnggotaResource\Pages;

use App\Filament\Resources\AnggotaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnggota extends CreateRecord
{
    protected static string $resource = AnggotaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika status verifikasi tidak diisi, default ke Pending
        $data['status_verifikasi'] ??= 'Pending';

        return $data;
    }
    protected function afterCreate(): void
    {
        $record = $this->record;

        $nia = AnggotaResource::generateNia($record);

        $record->update(['nia' => $nia]);
    }
}
