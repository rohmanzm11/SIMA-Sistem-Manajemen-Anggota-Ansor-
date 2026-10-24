<?php

namespace App\Filament\Widgets;

use App\Models\Pelatihan;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class PelatihanTerdekatWidget extends Widget
{
    protected static string $view = 'filament.widgets.pelatihan-terdekat-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 3;

    public function getPelatihanTerdekat(): Collection
    {
        $hariIni = now()->toDateString();

        return Pelatihan::with([
            'pelatihanDetails' => fn ($q) => $q
                ->where('is_active', true)
                ->orderBy('id')
                ->with('materi'),
        ])
            // Pelatihan yang sedang berjalan atau akan datang
            ->where(function ($q) use ($hariIni) {
                $q->whereDate('tanggal_selesai', '>=', $hariIni)
                    ->orWhere(function ($q2) use ($hariIni) {
                        $q2->whereNull('tanggal_selesai')
                            ->whereDate('tanggal_mulai', '>=', $hariIni);
                    });
            })
            ->orderBy('tanggal_mulai')
            ->limit(3)
            ->get()
            ->each(function (Pelatihan $p) {
                // Nama properti dipertahankan agar view lama tetap jalan
                $p->sesiAktif = $p->pelatihanDetails;
            });
    }

    public function formatTanggalPelatihan(Pelatihan $pelatihan, string $format = 'd M Y'): string
    {
        if (! $pelatihan->tanggal_mulai) {
            return '—';
        }

        $mulai   = Carbon::parse($pelatihan->tanggal_mulai);
        $selesai = $pelatihan->tanggal_selesai
            ? Carbon::parse($pelatihan->tanggal_selesai)
            : null;

        if (! $selesai || $selesai->isSameDay($mulai)) {
            return $mulai->translatedFormat($format);
        }

        return $mulai->translatedFormat($format) . ' – ' . $selesai->translatedFormat($format);
    }

    protected function getViewData(): array
    {
        return [
            'pelatihanList' => $this->getPelatihanTerdekat(),
        ];
    }
}