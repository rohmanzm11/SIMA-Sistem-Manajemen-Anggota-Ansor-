<?php

namespace App\Filament\Pages;

use App\Models\Anggota;
use App\Models\AnggotaPelatihan;
use App\Models\Pelatihan;
use App\Models\PelatihanDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Halaman "Daftar Pelatihan"
 *
 * Struktur data:
 *  - pelatihans         : nama_pelatihan, tempat, tanggal_mulai, tanggal_selesai, deskripsi
 *  - pelatihan_details  : pelatihan_id, materi_id, pengajar, is_active
 *  - materis            : nama_materi, deskripsi, skor_maksimal
 *
 * Alur:
 *  1. Tampilkan daftar PELATIHAN yang belum berakhir (materi tampil di bawahnya).
 *  2. Pengguna klik "Ikuti Pelatihan" → modal input NIK/NIA + Nama + Tanggal Lahir.
 *  3. Jika SUDAH terdaftar sebagai anggota DAN verifikasi 3 data cocok:
 *       - Auto-input AnggotaPelatihan untuk SEMUA materi aktif pelatihan tersebut
 *         (skip yang sudah ada agar tidak duplikat).
 *       - Redirect ke DashboardAnggota.
 *  4. Jika BELUM terdaftar → redirect ke Peserta (form pendaftaran anggota).
 *  5. Rate limiting: maksimal 5 percobaan gagal per user per 10 menit.
 */
