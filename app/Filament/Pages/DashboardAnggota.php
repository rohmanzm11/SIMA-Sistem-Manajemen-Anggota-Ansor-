<?php

namespace App\Filament\Pages;

use App\Models\Anggota;
use App\Models\AnggotaPelatihan;
use App\Models\AnggotaSocialMedia;
use App\Models\Desa;
use App\Models\Jabatan;
use App\Models\Kecamatan;
use App\Models\Kta;
use App\Models\Level;
use App\Models\Organisasi;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\PelatihanDetail;
use App\Models\Politik;
use App\Models\PrintLog;
use App\Models\SocialMedia;
use App\Models\StrukturOrganisasi;
use App\Models\TemplateSertifikat;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class DashboardAnggota extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Dashboard Anggota';
    protected static ?string $title           = 'Dashboard Anggota';
    protected static bool    $shouldRegisterNavigation = false;
    protected static string  $view            = 'filament.pages.dashboard-anggota';

    protected $queryString = ['anggotaId'];

    public ?int     $anggotaId = null;
    public ?Anggota $anggota   = null;

    // Form states
    public ?array $formData          = [];
    public ?array $profileFormData   = [];
    public ?array $pendidikanData    = [];
    public ?array $strukturData      = [];
    public ?array $sosmedData        = [];

    // Editing IDs
    public ?int $editingId           = null;
    public ?int $editingPendidikanId = null;
    public ?int $editingStrukturId   = null;
    public ?int $editingSosmedId     = null;

    // ----------------------------------------------------------------
    // Mount
    // ----------------------------------------------------------------
    public function mount(?int $anggotaId = null): void
    {
        $this->anggotaId = $anggotaId ?? $this->anggotaId;

        if (blank($this->anggotaId)) {
            $this->redirect(DaftarAnggota::getUrl());
            return;
        }

        $this->refreshAnggota();
        $this->form->fill([]);
    }

    // ================================================================
    // FORMS
    // ================================================================

    // ----------------------------------------------------------------
    // 1. Form Pelatihan (tambah/edit kehadiran)
    // ----------------------------------------------------------------
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pelatihan_detail_id')
                    ->label('Sesi Pelatihan')
                    ->options(function () {
                        return PelatihanDetail::with('pelatihan', 'materi')
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($detail) {
                                $label = ($detail->pelatihan->nama_pelatihan ?? '—')
                                    . ' — ' . ($detail->materi->nama_materi ?? '—')
                                    . ($detail->tanggal
                                        ? ' (' . \Carbon\Carbon::parse($detail->tanggal)->format('d/m/Y') . ')'
                                        : '');
                                return [$detail->id => $label];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->columnSpan(2),

                Forms\Components\Select::make('status_kehadiran')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir'       => 'Hadir',
                        'Tidak Hadir' => 'Tidak Hadir',
                        'Izin'        => 'Izin',
                        'Sakit'       => 'Sakit',
                    ])
                    ->default('Hadir')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn($state, Set $set) => $set('skor', match ($state) {
                        'Hadir'       => 100,
                        'Tidak Hadir' => 0,
                        'Izin'        => 50,
                        'Sakit'       => 50,
                        default       => null,
                    }))
                    ->columnSpan(1),

                Forms\Components\TextInput::make('skor')
                    ->label('Skor')
                    ->numeric()
                    ->visible(false)
                    ->dehydrated(true)
                    ->columnSpan(1),
            ])
            ->columns(2)
            ->statePath('formData');
    }

    // ----------------------------------------------------------------
    // 2. Form Edit Profil (lengkap sesuai form Peserta)
    // ----------------------------------------------------------------
    public function editProfilForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas')
                    ->schema([
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->numeric()
                            ->maxLength(16)
                            ->columnSpan(1),

                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Forms\Components\Select::make('status_pernikahan')
                            ->label('Status Pernikahan')
                            ->options([
                                'Belum Menikah' => 'Belum Menikah',
                                'Menikah'       => 'Menikah',
                                'Cerai Hidup'   => 'Cerai Hidup',
                                'Cerai Mati'    => 'Cerai Mati',
                            ])
                            ->columnSpan(1),

                        Forms\Components\Select::make('golongan_darah')
                            ->label('Golongan Darah')
                            ->options(['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O', 'Tidak Tahu' => 'Tidak Tahu'])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tinggi_badan')
                            ->label('Tinggi Badan (cm)')
                            ->numeric()
                            ->suffix('cm')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('berat_badan')
                            ->label('Berat Badan (kg)')
                            ->numeric()
                            ->suffix('kg')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kontak & Pekerjaan')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_hp')
                            ->label('Nomor HP')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('alamat_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('pekerjaan_id')
                            ->label('Pekerjaan')
                            ->options(fn() => Pekerjaan::orderBy('nama_pekerjaan')->pluck('nama_pekerjaan', 'id'))
                            ->searchable()
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('politik_id')
                            ->label('Afiliasi Politik')
                            ->options(fn() => Politik::orderBy('partai_politik')->pluck('partai_politik', 'id'))
                            ->searchable()
                            ->nullable()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Alamat')
                    ->schema([
                        Forms\Components\Select::make('kecamatan_id')
                            ->label('Kecamatan')
                            ->options(fn() => Kecamatan::orderBy('nama_kecamatan')->pluck('nama_kecamatan', 'id'))
                            ->searchable()
                            ->preload()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set) => $set('desa_id', null))
                            ->columnSpan(1),

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
                            ->live(onBlur: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('rt')
                            ->label('RT')
                            ->maxLength(5)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('rw')
                            ->label('RW')
                            ->maxLength(5)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('alamat_lengkap')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('NPWP & BPJS')
                    ->schema([
                        Forms\Components\Toggle::make('npwp_status')
                            ->label('Punya NPWP?')
                            ->live()
                            ->afterStateUpdated(fn(Set $set, bool $state) => !$state ? $set('npwp_nomor', null) : null)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('npwp_nomor')
                            ->label('Nomor NPWP')
                            ->maxLength(50)
                            ->visible(fn(Get $get) => (bool) $get('npwp_status'))
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('bpjs_status')
                            ->label('Punya BPJS?')
                            ->live()
                            ->afterStateUpdated(fn(Set $set, bool $state) => !$state ? $set('bpjs_nomor', null) : null)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('bpjs_nomor')
                            ->label('Nomor BPJS')
                            ->maxLength(50)
                            ->visible(fn(Get $get) => (bool) $get('bpjs_status'))
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ])
            ->statePath('profileFormData');
    }

    // ----------------------------------------------------------------
    // 3. Form Pendidikan
    // ----------------------------------------------------------------
    public function pendidikanForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenjang')
                    ->label('Jenjang')
                    ->options([
                        'SD'        => 'SD',
                        'SMP'       => 'SMP',
                        'SMA'       => 'SMA',
                        'SMK'       => 'SMK',
                        'MA'        => 'MA',
                        'Pesantren' => 'Pesantren',
                        'Diniyyah'  => 'Diniyyah',
                        'D1'        => 'D1',
                        'D2'        => 'D2',
                        'D3'        => 'D3',
                        'D4'        => 'D4',
                        'S1'        => 'S1',
                        'S2'        => 'S2',
                        'S3'        => 'S3',
                    ])
                    ->native(false)
                    ->required()
                    ->live()
                    ->columnSpan(1),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Lulus'            => 'Lulus',
                        'Tidak Lulus'      => 'Tidak Lulus',
                        'Sedang Berjalan'  => 'Sedang Berjalan',
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
            ->columns(2)
            ->statePath('pendidikanData');
    }

    // ----------------------------------------------------------------
    // 4. Form Struktur Organisasi
    // ----------------------------------------------------------------
    public function strukturForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipe_organisasi')
                    ->label('Tipe Organisasi')
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
                    ->options(fn() => Jabatan::orderBy('nama_jabatan')->pluck('nama_jabatan', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                Forms\Components\DatePicker::make('masa_khidmat_mulai')
                    ->label('Mulai')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->columnSpan(1),

                Forms\Components\DatePicker::make('masa_khidmat_selesai')
                    ->label('Selesai')
                    ->nullable()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->after('masa_khidmat_mulai')
                    ->columnSpan(1),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false)
                    ->columnSpan(2),
            ])
            ->columns(2)
            ->statePath('strukturData');
    }

    // ----------------------------------------------------------------
    // 5. Form Social Media
    // ----------------------------------------------------------------
    public function sosmedForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('social_media_id')
                    ->label('Platform')
                    ->options(fn() => SocialMedia::orderBy('platform_name')->pluck('platform_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

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
                    ->columnSpan(1),
            ])
            ->columns(2)
            ->statePath('sosmedData');
    }

    // ================================================================
    // ACTIONS — Pelatihan
    // ================================================================
    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->form->fill([
            'status_kehadiran' => 'Hadir',
            'skor'             => 100,
        ]);
        $this->dispatch('open-modal', id: 'pelatihan-modal');
    }

    public function openEditModal(int $id): void
    {
        $record          = AnggotaPelatihan::findOrFail($id);
        $this->editingId = $id;
        $this->form->fill($record->toArray());
        $this->dispatch('open-modal', id: 'pelatihan-modal');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $data['skor'] = match ($data['status_kehadiran'] ?? null) {
            'Hadir'       => 100,
            'Tidak Hadir' => 0,
            'Izin'        => 50,
            'Sakit'       => 50,
            default       => null,
        };

        if ($this->editingId) {
            AnggotaPelatihan::findOrFail($this->editingId)->update($data);
            Notification::make()->title('Kehadiran diperbarui')->success()->send();
        } else {
            AnggotaPelatihan::create(array_merge($data, ['anggota_id' => $this->anggotaId]));
            Notification::make()->title('Kehadiran ditambahkan')->success()->send();
        }

        $this->dispatch('close-modal', id: 'pelatihan-modal');
        $this->refreshAnggota();
        $this->form->fill([]);
        $this->editingId = null;
    }

    public function delete(int $id): void
    {
        AnggotaPelatihan::findOrFail($id)->delete();
        Notification::make()->title('Data dihapus')->danger()->send();
        $this->refreshAnggota();
    }

    // ================================================================
    // ACTIONS — Edit Profil
    // ================================================================
    public function openEditProfilModal(): void
    {
        $this->editProfilForm->fill($this->anggota->toArray());
        $this->dispatch('open-modal', id: 'profil-modal');
    }

    public function saveProfil(): void
    {
        $data = $this->editProfilForm->getState();
        $this->anggota->update($data);
        Notification::make()->title('Data pribadi berhasil diperbarui')->success()->send();
        $this->dispatch('close-modal', id: 'profil-modal');
        $this->refreshAnggota();
    }

    // ================================================================
    // ACTIONS — Pendidikan
    // ================================================================
    public function openCreatePendidikanModal(): void
    {
        $this->editingPendidikanId = null;
        $this->pendidikanData = []; // reset manual

        $this->pendidikanForm->fill([]);
        $this->dispatch('open-modal', id: 'pendidikan-modal');
    }

    public function openEditPendidikanModal(int $id): void
    {
        $record                    = Pendidikan::findOrFail($id);
        $this->editingPendidikanId = $id;
        $this->pendidikanForm->fill($record->toArray());
        $this->dispatch('open-modal', id: 'pendidikan-modal');
    }

    public function savePendidikan(): void
    {
        $data = $this->pendidikanForm->getState();

        if ($this->editingPendidikanId) {
            Pendidikan::findOrFail($this->editingPendidikanId)->update($data);
            Notification::make()->title('Pendidikan diperbarui')->success()->send();
        } else {
            $this->anggota->pendidikans()->create($data);
            Notification::make()->title('Pendidikan ditambahkan')->success()->send();
        }

        $this->dispatch('close-modal', id: 'pendidikan-modal');
        $this->refreshAnggota();
        $this->pendidikanForm->fill([]);
        $this->editingPendidikanId = null;
    }

    public function deletePendidikan(int $id): void
    {
        Pendidikan::findOrFail($id)->delete();
        Notification::make()->title('Pendidikan dihapus')->danger()->send();
        $this->refreshAnggota();
    }

    // ================================================================
    // ACTIONS — Struktur Organisasi
    // ================================================================
    public function openCreateStrukturModal(): void
    {
        $this->editingStrukturId = null;
        $this->strukturForm->fill(['is_active' => true]);
        $this->dispatch('open-modal', id: 'struktur-modal');
    }

    public function openEditStrukturModal(int $id): void
    {
        $record                  = StrukturOrganisasi::findOrFail($id);
        $this->editingStrukturId = $id;
        $this->strukturForm->fill($record->toArray());
        $this->dispatch('open-modal', id: 'struktur-modal');
    }

    public function saveStruktur(): void
    {
        $data = $this->strukturForm->getState();

        if ($this->editingStrukturId) {
            StrukturOrganisasi::findOrFail($this->editingStrukturId)->update($data);
            Notification::make()->title('Jabatan diperbarui')->success()->send();
        } else {
            $this->anggota->strukturOrganisasi()->create($data);
            Notification::make()->title('Jabatan ditambahkan')->success()->send();
        }

        $this->dispatch('close-modal', id: 'struktur-modal');
        $this->refreshAnggota();
        $this->strukturForm->fill([]);
        $this->editingStrukturId = null;
    }

    public function deleteStruktur(int $id): void
    {
        StrukturOrganisasi::findOrFail($id)->delete();
        Notification::make()->title('Jabatan dihapus')->danger()->send();
        $this->refreshAnggota();
    }

    // ================================================================
    // ACTIONS — Social Media
    // ================================================================
    public function openCreateSosmedModal(): void
    {
        $this->editingSosmedId = null;
        $this->sosmedForm->fill([]);
        $this->dispatch('open-modal', id: 'sosmed-modal');
    }

    public function openEditSosmedModal(int $id): void
    {
        $record                = AnggotaSocialMedia::findOrFail($id);
        $this->editingSosmedId = $id;
        $this->sosmedForm->fill($record->toArray());
        $this->dispatch('open-modal', id: 'sosmed-modal');
    }

    public function saveSosmed(): void
    {
        $data = $this->sosmedForm->getState();

        if ($this->editingSosmedId) {
            AnggotaSocialMedia::findOrFail($this->editingSosmedId)->update($data);
            Notification::make()->title('Social media diperbarui')->success()->send();
        } else {
            $this->anggota->socialMediaAccounts()->create($data);
            Notification::make()->title('Social media ditambahkan')->success()->send();
        }

        $this->dispatch('close-modal', id: 'sosmed-modal');
        $this->refreshAnggota();
        $this->sosmedForm->fill([]);
        $this->editingSosmedId = null;
    }

    public function deleteSosmed(int $id): void
    {
        AnggotaSocialMedia::findOrFail($id)->delete();
        Notification::make()->title('Social media dihapus')->danger()->send();
        $this->refreshAnggota();
    }

    // ================================================================
    // KTA & SERTIFIKAT Helpers (tidak berubah)
    // ================================================================
    public function getKtaPrintLog(): ?PrintLog
    {
        return PrintLog::with(['anggota.kecamatan', 'anggota.desa', 'kta'])
            ->where('anggota_id', $this->anggotaId)
            ->where('jenis_cetakan', 'KTA')
            ->latest()
            ->first();
    }

    public function getKtaTemplate(): ?Kta
    {
        $printLog = $this->getKtaPrintLog();
        $kta      = $printLog?->kta;

        if (! $kta || empty($kta->image)) {
            $fallback = Kta::where('is_active', true)->latest()->first()
                ?? Kta::latest()->first();

            if ($fallback) {
                if (! $kta) {
                    $kta = $fallback;
                } else {
                    $kta->image = $fallback->image;
                }
            }
        }

        return $kta;
    }

    public function toBase64(?string $value): ?string
    {
        if (empty($value)) return null;

        $content = null;
        $ext     = strtolower(pathinfo($value, PATHINFO_EXTENSION));

        try {
            if (Storage::disk('public')->exists($value)) {
                $content = Storage::disk('public')->get($value);
            }
        } catch (\Exception) {
        }

        if ($content === null) {
            foreach (
                [
                    storage_path('app/public/' . $value),
                    public_path('storage/' . $value),
                    public_path($value),
                    storage_path('app/' . $value),
                    public_path('storage/kta-images/' . basename($value)),
                    storage_path('app/public/kta-images/' . basename($value)),
                    public_path('storage/ttd-kta/' . basename($value)),
                    storage_path('app/public/ttd-kta/' . basename($value)),
                    public_path('storage/sertifikat/' . basename($value)),
                    storage_path('app/public/sertifikat/' . basename($value)),
                    public_path('storage/ttd-sertifikat/' . basename($value)),
                    storage_path('app/public/ttd-sertifikat/' . basename($value)),
                ] as $path
            ) {
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    break;
                }
            }
        }

        if ($content === null) return null;

        $mime = match ($ext) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
            default => 'image/jpeg',
        };

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    public function getKtaDownloadUrl(): ?string
    {
        $printLog = $this->getKtaPrintLog();
        return $printLog ? url('/kta/pdf/' . $printLog->id) : null;
    }

    public function getSertifikatPrintLogs(): \Illuminate\Support\Collection
    {
        return PrintLog::with([
            'pelatihanDetail.pelatihan.pelatihanDetails.materi',
            'templateSertifikat',
        ])
            ->where('anggota_id', $this->anggotaId)
            ->where('jenis_cetakan', 'Sertifikat')
            ->latest()
            ->get();
    }

    public function getLatestSertifikatPrintLog(): ?PrintLog
    {
        return PrintLog::with([
            'pelatihanDetail.pelatihan.pelatihanDetails.materi',
            'templateSertifikat',
        ])
            ->where('anggota_id', $this->anggotaId)
            ->where('jenis_cetakan', 'Sertifikat')
            ->latest()
            ->first();
    }

    public function getSertifikatPreviewData(): array
    {
        $printLog = $this->getLatestSertifikatPrintLog();

        if (! $printLog) {
            return [
                'printLog'   => null,
                'pelatihan'  => null,
                'detail'     => null,
                'template'   => null,
                'materiList' => collect(),
            ];
        }

        $materiList = collect();
        if ($printLog->pelatihanDetail && $printLog->pelatihanDetail->pelatihan) {
            $materiList = $printLog->pelatihanDetail->pelatihan
                ->pelatihanDetails()
                ->with('materi')
                ->orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->get();
        }

        return [
            'printLog'   => $printLog,
            'pelatihan'  => $printLog->pelatihanDetail?->pelatihan,
            'detail'     => $printLog->pelatihanDetail,
            'template'   => $printLog->templateSertifikat,
            'materiList' => $materiList,
        ];
    }

    public function getSertifikatDownloadUrl(): ?string
    {
        $printLog = $this->getLatestSertifikatPrintLog();
        return $printLog ? url('/print-log/' . $printLog->id) : null;
    }

    public function getAnggotaPelatihanForPrintLog(PrintLog $printLog): ?AnggotaPelatihan
    {
        if (! $printLog->pelatihan_detail_id) return null;

        return AnggotaPelatihan::where('anggota_id', $this->anggotaId)
            ->where('pelatihan_detail_id', $printLog->pelatihan_detail_id)
            ->first();
    }

    // ================================================================
    // Helpers
    // ================================================================
    private function refreshAnggota(): void
    {
        $this->anggota = Anggota::with([
            'kecamatan',
            'desa',
            'pekerjaan',
            'politik',
            'strukturOrganisasi.jabatan',
            'strukturOrganisasi.level',
            'strukturOrganisasi.organisasi',
            'pendidikans',
            'socialMediaAccounts.socialMedia',
            'pelatihanRecords.pelatihanDetail.pelatihan',
            'pelatihanRecords.pelatihanDetail.materi',
        ])->findOrFail($this->anggotaId);
    }

    public function getStatistik(): array
    {
        $records = $this->anggota->pelatihanRecords;

        return [
            'total_sesi'       => $records->count(),
            'hadir'            => $records->where('status_kehadiran', 'Hadir')->count(),
            'tidak_hadir'      => $records->where('status_kehadiran', 'Tidak Hadir')->count(),
            'izin'             => $records->where('status_kehadiran', 'Izin')->count(),
            'sakit'            => $records->where('status_kehadiran', 'Sakit')->count(),
            'rata_skor'        => $records->whereNotNull('skor')->avg('skor'),
            'sertifikat'       => $records->whereNotNull('sertifikat_nomor')->count(),
            'persentase_hadir' => $records->count() > 0
                ? round(($records->where('status_kehadiran', 'Hadir')->count() / $records->count()) * 100)
                : 0,
        ];
    }

    protected function getForms(): array
    {
        return [
            'form',
            'editProfilForm',
            'pendidikanForm',
            'strukturForm',
            'sosmedForm',
        ];
    }
}
