<?php

namespace App\Filament\Pages;

use App\Models\Anggota;
use App\Models\Kecamatan;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DaftarAnggota extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen Anggota';
    protected static ?string $title           = 'Manajemen Anggota';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $navigationGroup = 'Keanggotaan';
    protected static string  $view            = 'filament.pages.daftar-anggota';

    public string $search       = '';
    public string $filterStatus = '';
    public string $filterKec    = '';

    protected $queryString = [
        'search'       => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterKec'    => ['except' => ''],
    ];

    public function getAnggotaProperty(): Collection
    {
        return Anggota::with(['kecamatan', 'desa', 'strukturOrganisasi.jabatan'])
            ->when(
                filled($this->search),
                fn (Builder $q) => $q->where(function (Builder $sub) {
                    $sub->where('nama_lengkap', 'like', "%{$this->search}%")
                        ->orWhere('nia', 'like', "%{$this->search}%")
                        ->orWhere('nomor_hp', 'like', "%{$this->search}%");
                })
            )
            ->when(
                filled($this->filterStatus),
                fn (Builder $q) => $q->where('status_verifikasi', $this->filterStatus)
            )
            ->when(
                filled($this->filterKec),
                fn (Builder $q) => $q->where('kecamatan_id', $this->filterKec)
            )
            ->latest()
            ->get();
    }

    public function getKecamatanListProperty(): Collection
    {
        return Kecamatan::orderBy('nama_kecamatan')->get();
    }

    public function getStatistikProperty(): array
    {
        $all = Anggota::selectRaw('status_verifikasi, COUNT(*) as total')
            ->groupBy('status_verifikasi')
            ->pluck('total', 'status_verifikasi');

        return [
            'total'        => $all->sum(),
            'diverifikasi' => $all['Diverifikasi'] ?? 0,
            'pending'      => $all['Pending'] ?? 0,
            'ditolak'      => $all['Ditolak'] ?? 0,
        ];
    }

    public function resetFilter(): void
    {
        $this->reset(['search', 'filterStatus', 'filterKec']);
    }
}