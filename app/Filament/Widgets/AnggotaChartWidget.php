<?php

namespace App\Filament\Widgets;

use App\Models\Anggota;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AnggotaChartWidget extends ChartWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Pendaftaran Anggota';
    protected static ?string $description = 'Jumlah anggota baru per bulan dalam 12 bulan terakhir';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = '1';

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'month' => '30 Hari Terakhir',
            'year'  => '12 Bulan Terakhir',
        ];
    }

    protected function getData(): array
    {
        if ($this->filter === 'month') {
            // 30 hari terakhir
            $labels = [];
            $total  = [];
            $verified = [];

            for ($i = 29; $i >= 0; $i--) {
                $date     = now()->subDays($i);
                $labels[] = $date->format('d M');
                $total[]  = Anggota::whereDate('created_at', $date)->count();
                $verified[] = Anggota::whereDate('created_at', $date)
                    ->where('status_verifikasi', 'Diverifikasi')->count();
            }
        } else {
            // 12 bulan terakhir
            $labels = [];
            $total  = [];
            $verified = [];

            for ($i = 11; $i >= 0; $i--) {
                $date     = now()->subMonths($i);
                $labels[] = $date->format('M Y');
                $total[]  = Anggota::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)->count();
                $verified[] = Anggota::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status_verifikasi', 'Diverifikasi')->count();
            }
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Total Daftar',
                    'data'            => $total,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor'     => 'rgb(59, 130, 246)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Terverifikasi',
                    'data'            => $verified,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor'     => 'rgb(16, 185, 129)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
