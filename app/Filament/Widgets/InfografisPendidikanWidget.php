<?php

namespace App\Filament\Widgets;

use App\Models\Pendidikan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

/**
 * Widget Infografis Pendidikan
 */
class InfografisPendidikanWidget extends Widget
{
    use HasWidgetShield;

    protected static string $view = 'filament.widgets.infografis-pendidikan-content';

    protected static ?int $sort = 10;


    // Status sesuai opsi di form pendidikan (DashboardAnggota::pendidikanForm)
    public const STATUS_LULUS       = 'Lulus';
    public const STATUS_TIDAK_LULUS = 'Tidak Lulus';
    public const STATUS_BERJALAN    = 'Sedang Berjalan';

    public string $filterStatus = 'semua';

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

    public function getData(): array
    {
        // Satu query saja: jumlah per jenjang & status
        /** @var Collection $rows */
        $rows = Pendidikan::query()
            ->selectRaw('jenjang, status, COUNT(*) as total')
            ->groupBy('jenjang', 'status')
            ->get();

        $filtered = $this->filterStatus === 'semua'
            ? $rows
            : $rows->where('status', $this->filterStatus);

        $totalFiltered = (int) $filtered->sum('total');

        $categories = collect($this->getCategories())->map(function (array $cat) use ($rows, $filtered, $totalFiltered) {
            $catRows = $rows->whereIn('jenjang', $cat['jenjang']);

            $cat['count']          = (int) $filtered->whereIn('jenjang', $cat['jenjang'])->sum('total');
            $cat['percentage']     = $totalFiltered > 0 ? round(($cat['count'] / $totalFiltered) * 100) : 0;
            $cat['lulus']          = (int) $catRows->where('status', self::STATUS_LULUS)->sum('total');
            $cat['tidak_lulus']    = (int) $catRows->where('status', self::STATUS_TIDAK_LULUS)->sum('total');
            $cat['sedang_berjalan'] = (int) $catRows->where('status', self::STATUS_BERJALAN)->sum('total');

            // Alias untuk view lama
            $cat['sedang_belajar'] = $cat['sedang_berjalan'];
            $cat['berhenti']       = $cat['tidak_lulus'];

            return $cat;
        })->all();

        $totalRiwayat = (int) $rows->sum('total');
        $lulus        = (int) $rows->where('status', self::STATUS_LULUS)->sum('total');
        $tidakLulus   = (int) $rows->where('status', self::STATUS_TIDAK_LULUS)->sum('total');
        $berjalan     = (int) $rows->where('status', self::STATUS_BERJALAN)->sum('total');
        $sarjanaPlus  = (int) $rows->whereIn('jenjang', ['S1', 'S2', 'S3'])->sum('total');

        $summary = [
            'total_riwayat'        => $totalRiwayat,
            'lulus_total'          => $lulus,
            'tidak_lulus_total'    => $tidakLulus,
            'sedang_berjalan_total' => $berjalan,
            // Alias untuk view lama
            'sedang_belajar_total' => $berjalan,
            'berhenti_total'       => $tidakLulus,
            'sarjana_plus'         => $sarjanaPlus,
            'persen_lulus'         => $totalRiwayat > 0 ? round(($lulus / $totalRiwayat) * 100) : 0,
            'persen_sarjana'       => $totalRiwayat > 0 ? round(($sarjanaPlus / $totalRiwayat) * 100) : 0,
        ];

        return [
            'categories'   => $categories,
            'summary'      => $summary,
            'filterStatus' => $this->filterStatus,
        ];
    }

    protected function getViewData(): array
    {
        return $this->getData();
    }

    public function setFilterStatus(string $status): void
    {
        // Terima nilai lama dari view dan petakan ke status yang benar
        $status = match ($status) {
            'Sedang Belajar' => self::STATUS_BERJALAN,
            'Berhenti'       => self::STATUS_TIDAK_LULUS,
            default          => $status,
        };

        $valid = ['semua', self::STATUS_LULUS, self::STATUS_TIDAK_LULUS, self::STATUS_BERJALAN];

        $this->filterStatus = in_array($status, $valid, true) ? $status : 'semua';
    }
}