class DaftarPelatihan extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Daftar Pelatihan';
    protected static ?string $title           = 'Daftar Pelatihan';
    // protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.daftar-pelatihan';

    // ----------------------------------------------------------------
    // State
    // ----------------------------------------------------------------

    /** ID Pelatihan yang dipilih */
    public ?int $selectedPelatihanId = null;

    /** Data form verifikasi anggota */
    public ?array $cariData = [];

    // ----------------------------------------------------------------
    // Mount
    // ----------------------------------------------------------------
    public function mount(): void
    {
        $this->form->fill([]);
    }

    // ----------------------------------------------------------------
    // Form NIK / NIA + Nama Lengkap + Tanggal Lahir
    // ----------------------------------------------------------------
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kata_kunci')
                    ->label('NIK atau NIA')
                    ->placeholder('Masukkan NIK (16 digit) atau NIA Anda')
                    ->required()
                    ->maxLength(30)
                    ->helperText('Masukkan NIK atau NIA yang terdaftar.')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('nama_verifikasi')
                    ->label('Nama Lengkap')
                    ->placeholder('Masukkan nama lengkap sesuai data pendaftaran')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Nama harus sesuai persis dengan data yang terdaftar.')
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('tanggal_lahir_verifikasi')
                    ->label('Tanggal Lahir')
                    ->placeholder('Pilih tanggal lahir Anda')
                    ->required()
                    ->maxDate(now()->toDateString())
                    ->helperText('Tanggal lahir harus sesuai dengan data yang terdaftar.')
                    ->columnSpanFull(),
            ])
            ->statePath('cariData');
    }

    // ----------------------------------------------------------------
    // Ambil daftar pelatihan yang belum berakhir (tanggal_selesai >= hari ini)
    // dan memiliki minimal satu materi aktif.
    // Materi aktif dimuat lewat relasi pelatihanDetails → materi.
    // ----------------------------------------------------------------
    public function getPelatihanList(): \Illuminate\Support\Collection
    {
        $hariIni = now()->toDateString();

        return Pelatihan::with([
            'pelatihanDetails' => fn($q) => $q
                ->where('is_active', true)
                ->orderBy('id')
                ->with('materi'),
        ])
            ->whereHas('pelatihanDetails', fn($q) => $q->where('is_active', true))
            ->where(function ($q) use ($hariIni) {
                $q->whereDate('tanggal_selesai', '>=', $hariIni)
                    // Jaga-jaga jika tanggal_selesai kosong: pakai tanggal_mulai
                    ->orWhere(function ($q2) use ($hariIni) {
                        $q2->whereNull('tanggal_selesai')
                            ->whereDate('tanggal_mulai', '>=', $hariIni);
                    });
            })
            ->orderBy('tanggal_mulai')
            ->get();
    }

    // ----------------------------------------------------------------
    // Aksi: klik "Ikuti Pelatihan" pada card
    // ----------------------------------------------------------------
    public function ikutiPelatihan(int $pelatihanId): void
    {
        $this->selectedPelatihanId = $pelatihanId;
        $this->form->fill([]);
        $this->dispatch('open-modal', id: 'cari-anggota-modal');
    }

    // ----------------------------------------------------------------
    // Rate limiter key berdasarkan user login atau IP
    // ----------------------------------------------------------------
    private function rateLimiterKey(): string
    {
        $userId = Auth::id() ?? 'guest';
        $ip     = request()->ip();
        return "daftar-pelatihan-verifikasi:{$userId}:{$ip}";
    }

    // ----------------------------------------------------------------
    // Aksi: proses verifikasi NIK/NIA + Nama + Tanggal Lahir
    // ----------------------------------------------------------------
    public function prosesVerifikasi(): void
    {
        $limiterKey = $this->rateLimiterKey();

        // ── Rate limit: max 5 percobaan gagal per 10 menit ──
        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            $seconds = RateLimiter::availableIn($limiterKey);
            $menit   = ceil($seconds / 60);

            Notification::make()
                ->title('Terlalu banyak percobaan')
                ->body("Anda telah gagal verifikasi terlalu banyak. Coba lagi dalam {$menit} menit.")
                ->danger()
                ->send();
            return;
        }

        $data              = $this->form->getState();
        $keyword           = trim($data['kata_kunci'] ?? '');
        $namaInput         = trim($data['nama_verifikasi'] ?? '');
        $tanggalLahirInput = $data['tanggal_lahir_verifikasi'] ?? null;

        // ── Cari anggota berdasarkan NIK atau NIA saja ──
        // Sengaja tidak pakai nama/tgl lahir di query agar pesan error tidak bocorkan data
        $anggota = Anggota::where('nia', $keyword)
            ->orWhere('nik', $keyword)
            ->first();

        // ── Jika tidak ditemukan sama sekali ──
        if (! $anggota) {
            RateLimiter::hit($limiterKey, 600); // +1 hit, expire 10 menit

            Notification::make()
                ->title('Data tidak ditemukan')
                ->body('NIK/NIA, nama, atau tanggal lahir tidak cocok. Periksa kembali data Anda, atau daftarkan diri sebagai anggota baru.')
                ->warning()
                ->send();

            $this->dispatch('close-modal', id: 'cari-anggota-modal');
            $this->redirect(Peserta::getUrl());
            return;
        }

        // ── Verifikasi 1: Nama Lengkap (toleransi typo 90%) ──
        $namaTerdaftar  = mb_strtolower(preg_replace('/\s+/', ' ', trim($anggota->nama_lengkap)));
        $namaInputClean = mb_strtolower(preg_replace('/\s+/', ' ', $namaInput));
        similar_text($namaTerdaftar, $namaInputClean, $persenNama);

        // ── Verifikasi 2: Tanggal Lahir (exact match) ──
        $tglLahirTerdaftar = $anggota->tanggal_lahir
            ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->toDateString()
            : null;
        $tglLahirInput = $tanggalLahirInput
            ? \Carbon\Carbon::parse($tanggalLahirInput)->toDateString()
            : null;

        $namaValid = $persenNama >= 90;
        $tglValid  = $tglLahirTerdaftar && $tglLahirInput && $tglLahirTerdaftar === $tglLahirInput;

        // ── Jika salah satu tidak cocok → tolak dengan pesan generik ──
        if (! $namaValid || ! $tglValid) {
            RateLimiter::hit($limiterKey, 600); // +1 hit

            Notification::make()
                ->title('Verifikasi gagal')
                ->body('NIK/NIA, nama, atau tanggal lahir tidak cocok. Periksa kembali data Anda.')
                ->danger()
                ->send();

            // Jangan redirect, jangan tutup modal → biarkan user coba lagi
            return;
        }

        // ── Verifikasi berhasil → reset rate limiter ──
        RateLimiter::clear($limiterKey);

        // ── Auto-input semua materi aktif dari pelatihan yang dipilih ──
        $namaLatihan = '—';
        $inserted    = 0;

        if ($this->selectedPelatihanId) {
            $pelatihan   = Pelatihan::find($this->selectedPelatihanId);
            $namaLatihan = $pelatihan?->nama_pelatihan ?? 'pelatihan ini';

            $detailList = PelatihanDetail::where('pelatihan_id', $this->selectedPelatihanId)
                ->where('is_active', true)
                ->get();

            DB::transaction(function () use ($anggota, $detailList, &$inserted) {
                foreach ($detailList as $detail) {
                    $exists = AnggotaPelatihan::where('anggota_id', $anggota->id)
                        ->where('pelatihan_detail_id', $detail->id)
                        ->exists();

                    if (! $exists) {
                        AnggotaPelatihan::create([
                            'anggota_id'          => $anggota->id,
                            'pelatihan_detail_id' => $detail->id,
                            // status_kehadiran & skor dikosongkan → diisi nanti
                            'status_kehadiran'    => null,
                            'skor'                => null,
                        ]);
                        $inserted++;
                    }
                }
            });
        }

        // ── Notifikasi & redirect ke dashboard ──
        if ($inserted > 0) {
            Notification::make()
                ->title("Pendaftaran berhasil, {$anggota->nama_lengkap}!")
                ->body("Anda telah terdaftar di {$inserted} materi pelatihan \"{$namaLatihan}\". Silakan cek dashboard Anda.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title("Selamat datang kembali, {$anggota->nama_lengkap}!")
                ->body("Anda sudah terdaftar di semua materi pelatihan \"{$namaLatihan}\".")
                ->info()
                ->send();
        }

        $this->dispatch('close-modal', id: 'cari-anggota-modal');

        $this->redirect(
            DashboardAnggota::getUrl(['anggotaId' => $anggota->id])
        );
    }

    // ----------------------------------------------------------------
    // Form list
    // ----------------------------------------------------------------
    protected function getForms(): array
    {
        return ['form'];
    }
}