<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnggotaStatsOverview;
use App\Filament\Widgets\AnggotaChartWidget;
use App\Filament\Widgets\AnggotaTableWidget;
use App\Filament\Widgets\AnggotaAktivitasWidget;
use App\Filament\Widgets\AnggotaPelatihanTableWidget;
use App\Filament\Widgets\AnggotaPekerjaanWidget;
use App\Filament\Widgets\InfografisPendidikanWidget;
use App\Filament\Widgets\PelatihanTerdekatWidget;
use App\Filament\Widgets\WelcomeWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasPageShield;

    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Dashboard';
    protected static ?int    $navigationSort  = -2;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_anggota')
                ->label('Tambah Anggota')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->url(route('filament.admin.resources.anggotas.create')),
        ];
    }

    public function getWidgets(): array
    {
        return [
            // Row 1: Welcome (Full Width)
            WelcomeWidget::class,

            // Row 2: Stats Overview (Full Width)
            AnggotaStatsOverview::class,

            // Row 3: Charts (2 Columns)
            AnggotaChartWidget::class,
            AnggotaPekerjaanWidget::class,

            // Row 4: Education & Activity (2 Columns)
            PelatihanTerdekatWidget::class,

            // Row 5: Tables (Full Width)
            AnggotaTableWidget::class,
            AnggotaPelatihanTableWidget::class,

            // Row 6: Infografis Pendidikan (Full Width)
            InfografisPendidikanWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'lg' => 2,
            'xl' => 2,
            '2xl' => 2,
        ];
    }
}
