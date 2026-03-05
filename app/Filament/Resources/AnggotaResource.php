<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnggotaResource\Pages;
use App\Models\Anggota;
use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\Kecamatan;
use App\Models\Level;
use App\Models\Organisasi;
use App\Models\Pekerjaan;
use App\Models\Politik;
use App\Models\SocialMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnggotaResource extends Resource
{
    protected static ?string $model = Anggota::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Anggota';
    protected static ?string $modelLabel = 'Anggota';
    protected static ?string $pluralModelLabel = 'Anggota';
    protected static ?string $navigationGroup = 'Keanggotaan';

    // =========================================================================
    // NIA GENERATOR
    // =========================================================================

    public static function generateNia(Anggota $record): string
    {
        $kecamatanId = $record->kecamatan_id ?? 'X';
        $desaUrutan  = 'X';

        if ($record->kecamatan_id && $record->desa_id) {
            $desaUrutan = Desa::where('kecamatan_id', $record->kecamatan_id)
                ->orderBy('id')
                ->pluck('id')
                ->search($record->desa_id);

            $desaUrutan = $desaUrutan !== false ? $desaUrutan + 1 : 'X';
        }

        return "{$record->id}/X-11/{$kecamatanId}/{$desaUrutan}/KTR/" . now()->year;
    }

    public static function previewNia(?int $anggotaId, ?int $kecamatanId, ?int $desaId): string
    {
        if (! $anggotaId || ! $kecamatanId || ! $desaId) {
            return '';
        }

        $desaUrutan = Desa::where('kecamatan_id', $kecamatanId)
            ->orderBy('id')
            ->pluck('id')
            ->search($desaId);

        $desaUrutan = $desaUrutan !== false ? $desaUrutan + 1 : 'X';

        return "{$anggotaId}/X-11/{$kecamatanId}/{$desaUrutan}/KTR/" . now()->year;
    }

    // =========================================================================
    // FORM
    // =========================================================================

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ----- NIA -------------------------------------------------------
            Forms\Components\Section::make('Nomor Induk Anggota (NIA)')
                ->icon('heroicon-o-identification')
                ->schema([
                    Forms\Components\TextInput::make('nia')
                        ->label('NIA')
                        ->readOnly()
                        ->dehydrated(true)
                        ->placeholder('Pilih kecamatan & desa untuk generate NIA otomatis')
                        ->helperText('NIA ter-generate otomatis saat kecamatan atau desa diubah.')
                        ->columnSpanFull()
                        ->suffixIcon('heroicon-o-key'),
                ]),

            // ----- DATA PRIBADI ---------------------------------------------
            Forms\Components\Section::make('Data Pribadi')
                ->icon('heroicon-o-identification')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(16)
                        ->minLength(16)
                        ->numeric()
                        ->placeholder('16 digit NIK'),

                    Forms\Components\Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                        ->required(),

                    Forms\Components\TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->required()
                        ->displayFormat('d/m/Y')
                        ->maxDate(now()->subYears(10))
                        ->native(false),

                    Forms\Components\Select::make('status_pernikahan')
                        ->label('Status Pernikahan')
                        ->options([
                            'Belum Menikah' => 'Belum Menikah',
                            'Menikah'       => 'Menikah',
                            'Cerai Hidup'   => 'Cerai Hidup',
                            'Cerai Mati'    => 'Cerai Mati',
                        ])
                        ->required(),

                    Forms\Components\Select::make('golongan_darah')
                        ->label('Golongan Darah')
                        ->options([
                            'A'          => 'A',
                            'B'          => 'B',
                            'AB'         => 'AB',
                            'O'          => 'O',
                            'Tidak Tahu' => 'Tidak Tahu',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('tinggi_badan')
                        ->label('Tinggi Badan (cm)')
                        ->required()
                        ->numeric()
                        ->minValue(50)
                        ->maxValue(250)
                        ->suffix('cm'),

                    Forms\Components\TextInput::make('berat_badan')
                        ->label('Berat Badan (kg)')
                        ->required()
                        ->numeric()
                        ->minValue(10)
                        ->maxValue(300)
                        ->suffix('kg'),
                ])
                ->columns(2),

            // ----- ALAMAT ---------------------------------------------------
            Forms\Components\Section::make('Alamat')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\Select::make('kecamatan_id')
                        ->label('Kecamatan')
                        ->options(fn() => Kecamatan::orderBy('nama_kecamatan')->pluck('nama_kecamatan', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, $record) {
                            $set('desa_id', null);
                            $set('nia', '');
                        }),

                    Forms\Components\Select::make('desa_id')
                        ->label('Desa / Kelurahan')
                        ->options(function (Get $get): array {
                            $kecamatanId = $get('kecamatan_id');
                            if (blank($kecamatanId)) return [];
                            return Desa::where('kecamatan_id', $kecamatanId)
                                ->orderBy('nama_desa')
                                ->pluck('nama_desa', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state, $record) {
                            if ($record?->id) {
                                $nia = static::previewNia(
                                    anggotaId: $record->id,
                                    kecamatanId: (int) $get('kecamatan_id'),
                                    desaId: (int) $state,
                                );
                                $set('nia', $nia);
                            } else {
                                $set('nia', 'NIA akan digenerate otomatis setelah disimpan');
                            }
                        }),

                    Forms\Components\TextInput::make('rt')
                        ->label('RT')
                        ->required()
                        ->maxLength(10),

                    Forms\Components\TextInput::make('rw')
                        ->label('RW')
                        ->required()
                        ->maxLength(10),

                    Forms\Components\Textarea::make('alamat_lengkap')
                        ->label('Alamat Lengkap')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ----- KONTAK & PEKERJAAN ---------------------------------------
            Forms\Components\Section::make('Kontak & Pekerjaan')
                ->icon('heroicon-o-phone')
                ->schema([
                    Forms\Components\TextInput::make('nomor_hp')
                        ->label('Nomor HP')
                        ->required()
                        ->tel()
                        ->maxLength(15)
                        ->placeholder('08xxxxxxxxxx'),

                    Forms\Components\TextInput::make('alamat_email')
                        ->label('Alamat Email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->nullable(),

                    Forms\Components\Select::make('pekerjaan_id')
                        ->label('Pekerjaan')
                        ->options(fn() => Pekerjaan::orderBy('nama_pekerjaan')->pluck('nama_pekerjaan', 'id')->toArray())
                        ->searchable()
                        ->nullable(),

                    Forms\Components\Select::make('politik_id')
                        ->label('Afiliasi Politik')
                        ->options(fn() => Politik::orderBy('partai_politik')->pluck('partai_politik', 'id')->toArray())
                        ->searchable()
                        ->nullable(),
                ])
                ->columns(2),

            // ----- NPWP & BPJS ----------------------------------------------
            Forms\Components\Section::make('NPWP & BPJS')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Toggle::make('npwp_status')
                        ->label('Memiliki NPWP')
                        ->live()
                        ->afterStateUpdated(fn(Set $set, bool $state) => ! $state ? $set('npwp_nomor', null) : null),

                    Forms\Components\TextInput::make('npwp_nomor')
                        ->label('Nomor NPWP')
                        ->maxLength(20)
                        ->nullable()
                        ->visible(fn(Get $get): bool => (bool) $get('npwp_status'))
                        ->required(fn(Get $get): bool => (bool) $get('npwp_status')),

                    Forms\Components\Toggle::make('bpjs_status')
                        ->label('Memiliki BPJS')
                        ->live()
                        ->afterStateUpdated(fn(Set $set, bool $state) => ! $state ? $set('bpjs_nomor', null) : null),

                    Forms\Components\TextInput::make('bpjs_nomor')
                        ->label('Nomor BPJS')
                        ->maxLength(20)
                        ->nullable()
                        ->visible(fn(Get $get): bool => (bool) $get('bpjs_status'))
                        ->required(fn(Get $get): bool => (bool) $get('bpjs_status')),
                ])
                ->columns(2),

            // ----- PENDIDIKAN -----------------------------------------------
            Forms\Components\Section::make('Riwayat Pendidikan')
                ->icon('heroicon-o-academic-cap')
                ->description('Riwayat pendidikan formal maupun non-formal.')
                ->schema([
                    Forms\Components\Repeater::make('pendidikans')
                        ->label('')
                        ->relationship('pendidikans')
                        ->schema([
                            Forms\Components\Select::make('jenjang')
                                ->label('Jenjang')
                                ->options([
                                    'SD'        => 'SD',
                                    'SMP'       => 'SMP',
                                    'SMA'       => 'SMA',
                                    'SMK'       => 'SMK',
                                    'D1'        => 'D1',
                                    'D2'        => 'D2',
                                    'D3'        => 'D3',
                                    'D4'        => 'D4',
                                    'S1'        => 'S1',
                                    'S2'        => 'S2',
                                    'S3'        => 'S3',
                                    'MA'        => 'MA',
                                    'Pesantren' => 'Pesantren',
                                    'Diniyyah'  => 'Diniyyah',
                                ])
                                ->required()
                                ->searchable()
                                ->columnSpan(1),

                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'Lulus'           => 'Lulus',
                                    'Tidak Lulus'     => 'Tidak Lulus',
                                    'Sedang Berjalan' => 'Sedang Berjalan',
                                ])
                                ->required()
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('nama_institusi')
                                ->label('Nama Institusi')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: SMAN 1 Kudus')
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('jurusan')
                                ->label('Jurusan / Program Studi')
                                ->maxLength(255)
                                ->nullable()
                                ->placeholder('Kosongkan jika tidak ada')
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('tahun_masuk')
                                ->label('Tahun Masuk')
                                ->numeric()
                                ->minValue(1950)
                                ->maxValue(now()->year)
                                ->required()
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('tahun_lulus')
                                ->label('Tahun Lulus')
                                ->numeric()
                                ->minValue(1950)
                                ->maxValue(now()->year + 10)
                                ->nullable()
                                ->columnSpan(1),
                        ])
                        ->columns(4)
                        ->addActionLabel('+ Tambah Pendidikan')
                        ->reorderable(false)
                        ->collapsible()
                        ->collapseAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Tutup Semua'))
                        ->expandAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Buka Semua'))
                        ->itemLabel(
                            fn(array $state): ?string =>
                            filled($state['jenjang'] ?? null) && filled($state['nama_institusi'] ?? null)
                                ? "{$state['jenjang']} — {$state['nama_institusi']}"
                                : 'Pendidikan Baru'
                        )
                        ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation()),
                ]),

            // ----- STRUKTUR ORGANISASI --------------------------------------
            Forms\Components\Section::make('Struktur Organisasi')
                ->icon('heroicon-o-building-office-2')
                ->description('Riwayat organisasi dan jabatan anggota.')
                ->schema([
                    Forms\Components\Repeater::make('strukturOrganisasi')
                        ->label('')
                        ->relationship('strukturOrganisasi')
                        ->schema([
                            Forms\Components\Select::make('tipe_organisasi')
                                ->label('Tipe')
                                ->options([
                                    'internal'  => 'Internal',
                                    'eksternal' => 'Eksternal',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('level_id', null);
                                    $set('organisasi_id', null);
                                    $set('nama_organisasi', null);
                                })
                                ->columnSpan(2),

                            Forms\Components\Select::make('level_id')
                                ->label('Level (Internal)')
                                ->options(function () {
                                    return Level::all()->mapWithKeys(function ($level) {
                                        $namaLevel = $level->nama_level ?? "{$level->level_type} #{$level->id}";
                                        return [$level->id => "[{$level->level_type}] {$namaLevel}"];
                                    });
                                })
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->visible(fn(Get $get) => $get('tipe_organisasi') === 'internal')
                                ->required(fn(Get $get) => $get('tipe_organisasi') === 'internal')
                                ->columnSpan(2),

                            Forms\Components\Select::make('organisasi_id')
                                ->label('Organisasi (Eksternal)')
                                ->options(fn() => Organisasi::all()->pluck('nama_organisasi', 'id'))
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->visible(fn(Get $get) => $get('tipe_organisasi') === 'eksternal')
                                ->required(fn(Get $get) => $get('tipe_organisasi') === 'eksternal'
                                    && blank($get('nama_organisasi')))
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('nama_organisasi')
                                ->label('Atau Ketik Nama Organisasi')
                                ->placeholder('Jika tidak ada di daftar...')
                                ->nullable()
                                ->visible(fn(Get $get) => $get('tipe_organisasi') === 'eksternal')
                                ->required(fn(Get $get) => $get('tipe_organisasi') === 'eksternal'
                                    && blank($get('organisasi_id')))
                                ->helperText('Isi salah satu: pilih dari dropdown atau ketik manual.')
                                ->columnSpan(2),

                            Forms\Components\Select::make('jabatan_id')
                                ->label('Jabatan')
                                ->relationship('jabatan', 'nama_jabatan')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\DatePicker::make('masa_khidmat_mulai')
                                ->label('Mulai')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),

                            Forms\Components\DatePicker::make('masa_khidmat_selesai')
                                ->label('Selesai')
                                ->nullable()
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->after('masa_khidmat_mulai'),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger')
                                ->inline(false)
                                ->columnSpan(2),
                        ])
                        ->columns(4)
                        ->addActionLabel('+ Tambah Jabatan')
                        ->reorderable(false)
                        ->collapsible()
                        ->collapseAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Tutup Semua'))
                        ->expandAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Buka Semua'))
                        ->itemLabel(function (array $state): ?string {
                            $jabatan = filled($state['jabatan_id'] ?? null)
                                ? optional(Jabatan::find($state['jabatan_id']))->nama_jabatan
                                : null;
                            $tipe  = ($state['tipe_organisasi'] ?? '') === 'internal' ? '🏢 Internal' : '🌐 Eksternal';
                            $aktif = ($state['is_active'] ?? false) ? '✅ Aktif' : '⬜ Tidak Aktif';
                            return $jabatan
                                ? "{$jabatan} — {$tipe} — {$aktif}"
                                : "Jabatan Baru — {$aktif}";
                        })
                        ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation()),
                ]),

            // ----- SOCIAL MEDIA ---------------------------------------------
            Forms\Components\Section::make('Social Media')
                ->icon('heroicon-o-computer-desktop')
                ->description('Akun media sosial anggota.')
                ->schema([
                    Forms\Components\Repeater::make('socialMediaAccounts')
                        ->label('')
                        ->relationship('socialMediaAccounts')
                        ->schema([
                            Forms\Components\Select::make('social_media_id')
                                ->label('Platform')
                                ->options(fn() => SocialMedia::orderBy('platform_name')->pluck('platform_name', 'id')->toArray())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('username')
                                ->label('Username / Nama Akun')
                                ->maxLength(100)
                                ->nullable()
                                ->placeholder('@username')
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('url')
                                ->label('URL Profil')
                                ->url()
                                ->maxLength(255)
                                ->nullable()
                                ->placeholder('https://...')
                                ->columnSpan(2),
                        ])
                        ->columns(6)
                        ->addActionLabel('+ Tambah Akun Social Media')
                        ->reorderable(false)
                        ->collapsible()
                        ->collapseAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Tutup Semua'))
                        ->expandAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Buka Semua'))
                        ->itemLabel(function (array $state): ?string {
                            $platform = filled($state['social_media_id'] ?? null)
                                ? optional(SocialMedia::find($state['social_media_id']))->platform_name
                                : null;
                            $username = filled($state['username'] ?? null) ? $state['username'] : null;
                            return $platform
                                ? $platform . ($username ? " — {$username}" : '')
                                : 'Akun Baru';
                        })
                        ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation()),
                ]),

            // ----- DOKUMEN / FOTO -------------------------------------------
            Forms\Components\Section::make('Dokumen')
                ->icon('heroicon-o-paper-clip')
                ->schema([
                    Forms\Components\FileUpload::make('foto')
                        ->label('Foto')
                        ->image()
                        ->imageEditor()
                        ->directory('anggota/foto')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Format: JPG, PNG, WEBP. Maks 2MB.'),

                    Forms\Components\FileUpload::make('ktp')
                        ->label('Foto KTP')
                        ->image()
                        ->directory('anggota/ktp')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Format: JPG, PNG, WEBP. Maks 2MB.'),
                ])
                ->columns(2),

            // ----- STATUS VERIFIKASI ----------------------------------------
            Forms\Components\Section::make('Status Verifikasi')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Forms\Components\Select::make('status_verifikasi')
                        ->label('Status')
                        ->options([
                            'Pending'      => 'Pending',
                            'Diverifikasi' => 'Diverifikasi',
                            'Ditolak'      => 'Ditolak',
                        ])
                        ->required()
                        ->default('Pending')
                        ->live()
                        ->disabled(
                            fn(Get $get, $record): bool =>
                            $record === null || $record->status_verifikasi === 'Pending'
                        )
                        ->dehydrated(true),

                    Forms\Components\DateTimePicker::make('tanggal_verifikasi')
                        ->label('Tanggal Verifikasi')
                        ->displayFormat('d/m/Y H:i')
                        ->native(false)
                        ->nullable()
                        ->visible(fn(Get $get): bool => $get('status_verifikasi') !== 'Pending'),

                    Forms\Components\Textarea::make('catatan_verifikasi')
                        ->label('Catatan Verifikasi')
                        ->placeholder('Tuliskan alasan penolakan atau catatan lainnya...')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull()
                        ->visible(fn(Get $get): bool => $get('status_verifikasi') !== 'Pending'),
                ])
                ->columns(2),
        ]);
    }

    // =========================================================================
    // TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(asset('images/default-avatar.png'))
                    ->size(40),

                Tables\Columns\TextColumn::make('nia')
                    ->label('NIA')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('NIA disalin!')
                    ->fontFamily('mono')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'L'     => 'info',
                        'P'     => 'pink',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'L'     => 'L',
                        'P'     => 'P',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('kecamatan.nama_kecamatan')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('desa.nama_desa')
                    ->label('Desa')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nomor_hp')
                    ->label('No. HP')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pekerjaan.nama_pekerjaan')
                    ->label('Pekerjaan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('umur')
                    ->label('Umur')
                    ->getStateUsing(fn(Anggota $record): ?string => $record->umur ? $record->umur . ' thn' : '-')
                    ->sortable(query: fn(Builder $query, string $direction) => $query->orderBy('tanggal_lahir', $direction === 'asc' ? 'desc' : 'asc'))
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('jabatanAktif')
                    ->label('Jabatan Aktif')
                    ->getStateUsing(function (Anggota $record): string {
                        $aktif = $record->strukturOrganisasi()
                            ->where('is_active', true)
                            ->with(['jabatan', 'level'])
                            ->latest('masa_khidmat_mulai')
                            ->first();

                        if (! $aktif) return '—';

                        $jabatan = $aktif->jabatan?->nama_jabatan ?? '—';
                        $level   = $aktif->level?->level_type ?? '';

                        return $level ? "{$jabatan} ({$level})" : $jabatan;
                    })
                    ->badge()
                    ->color('success')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('status_verifikasi')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Diverifikasi' => 'success',
                        'Ditolak'      => 'danger',
                        default        => 'warning',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_verifikasi')
                    ->label('Status Verifikasi')
                    ->options([
                        'Pending'      => 'Pending',
                        'Diverifikasi' => 'Diverifikasi',
                        'Ditolak'      => 'Ditolak',
                    ])
                    ->placeholder('Semua Status'),

                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),

                Tables\Filters\SelectFilter::make('kecamatan_id')
                    ->label('Kecamatan')
                    ->relationship('kecamatan', 'nama_kecamatan')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('pekerjaan_id')
                    ->label('Pekerjaan')
                    ->relationship('pekerjaan', 'nama_pekerjaan')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('tanggal_lahir')
                    ->label('Rentang Usia')
                    ->form([
                        Forms\Components\TextInput::make('usia_min')->label('Usia Min')->numeric()->placeholder('Contoh: 17'),
                        Forms\Components\TextInput::make('usia_max')->label('Usia Maks')->numeric()->placeholder('Contoh: 45'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['usia_min'], fn(Builder $q, $min) =>
                            $q->whereDate('tanggal_lahir', '<=', now()->subYears((int) $min)))
                            ->when($data['usia_max'], fn(Builder $q, $max) =>
                            $q->whereDate('tanggal_lahir', '>=', now()->subYears((int) $max + 1)));
                    }),

                Tables\Filters\TernaryFilter::make('npwp_status')
                    ->label('NPWP')
                    ->trueLabel('Memiliki NPWP')
                    ->falseLabel('Tidak Memiliki NPWP'),

                Tables\Filters\TernaryFilter::make('bpjs_status')
                    ->label('BPJS')
                    ->trueLabel('Memiliki BPJS')
                    ->falseLabel('Tidak Memiliki BPJS'),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('verifikasi')
                        ->label('Verifikasi')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn(Anggota $record): bool => $record->status_verifikasi === 'Pending')
                        ->requiresConfirmation()
                        ->action(function (Anggota $record): void {
                            $record->update([
                                'status_verifikasi'  => 'Diverifikasi',
                                'tanggal_verifikasi' => now(),
                            ]);
                        }),
                    Tables\Actions\Action::make('tolak')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(Anggota $record): bool => $record->status_verifikasi === 'Pending')
                        ->form([
                            Forms\Components\Textarea::make('catatan_verifikasi')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Anggota $record, array $data): void {
                            $record->update([
                                'status_verifikasi'  => 'Ditolak',
                                'tanggal_verifikasi' => now(),
                                'catatan_verifikasi' => $data['catatan_verifikasi'],
                            ]);
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])->label('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('verifikasi_massal')
                        ->label('Verifikasi Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each(fn(Anggota $record) => $record->update([
                            'status_verifikasi'  => 'Diverifikasi',
                            'tanggal_verifikasi' => now(),
                        ]))),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    // =========================================================================
    // INFOLIST
    // =========================================================================

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Nomor Induk Anggota')
                ->icon('heroicon-o-key')
                ->schema([
                    Infolists\Components\TextEntry::make('nia')
                        ->label('NIA')
                        ->fontFamily('mono')
                        ->copyable()
                        ->copyMessage('NIA disalin!')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Data Pribadi')
                ->icon('heroicon-o-identification')
                ->schema([
                    Infolists\Components\TextEntry::make('nama_lengkap')->label('Nama Lengkap'),
                    Infolists\Components\TextEntry::make('nik')->label('NIK')->fontFamily('mono'),
                    Infolists\Components\TextEntry::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                    Infolists\Components\TextEntry::make('tempat_lahir')->label('Tempat Lahir'),
                    Infolists\Components\TextEntry::make('tanggal_lahir')->label('Tanggal Lahir')->date('d/m/Y'),
                    Infolists\Components\TextEntry::make('status_pernikahan')->label('Status Pernikahan'),
                    Infolists\Components\TextEntry::make('golongan_darah')->label('Golongan Darah'),
                    Infolists\Components\TextEntry::make('tinggi_badan')->label('Tinggi Badan')->suffix(' cm'),
                    Infolists\Components\TextEntry::make('berat_badan')->label('Berat Badan')->suffix(' kg'),
                ])
                ->columns(3),

            Infolists\Components\Section::make('Alamat')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Infolists\Components\TextEntry::make('kecamatan.nama_kecamatan')->label('Kecamatan'),
                    Infolists\Components\TextEntry::make('desa.nama_desa')->label('Desa'),
                    Infolists\Components\TextEntry::make('rt')->label('RT'),
                    Infolists\Components\TextEntry::make('rw')->label('RW'),
                    Infolists\Components\TextEntry::make('alamat_lengkap')->label('Alamat Lengkap')->columnSpanFull(),
                ])
                ->columns(4),

            Infolists\Components\Section::make('Kontak & Pekerjaan')
                ->icon('heroicon-o-phone')
                ->schema([
                    Infolists\Components\TextEntry::make('nomor_hp')->label('No. HP'),
                    Infolists\Components\TextEntry::make('alamat_email')->label('Email')->placeholder('—'),
                    Infolists\Components\TextEntry::make('pekerjaan.nama_pekerjaan')->label('Pekerjaan')->placeholder('—'),
                    Infolists\Components\TextEntry::make('politik.partai_politik')->label('Afiliasi Politik')->placeholder('—'),
                ])
                ->columns(2),

            Infolists\Components\Section::make('NPWP & BPJS')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Infolists\Components\IconEntry::make('npwp_status')->label('NPWP')->boolean(),
                    Infolists\Components\TextEntry::make('npwp_nomor')->label('Nomor NPWP')->placeholder('—')->fontFamily('mono'),
                    Infolists\Components\IconEntry::make('bpjs_status')->label('BPJS')->boolean(),
                    Infolists\Components\TextEntry::make('bpjs_nomor')->label('Nomor BPJS')->placeholder('—')->fontFamily('mono'),
                ])
                ->columns(2),

            // ----- PENDIDIKAN -----------------------------------------------
            Infolists\Components\Section::make('Riwayat Pendidikan')
                ->icon('heroicon-o-academic-cap')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('pendidikans')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('jenjang')
                                ->label('Jenjang')
                                ->badge()
                                ->color('info'),

                            Infolists\Components\TextEntry::make('nama_institusi')
                                ->label('Institusi'),

                            Infolists\Components\TextEntry::make('jurusan')
                                ->label('Jurusan')
                                ->placeholder('—'),

                            Infolists\Components\TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn(?string $state): string => match ($state) {
                                    'Lulus'           => 'success',
                                    'Tidak Lulus'     => 'danger',
                                    'Sedang Berjalan' => 'warning',
                                    default           => 'gray',
                                }),

                            Infolists\Components\TextEntry::make('tahun_masuk')
                                ->label('Tahun Masuk')
                                ->placeholder('—'),

                            Infolists\Components\TextEntry::make('tahun_lulus')
                                ->label('Tahun Lulus')
                                ->placeholder('Sekarang'),
                        ])
                        ->columns(3)
                        ->placeholder('Belum ada data pendidikan.'),
                ]),

            // ----- STRUKTUR ORGANISASI --------------------------------------
            Infolists\Components\Section::make('Struktur Organisasi')
                ->icon('heroicon-o-building-office-2')
                ->description('Riwayat jabatan dan posisi anggota dalam organisasi.')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('strukturOrganisasi')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('tipe_organisasi')
                                ->label('Tipe')
                                ->badge()
                                ->formatStateUsing(fn(?string $state): string => match ($state) {
                                    'internal'  => 'Internal',
                                    'eksternal' => 'Eksternal',
                                    default     => '—',
                                })
                                ->color(fn(?string $state): string => match ($state) {
                                    'internal'  => 'info',
                                    'eksternal' => 'warning',
                                    default     => 'gray',
                                }),

                            Infolists\Components\TextEntry::make('jabatan.nama_jabatan')
                                ->label('Jabatan')
                                ->placeholder('—'),

                            Infolists\Components\IconEntry::make('is_active')
                                ->label('Aktif')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            Infolists\Components\TextEntry::make('masa_khidmat_mulai')
                                ->label('Mulai')
                                ->date('d/m/Y'),

                            Infolists\Components\TextEntry::make('masa_khidmat_selesai')
                                ->label('Selesai')
                                ->date('d/m/Y')
                                ->placeholder('Sekarang'),

                            // Internal only
                            Infolists\Components\TextEntry::make('level.level_type')
                                ->label('Tipe Level')
                                ->badge()
                                ->color(fn(?string $state): string => match ($state) {
                                    'PC'      => 'info',
                                    'PAC'     => 'warning',
                                    'RANTING' => 'success',
                                    default   => 'gray',
                                })
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'internal'),

                            Infolists\Components\TextEntry::make('level.nama_level')
                                ->label('Nama Level')
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'internal'),

                            // Eksternal only
                            Infolists\Components\TextEntry::make('organisasi.nama_organisasi')
                                ->label('Organisasi')
                                ->badge()
                                ->color('success')
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'eksternal'
                                    && filled($record->organisasi_id)),

                            Infolists\Components\TextEntry::make('nama_organisasi')
                                ->label('Organisasi (Manual)')
                                ->badge()
                                ->color('gray')
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'eksternal'
                                    && blank($record->organisasi_id)),
                        ])
                        ->columns(4)
                        ->placeholder('Belum ada riwayat struktur organisasi.'),
                ]),

            // ----- SOCIAL MEDIA ---------------------------------------------
            Infolists\Components\Section::make('Social Media')
                ->icon('heroicon-o-computer-desktop')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('socialMediaAccounts')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('socialMedia.platform_name')
                                ->label('Platform')
                                ->badge()
                                ->color('info'),

                            Infolists\Components\TextEntry::make('username')
                                ->label('Username')
                                ->copyable()
                                ->copyMessage('Username disalin!')
                                ->placeholder('—'),

                            Infolists\Components\TextEntry::make('url')
                                ->label('URL Profil')
                                ->copyable()
                                ->copyMessage('URL disalin!')
                                ->placeholder('—')
                                ->url(fn(?string $state): ?string => $state, shouldOpenInNewTab: true),
                        ])
                        ->columns(3)
                        ->placeholder('Belum ada akun social media terdaftar.'),
                ]),

            Infolists\Components\Section::make('Status Verifikasi')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Infolists\Components\TextEntry::make('status_verifikasi')
                        ->label('Status')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Diverifikasi' => 'success',
                            'Ditolak'      => 'danger',
                            default        => 'warning',
                        }),
                    Infolists\Components\TextEntry::make('tanggal_verifikasi')
                        ->label('Tanggal Verifikasi')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('catatan_verifikasi')
                        ->label('Catatan')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    // =========================================================================
    // RELATIONS & PAGES
    // =========================================================================

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnggotas::route('/'),
            'create' => Pages\CreateAnggota::route('/create'),
            'view'   => Pages\ViewAnggota::route('/{record}'),
            'edit'   => Pages\EditAnggota::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_lengkap', 'nik', 'nomor_hp', 'alamat_email', 'nia'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'NIA'    => $record->nia ?? '—',
            'NIK'    => $record->nik,
            'Status' => $record->status_verifikasi,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'kecamatan',
                'desa',
                'pekerjaan',
                'strukturOrganisasi.jabatan',
                'strukturOrganisasi.level',
                'strukturOrganisasi.organisasi',
                'pendidikans',
                'socialMediaAccounts.socialMedia',
            ]);
    }
}
