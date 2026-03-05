<?php

namespace App\Filament\Widgets;

use App\Models\Pendidikan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;

/**
 * Widget Infografis Pendidikan - FIXED VERSION
 * Versi yang sudah diperbaiki untuk tampil dengan baik di dashboard
 */
class InfografisPendidikanWidget extends Widget
{
    use HasWidgetShield;

    protected static string $view = 'filament.widgets.infografis-pendidikan-content';

    protected static ?string $heading = '📚 Profil Pendidikan Anggota';
    protected static ?string $description = 'Ringkasan tingkat pendidikan formal - 7 kategori';
    protected static ?int $sort = 10;

    // PENTING: Full width untuk menampilkan semua 7 kartu
    // protected int | string | array $columnSpan = 'full';
    protected int | string | array $columnSpan = 6;

    #[Reactive]
    public string $filterStatus = 'semua';

    /**
     * Definisikan kategori pendidikan
     */
    private function getCategories(): array
    {
        return [
            [
                'id' => 'dasar',
                'nama' => 'Tingkat Dasar',
                'subtitle' => 'SD/MI & SMP/MTs',
                'jenjang' => ['SD', 'MI', 'SMP', 'MTs'],
                'icon' => '🏫',
                'color' => 'blue',
                'gradientFrom' => 'from-blue-400',
                'gradientTo' => 'to-blue-600',
            ],
            [
                'id' => 'menengah',
                'nama' => 'Tingkat Menengah',
                'subtitle' => 'SMA/SMK/MA/MAK',
                'jenjang' => ['SMA', 'SMK', 'MA', 'MAK'],
                'icon' => '🎓',
                'color' => 'purple',
                'gradientFrom' => 'from-purple-400',
                'gradientTo' => 'to-purple-600',
            ],
            [
                'id' => 's1',
                'nama' => 'Tingkat Sarjana',
                'subtitle' => 'S1',
                'jenjang' => ['S1'],
                'icon' => '🎯',
                'color' => 'green',
                'gradientFrom' => 'from-green-400',
                'gradientTo' => 'to-green-600',
            ],
            [
                'id' => 's2',
                'nama' => 'Tingkat Magister',
                'subtitle' => 'S2',
                'jenjang' => ['S2'],
                'icon' => '🏆',
                'color' => 'amber',
                'gradientFrom' => 'from-amber-400',
                'gradientTo' => 'to-amber-600',
            ],
            [
                'id' => 's3',
                'nama' => 'Tingkat Doktor',
                'subtitle' => 'S3',
                'jenjang' => ['S3'],
                'icon' => '👑',
                'color' => 'red',
                'gradientFrom' => 'from-red-400',
                'gradientTo' => 'to-red-600',
            ],
            [
                'id' => 'pesantren',
                'nama' => 'Pondok Pesantren',
                'subtitle' => 'Pesantren',
                'jenjang' => ['Pesantren'],
                'icon' => '📖',
                'color' => 'pink',
                'gradientFrom' => 'from-pink-400',
                'gradientTo' => 'to-pink-600',
            ],
            [
                'id' => 'diniyyah',
                'nama' => 'Pendidikan Diniyyah',
                'subtitle' => 'Diniyyah',
                'jenjang' => ['Diniyyah'],
                'icon' => '✨',
                'color' => 'teal',
                'gradientFrom' => 'from-teal-400',
                'gradientTo' => 'to-teal-600',
            ],
        ];
    }

    /**
     * Get data untuk view
     */
    public function getData(): array
    {
        $categories = $this->getCategories();
        $total = Pendidikan::count();

        // Apply filter
        $query = Pendidikan::query();
        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
            $total = $query->count();
        }

        // Hitung statistik untuk setiap kategori
        foreach ($categories as &$cat) {
            $baseQuery = Pendidikan::whereIn('jenjang', $cat['jenjang']);

            // Apply filter
            if ($this->filterStatus !== 'semua') {
                $baseQuery->where('status', $this->filterStatus);
            }

            $cat['count'] = $baseQuery->count();
            $cat['percentage'] = $total > 0 ? round(($cat['count'] / $total) * 100) : 0;

            // Status breakdown
            $cat['lulus'] = Pendidikan::whereIn('jenjang', $cat['jenjang'])
                ->where('status', 'Lulus')->count();
            $cat['sedang_belajar'] = Pendidikan::whereIn('jenjang', $cat['jenjang'])
                ->where('status', 'Sedang Belajar')->count();
            $cat['berhenti'] = Pendidikan::whereIn('jenjang', $cat['jenjang'])
                ->where('status', 'Berhenti')->count();
        }

        // Summary stats
        $summary = [
            'total_riwayat' => Pendidikan::count(),
            'lulus_total' => Pendidikan::where('status', 'Lulus')->count(),
            'sedang_belajar_total' => Pendidikan::where('status', 'Sedang Belajar')->count(),
            'berhenti_total' => Pendidikan::where('status', 'Berhenti')->count(),
            'sarjana_plus' => Pendidikan::whereIn('jenjang', ['S1', 'S2', 'S3'])->count(),
        ];

        $summary['persen_lulus'] = $summary['total_riwayat'] > 0
            ? round(($summary['lulus_total'] / $summary['total_riwayat']) * 100)
            : 0;
        $summary['persen_sarjana'] = $summary['total_riwayat'] > 0
            ? round(($summary['sarjana_plus'] / $summary['total_riwayat']) * 100)
            : 0;

        return [
            'categories' => $categories,
            'summary' => $summary,
            'filterStatus' => $this->filterStatus,
        ];
    }

    /**
     * Set filter status
     */
    public function setFilterStatus(string $status): void
    {
        $this->filterStatus = $status;
    }
}
