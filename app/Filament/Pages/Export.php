<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PrintLogResource;
use App\Models\Anggota;
use App\Models\Kta;
use App\Models\Pelatihan;
use App\Models\PrintLog;
use App\Models\TemplateSertifikat;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Export extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationLabel = 'Export';
    protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';
    protected static ?int $navigationSort     = 6;
    protected static ?string $title           = 'Export';
    protected static string $view             = 'filament.pages.export';

    public ?array $sertifikat = [];
    public ?array $kta = [];

    public function mount(): void
    {
        $this->sertifikatForm->fill();
        $this->ktaForm->fill(['mode' => 'pelatihan']);
    }

    protected function getForms(): array
    {
        return ['sertifikatForm', 'ktaForm'];
    }

    // ───────── FORM SERTIFIKAT (kiri) ─────────
    public function sertifikatForm(Form $form): Form
    {
        return $form
            ->statePath('sertifikat')
            ->schema([
                Forms\Components\Select::make('pelatihan_id')
                    ->label('Pelatihan')
                    ->placeholder('Pilih pelatihan')
                    ->options(fn () => PrintLogResource::pelatihanOptions())
                    ->searchable()
                    ->required()
                    ->live(),

                Forms\Components\Select::make('template_sertifikat_id')
                    ->label('Template Sertifikat')
                    ->options(fn () => TemplateSertifikat::pluck('nama_batch', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Placeholder::make('info')
                    ->label('')
                    ->content(fn (Get $get) => $this->infoPeserta($get('pelatihan_id'))),
            ]);
    }

    // ───────── FORM KTA (kanan) ─────────
    public function ktaForm(Form $form): Form
    {
        return $form
            ->statePath('kta')
            ->schema([
                Forms\Components\Select::make('kta_id')
                    ->label('Batch KTA')
                    ->placeholder('Pilih batch KTA')
                    ->options(fn () => Kta::pluck('nama_batch', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Radio::make('mode')
                    ->label('Export untuk')
                    ->options([
                        'pelatihan' => 'Per pelatihan (semua peserta)',
                        'anggota'   => 'Satu anggota',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\Select::make('pelatihan_id')
                    ->label('Pelatihan')
                    ->placeholder('Pilih pelatihan')
                    ->options(fn () => PrintLogResource::pelatihanOptions())
                    ->searchable()
                    ->live()
                    ->visible(fn (Get $get) => $get('mode') === 'pelatihan')
                    ->required(fn (Get $get) => $get('mode') === 'pelatihan'),

                Forms\Components\Select::make('anggota_id')
                    ->label('Anggota')
                    ->placeholder('Cari nama anggota')
                    ->options(fn () => Anggota::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('mode') === 'anggota')
                    ->required(fn (Get $get) => $get('mode') === 'anggota'),

                Forms\Components\Placeholder::make('info')
                    ->label('')
                    ->visible(fn (Get $get) => $get('mode') === 'pelatihan')
                    ->content(fn (Get $get) => $this->infoPeserta($get('pelatihan_id'))),
            ]);
    }

    protected function infoPeserta($pelatihanId): string
    {
        if (blank($pelatihanId)) {
            return 'Pilih pelatihan untuk melihat jumlah peserta.';
        }

        $n = count(PrintLogResource::anggotaPeserta((int) $pelatihanId));

        return $n > 0
            ? "{$n} peserta akan ikut diexport."
            : 'Belum ada anggota yang tercatat mengikuti pelatihan ini.';
    }

    // ───────── ACTIONS ─────────
    public function exportSertifikat(): void
    {
        $data = $this->sertifikatForm->getState();
        $pelatihanId = (int) $data['pelatihan_id'];

        $anggotaIds = array_keys(PrintLogResource::anggotaPeserta($pelatihanId));

        if (empty($anggotaIds)) {
            Notification::make()->title('Tidak ada peserta pada pelatihan ini')->warning()->send();
            return;
        }

        $ids = DB::transaction(fn () => collect($anggotaIds)->map(
            fn ($anggotaId) => PrintLog::create([
                'jenis_cetakan'          => 'Sertifikat',
                'anggota_id'             => $anggotaId,
                'pelatihan_detail_id'    => PrintLogResource::resolveDetailId($pelatihanId, (int) $anggotaId),
                'template_sertifikat_id' => $data['template_sertifikat_id'],
                'tanggal_cetak'          => now(),
                'dicetak_oleh'           => auth()->id(),
            ])->id
        )->all());

        $this->download($ids, 'sertifikat', Pelatihan::find($pelatihanId)?->nama_pelatihan);
    }

    public function exportKta(): void
    {
        $data = $this->ktaForm->getState();

        // ── Satu anggota: langsung buka PDF ──
        if ($data['mode'] === 'anggota') {
            $log = PrintLog::create([
                'jenis_cetakan' => 'KTA',
                'anggota_id'    => $data['anggota_id'],
                'kta_id'        => $data['kta_id'],
                'tanggal_cetak' => now(),
                'dicetak_oleh'  => auth()->id(),
            ]);

            Notification::make()->title('KTA berhasil dibuat')->success()->send();
            $this->redirect(route('kta.pdf', $log->id));
            return;
        }

        // ── Per pelatihan: ZIP ──
        $pelatihanId = (int) $data['pelatihan_id'];
        $anggotaIds  = array_keys(PrintLogResource::anggotaPeserta($pelatihanId));

        if (empty($anggotaIds)) {
            Notification::make()->title('Tidak ada peserta pada pelatihan ini')->warning()->send();
            return;
        }

        $ids = DB::transaction(fn () => collect($anggotaIds)->map(
            fn ($anggotaId) => PrintLog::create([
                'jenis_cetakan' => 'KTA',
                'anggota_id'    => $anggotaId,
                'kta_id'        => $data['kta_id'],
                'tanggal_cetak' => now(),
                'dicetak_oleh'  => auth()->id(),
            ])->id
        )->all());

        $this->download($ids, 'kta', Pelatihan::find($pelatihanId)?->nama_pelatihan);
    }

    protected function download(array $ids, string $jenis, ?string $nama = null): void
    {
        $token = (string) Str::uuid();

        Cache::put("export:{$token}", [
            'ids'   => $ids,
            'jenis' => $jenis,
            'nama'  => $nama,
        ], now()->addMinutes(10));

        Notification::make()->title(count($ids) . ' data berhasil diexport')->success()->send();

        $this->redirect(route('export.download', $token));
    }
}