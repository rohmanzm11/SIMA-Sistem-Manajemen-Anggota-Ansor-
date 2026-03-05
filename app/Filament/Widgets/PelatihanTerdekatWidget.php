<?php

namespace App\Filament\Widgets;

use App\Models\Pelatihan;
use Filament\Widgets\Widget;

class PelatihanTerdekatWidget extends Widget
{
    protected static string $view = 'filament.widgets.pelatihan-terdekat-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 3;

    public function getPelatihanTerdekat(): \Illuminate\Support\Collection
    {
        return Pelatihan::with([
            'pelatihanDetails' => function ($q) {
                $q->where('is_active', true)
                    ->whereNotNull('tanggal')
                    ->orderBy('tanggal')
                    ->orderBy('jam_mulai')
                    ->with('materi');
            },
        ])
            ->whereHas('pelatihanDetails', function ($q) {
                $q->where('is_active', true)
                    ->whereNotNull('tanggal')
                    ->where('tanggal', '>=', now()->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function (Pelatihan $p) {
                $p->sesiAktif = $p->pelatihanDetails->filter(
                    fn($d) => $d->tanggal && $d->tanggal >= now()->toDateString()
                )->values();
                return $p;
            })
            ->filter(fn($p) => $p->sesiAktif->isNotEmpty())
            ->values();
    }

    protected function getViewData(): array
    {
        return [
            'pelatihanList' => $this->getPelatihanTerdekat(),
        ];
    }
}
