<?php

namespace App\Filament\Widgets;

use App\Models\Anggota;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class AnggotaAktivitasWidget extends Widget
{
    use HasWidgetShield;
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 2;
    protected static string $view = 'filament.widgets.anggota-aktivitas-widget';

    public function getViewData(): array
    {
        $aktivitas = collect();

        // 10 anggota terbaru mendaftar
        $baru = Anggota::latest('created_at')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'type'    => 'daftar',
                'nama'    => $a->nama_lengkap,
                'label'   => 'Mendaftar',
                'waktu'   => $a->created_at,
                'color'   => 'blue',
                'icon'    => 'heroicon-o-user-plus',
                'url'     => route('filament.admin.resources.anggotas.view', $a->id),
            ]);

        // 5 anggota terbaru diverifikasi
        $verified = Anggota::where('status_verifikasi', 'Diverifikasi')
            ->whereNotNull('tanggal_verifikasi')
            ->latest('tanggal_verifikasi')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'type'    => 'verifikasi',
                'nama'    => $a->nama_lengkap,
                'label'   => 'Diverifikasi',
                'waktu'   => Carbon::parse($a->tanggal_verifikasi),
                'color'   => 'green',
                'icon'    => 'heroicon-o-check-badge',
                'url'     => route('filament.admin.resources.anggotas.view', $a->id),
            ]);

        // 5 anggota terbaru ditolak
        $ditolak = Anggota::where('status_verifikasi', 'Ditolak')
            ->whereNotNull('tanggal_verifikasi')
            ->latest('tanggal_verifikasi')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'type'    => 'tolak',
                'nama'    => $a->nama_lengkap,
                'label'   => 'Ditolak',
                'waktu'   => Carbon::parse($a->tanggal_verifikasi),
                'color'   => 'red',
                'icon'    => 'heroicon-o-x-circle',
                'url'     => route('filament.admin.resources.anggotas.view', $a->id),
            ]);

        $aktivitas = $baru->merge($verified)->merge($ditolak)
            ->sortByDesc('waktu')
            ->take(10)
            ->values();

        return compact('aktivitas');
    }
}
