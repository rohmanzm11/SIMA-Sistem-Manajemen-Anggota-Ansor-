<?php

namespace App\Filament\Widgets;

use App\Models\Pendidikan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;

class PendidikanInfografisWidget extends Widget
{
    use HasWidgetShield;

    protected static string $view = 'filament.widgets.pendidikan-infografis';

    protected static ?string $heading = 'Infografis Pendidikan Anggota';
    protected static ?string $description = 'Ringkasan tingkat pendidikan formal dengan 7 kategori';
    protected static ?int $sort = 2;
   protected int | string | array $columnSpan = 'full';

private function getCategories(): array
{
    return [
        ['id' => 'dasar',     'nama' => 'Tingkat Dasar',        'subtitle' => 'SD/MI & SMP/MTs', 'jenjang' => ['SD', 'MI', 'SMP', 'MTs'],   'icon' => 'heroicon-o-building-library', 'color' => 'blue'],
        ['id' => 'menengah',  'nama' => 'Tingkat Menengah',     'subtitle' => 'SMA/SMK/MA/MAK',  'jenjang' => ['SMA', 'SMK', 'MA', 'MAK'],  'icon' => 'heroicon-o-academic-cap',     'color' => 'purple'],
        ['id' => 'diploma',   'nama' => 'Tingkat Diploma',      'subtitle' => 'D1 - D4',         'jenjang' => ['D1', 'D2', 'D3', 'D4'],     'icon' => 'heroicon-o-document-text',    'color' => 'indigo'],
        ['id' => 's1',        'nama' => 'Tingkat Sarjana',      'subtitle' => 'S1',              'jenjang' => ['S1'],                       'icon' => 'heroicon-o-book-open',        'color' => 'green'],
        ['id' => 's2',        'nama' => 'Tingkat Magister',     'subtitle' => 'S2',              'jenjang' => ['S2'],                       'icon' => 'heroicon-o-trophy',           'color' => 'amber'],
        ['id' => 's3',        'nama' => 'Tingkat Doktor',       'subtitle' => 'S3',              'jenjang' => ['S3'],                       'icon' => 'heroicon-o-star',             'color' => 'red'],
        ['id' => 'pesantren', 'nama' => 'Pondok Pesantren',     'subtitle' => 'Pesantren',       'jenjang' => ['Pesantren'],                'icon' => 'heroicon-o-home-modern',      'color' => 'pink'],
        ['id' => 'diniyyah',  'nama' => 'Pendidikan Diniyyah',  'subtitle' => 'Diniyyah',        'jenjang' => ['Diniyyah'],                 'icon' => 'heroicon-o-sparkles',         'color' => 'teal'],
    ];
}

    #[Reactive]
    public string $filterStatus = 'semua';

    /**
     * Definisikan kategori pendidikan
     */
    

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