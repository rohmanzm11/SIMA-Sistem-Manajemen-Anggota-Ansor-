<?php

namespace App\Filament\Widgets;

use App\Models\Anggota;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnggotaStatsOverview extends BaseWidget
{

    use HasWidgetShield;
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = '3';

    protected function getStats(): array
    {
        $total        = Anggota::count();
        $pending      = Anggota::where('status_verifikasi', 'Pending')->count();
        $diverifikasi = Anggota::where('status_verifikasi', 'Diverifikasi')->count();
        $ditolak      = Anggota::where('status_verifikasi', 'Ditolak')->count();
        $lakiLaki     = Anggota::where('jenis_kelamin', 'L')->count();
        $perempuan    = Anggota::where('jenis_kelamin', 'P')->count();

        // Tren 7 hari terakhir untuk chart mini
        $tren = collect(range(6, 0))->map(
            fn($i) => Anggota::whereDate('created_at', now()->subDays($i))->count()
        )->toArray();

        return [
            Stat::make('Total Anggota', number_format($total))
                ->description('Seluruh anggota terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->chart($tren)
                ->color('primary'),

            Stat::make('Menunggu Verifikasi', number_format($pending))
                ->description($pending > 0 ? 'Perlu tindakan segera' : 'Semua sudah diproses')
                ->descriptionIcon($pending > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->color($pending > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.anggotas.index', ['tableFilters[status_verifikasi][value]' => 'Pending'])),

            Stat::make('Terverifikasi', number_format($diverifikasi))
                ->description($total > 0 ? round(($diverifikasi / $total) * 100, 1) . '% dari total anggota' : '—')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Ditolak', number_format($ditolak))
                ->description('Perlu perbaikan data')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Laki-laki', number_format($lakiLaki))
                ->description('Anggota laki-laki')
                ->descriptionIcon('heroicon-o-user')
                ->color('info'),

            Stat::make('Perempuan', number_format($perempuan))
                ->description('Anggota perempuan')
                ->descriptionIcon('heroicon-o-user')
                ->color('pink'),
        ];
    }
}
