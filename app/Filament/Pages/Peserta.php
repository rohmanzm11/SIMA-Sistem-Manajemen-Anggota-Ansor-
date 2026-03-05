<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AnggotaResource;
use App\Models\Anggota;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Level;
use App\Models\Jabatan;
use App\Models\Organisasi;
use App\Models\Pekerjaan;
use App\Models\Politik;
use App\Models\SocialMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Peserta extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static string $view = 'filament.pages.peserta';

    protected static ?string $title = 'Formulir  Peserta';

    protected static ?string $navigationLabel = 'Formulir Peserta';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // ----- DATA PRIBADI -----------------------------------------
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
                            ->unique(table: 'anggotas', column: 'nik')
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

                // ----- ALAMAT -----------------------------------------------
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
                            ->afterStateUpdated(function (Set $set) {
                                $set('desa_id', null);
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
                            ->live(onBlur: true),

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

                // ----- KONTAK & PEKERJAAN -----------------------------------
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
                            ->unique(table: 'anggotas', column: 'alamat_email')
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

                // ----- NPWP & BPJS ------------------------------------------
                Forms\Components\Section::make('NPWP & BPJS')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Toggle::make('npwp_status')
                            ->label('Memiliki NPWP')
                            ->live()
                            ->afterStateUpdated(fn(Set $set, bool $state) => !$state ? $set('npwp_nomor', null) : null),

                        Forms\Components\TextInput::make('npwp_nomor')
                            ->label('Nomor NPWP')
                            ->maxLength(20)
                            ->nullable()
                            ->visible(fn(Get $get): bool => (bool) $get('npwp_status'))
                            ->required(fn(Get $get): bool => (bool) $get('npwp_status')),

                        Forms\Components\Toggle::make('bpjs_status')
                            ->label('Memiliki BPJS')
                            ->live()
                            ->afterStateUpdated(fn(Set $set, bool $state) => !$state ? $set('bpjs_nomor', null) : null),

                        Forms\Components\TextInput::make('bpjs_nomor')
                            ->label('Nomor BPJS')
                            ->maxLength(20)
                            ->nullable()
                            ->visible(fn(Get $get): bool => (bool) $get('bpjs_status'))
                            ->required(fn(Get $get): bool => (bool) $get('bpjs_status')),
                    ])
                    ->columns(2),

                // ----- PENDIDIKAN -------------------------------------------
                Forms\Components\Section::make('Riwayat Pendidikan')
                    ->icon('heroicon-o-academic-cap')
                    ->description('Isi riwayat pendidikan formal maupun non-formal.')
                    ->schema([
                        Forms\Components\Repeater::make('pendidikans')
                            ->label('')
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
                                    ->searchable(),

                                Forms\Components\TextInput::make('nama_institusi')
                                    ->label('Nama Institusi')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: SMAN 1 Kudus'),

                                Forms\Components\TextInput::make('jurusan')
                                    ->label('Jurusan / Program Studi')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->placeholder('Kosongkan jika tidak ada'),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Lulus'      => 'Lulus',
                                        'Tidak Lulus' => 'Tidak Lulus',
                                        'Sedang Berjalan' => 'Sedang Berjalan',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('tahun_masuk')
                                    ->label('Tahun Masuk')
                                    ->numeric()
                                    ->minValue(1950)
                                    ->maxValue(now()->year)
                                    ->required(),

                                Forms\Components\TextInput::make('tahun_lulus')
                                    ->label('Tahun Lulus')
                                    ->numeric()
                                    ->minValue(1950)
                                    ->maxValue(now()->year + 10)
                                    ->nullable(),
                            ])
                            ->columns(3)
                            ->addActionLabel('+ Tambah Pendidikan')
                            ->reorderable(false)
                            ->collapsible()
                            ->collapseAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Tutup Semua'))
                            ->expandAllAction(fn(Forms\Components\Actions\Action $action) => $action->label('Buka Semua'))
                            ->itemLabel(fn(array $state): ?string => filled($state['jenjang'] ?? null) && filled($state['nama_institusi'] ?? null)
                                ? "{$state['jenjang']} — {$state['nama_institusi']}"
                                : 'Pendidikan Baru')
                            ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation()),
                    ]),

                // ----- STRUKTUR ORGANISASI ----------------------------------
                Forms\Components\Section::make('Struktur Organisasi')
                    ->icon('heroicon-o-building-office-2')
                    ->description('Riwayat organisasi dan jabatan.')
                    ->schema([
                        Forms\Components\Repeater::make('strukturOrganisasi')
                            ->label('')
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
                                    ->options(fn() => Jabatan::orderBy('nama_jabatan')->pluck('nama_jabatan', 'id'))  // ← ganti ini
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
                                $tipe = $state['tipe_organisasi'] === 'internal' ? '🏢 Internal' : '🌐 Eksternal';
                                $aktif = ($state['is_active'] ?? false) ? '✅ Aktif' : '⬜ Tidak Aktif';
                                return $jabatan
                                    ? "{$jabatan} — {$tipe} — {$aktif}"
                                    : "Jabatan Baru — {$tipe}";
                            })
                            ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation()),
                    ]),

                // ----- SOCIAL MEDIA -----------------------------------------
                Forms\Components\Section::make('Social Media')
                    ->icon('heroicon-o-computer-desktop')
                    ->description('Akun media sosial yang dimiliki.')
                    ->schema([
                        Forms\Components\Repeater::make('socialMediaAccounts')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('social_media_id')
                                    ->label('Platform')
                                    ->options(fn() => SocialMedia::orderBy('platform_name')->pluck('platform_name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('username')
                                    ->label('Username')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->placeholder('@username')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('url')
                                    ->label('URL Profil')
                                    ->url()
                                    ->maxLength(255)
                                    ->nullable()
                                    ->placeholder('https://')
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->addActionLabel('+ Tambah Akun')
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
                                    ? ($username ? "{$platform} — {$username}" : $platform)
                                    : 'Akun Baru';
                            })
                            ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation()),
                    ]),

                // ----- DOKUMEN / FOTO ---------------------------------------
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

            ])
            ->statePath('data');
    }

    // -------------------------------------------------------------------------
    // SUBMIT
    // -------------------------------------------------------------------------

    public function submit(): void
    {
        $validated = $this->form->getState();

        $anggota = DB::transaction(function () use ($validated) {
            // Pisahkan relasi dari data utama
            $pendidikans         = $validated['pendidikans'] ?? [];
            $strukturOrganisasi  = $validated['strukturOrganisasi'] ?? [];
            $socialMediaAccounts = $validated['socialMediaAccounts'] ?? [];

            unset(
                $validated['pendidikans'],
                $validated['strukturOrganisasi'],
                $validated['socialMediaAccounts'],
            );

            /** @var Anggota $anggota */
            $anggota = Anggota::create(array_merge($validated, [
                'status_verifikasi' => 'Pending',
            ]));

            // Generate NIA
            $anggota->nia = AnggotaResource::generateNia($anggota);
            $anggota->save();

            // Simpan relasi pendidikan
            foreach ($pendidikans as $pendidikan) {
                $anggota->pendidikans()->create($pendidikan);
            }

            // Simpan struktur organisasi
            foreach ($strukturOrganisasi as $struktur) {
                $anggota->strukturOrganisasi()->create($struktur);
            }

            // Simpan social media
            foreach ($socialMediaAccounts as $sosmed) {
                $anggota->socialMediaAccounts()->create($sosmed);
            }

            return $anggota;
        });

        Notification::make()
            ->title('Pendaftaran Berhasil')
            ->body("Data {$anggota->nama_lengkap} berhasil disimpan. NIA: {$anggota->nia}")
            ->success()
            ->send();

        $this->redirect(
            DashboardAnggota::getUrl(['anggotaId' => $anggota->id])
        );
    }
}
