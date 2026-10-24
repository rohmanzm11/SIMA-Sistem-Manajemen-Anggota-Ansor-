<?php

namespace App\Filament\Widgets;

use App\Models\Anggota;
use App\Models\Pekerjaan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class AnggotaPekerjaanWidget extends ChartWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Distribusi Pekerjaan Anggota';
    protected static ?string $description = 'Ringkasan jenis pekerjaan seluruh anggota';
    public static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected static string $color = 'info';

    protected function getData(): array
    {
        // Query langsung dari tabel anggotas dengan join pekerjaans
        $pekerjaanData = Anggota::query()
            ->whereNotNull('pekerjaan_id')
            ->join('pekerjaans', 'anggotas.pekerjaan_id', '=', 'pekerjaans.id')
            ->selectRaw('pekerjaans.nama_pekerjaan, COUNT(anggotas.id) as jumlah')
            ->groupBy('pekerjaans.nama_pekerjaan')
            ->orderByRaw('COUNT(anggotas.id) DESC')
            ->pluck('jumlah', 'nama_pekerjaan');

        // Handle jika tidak ada data
        if ($pekerjaanData->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Tidak Ada Data Pekerjaan',
                        'data' => [0],
                        'backgroundColor' => ['#D1D5DB'],
                    ],
                ],
                'labels' => ['Tidak Ada Data'],
            ];
        }

        // Warna untuk chart
        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#14B8A6', // Teal
            '#F97316', // Orange
            '#6366F1', // Indigo
            '#06B6D4', // Cyan
            '#D946EF', // Fuchsia
            '#64748B', // Slate
        ];

        $backgroundColor = array_map(
            fn($index) => $colors[$index % count($colors)],
            range(0, $pekerjaanData->count() - 1)
        );

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Anggota',
                    'data' => $pekerjaanData->values()->all(),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $pekerjaanData->keys()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 15,
                        'font' => [
                            'size' => 11,
                        ],
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.label + ": " + context.parsed + " anggota"; }',
                    ],
                ],
            ],
        ];
    }
}
