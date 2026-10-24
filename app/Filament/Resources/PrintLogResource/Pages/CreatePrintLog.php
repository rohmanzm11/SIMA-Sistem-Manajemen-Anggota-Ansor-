<?php

namespace App\Filament\Resources\PrintLogResource\Pages;

use App\Filament\Resources\PrintLogResource;
use App\Models\PrintLog;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePrintLog extends CreateRecord
{
    protected static string $resource = PrintLogResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $anggotaIds  = $data['anggota_ids'] ?? [];
        $pelatihanId = $data['pelatihan_id'] ?? null;

        unset($data['anggota_ids'], $data['pelatihan_id']);

        if (($data['jenis_cetakan'] ?? null) !== 'Sertifikat') {
            return PrintLog::create($data);
        }

        return DB::transaction(function () use ($data, $anggotaIds, $pelatihanId) {
            $records = collect($anggotaIds)->map(fn ($anggotaId) => PrintLog::create($data + [
                'anggota_id'          => (int) $anggotaId,
                'pelatihan_detail_id' => PrintLogResource::resolveDetailId((int) $pelatihanId, (int) $anggotaId),
            ]));

            return $records->first();
        });
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        $n = count($this->data['anggota_ids'] ?? []);

        return $n > 1 ? "{$n} log cetak sertifikat berhasil dibuat" : 'Log cetak berhasil dibuat';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

       protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['dicetak_oleh']  = Auth::id();
    $data['tanggal_cetak'] = now();

    return $data;
}
}