<?php

namespace App\Filament\Resources\PrintLogResource\Pages;

use App\Filament\Resources\PrintLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrintLog extends EditRecord
{
    protected static string $resource = PrintLogResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['pelatihan_id'] = $this->record->pelatihanDetail?->pelatihan_id;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $pelatihanId = $data['pelatihan_id'] ?? null;
        unset($data['pelatihan_id'], $data['anggota_ids']);

        if ($pelatihanId && ($data['jenis_cetakan'] ?? null) === 'Sertifikat') {
            $data['pelatihan_detail_id'] = PrintLogResource::resolveDetailId(
                (int) $pelatihanId,
                (int) ($data['anggota_id'] ?? 0)
            );
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}