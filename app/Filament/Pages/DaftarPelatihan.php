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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman "Daftar Pelatihan"
 *
 * Alur:
 *  1. Tampilkan daftar PELATIHAN (digabung per pelatihan, semua sesi tampil di bawahnya).
 *  2. Pengguna klik "Ikuti Pelatihan" → modal input NIK/NIA + Nama + Tanggal Lahir.
 *  3. Jika SUDAH terdaftar sebagai anggota DAN verifikasi 3 data cocok:
 *       - Auto-input AnggotaPelatihan untuk SEMUA sesi aktif pelatihan tersebut
 *         (skip sesi yang sudah ada agar tidak duplikat).
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

    /** ID Pelatihan (bukan detail/sesi) yang dipilih */
    public ?int $selectedPelatihanId = null;

    /** Data form pencarian anggota */
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
    // Ambil daftar pelatihan yang masih ada sesi aktif ke depan
    // Dikelompokkan per Pelatihan — bukan per sesi/materi
    // ----------------------------------------------------------------
    public function getPelatihanList(): \Illuminate\Support\Collection
    {
        return Pelatihan::with([
            'pelatihanDetails' => function ($q) {
                $q->where('is_active', true)
                    ->whereNotNull('tanggal')
                    ->orderBy('tanggal')
                    ->orderBy('jam_mulai')
                    ->with('materi');
            },
        ])
            ->whereHas('pelatihanDetails', function ($q) {
                $q->where('is_active', true)
                    ->whereNotNull('tanggal')
                    ->where('tanggal', '>=', now()->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Pelatihan $p) {
                // Hanya tampilkan sesi yang belum lewat
                $p->sesiAktif = $p->pelatihanDetails->filter(
                    fn($d) => $d->tanggal && $d->tanggal >= now()->toDateString()
                )->values();
                return $p;
            })
            ->filter(fn($p) => $p->sesiAktif->isNotEmpty())
            ->values();
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

        $data                   = $this->form->getState();
        $keyword                = trim($data['kata_kunci'] ?? '');
        $namaInput              = trim($data['nama_verifikasi'] ?? '');
        $tanggalLahirInput      = $data['tanggal_lahir_verifikasi'] ?? null;

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

            // Hanya redirect ke Peserta setelah percobaan pertama (tidak langsung)
            // agar tidak memberi petunjuk NIK mana yang valid
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
            // tapi jangan juga bocorkan field mana yang salah
            return;
        }

        // ── Verifikasi berhasil → reset rate limiter ──
        RateLimiter::clear($limiterKey);

        // ── Auto-input semua sesi aktif dari pelatihan yang dipilih ──
        $namaLatihan = '—';
        $inserted    = 0;

        if ($this->selectedPelatihanId) {
            $pelatihan   = Pelatihan::find($this->selectedPelatihanId);
            $namaLatihan = $pelatihan?->nama_pelatihan ?? 'pelatihan ini';

            $sesiList = PelatihanDetail::where('pelatihan_id', $this->selectedPelatihanId)
                ->where('is_active', true)
                ->whereNotNull('tanggal')
                ->get();

            DB::transaction(function () use ($anggota, $sesiList, &$inserted) {
                foreach ($sesiList as $sesi) {
                    $exists = AnggotaPelatihan::where('anggota_id', $anggota->id)
                        ->where('pelatihan_detail_id', $sesi->id)
                        ->exists();

                    if (! $exists) {
                        AnggotaPelatihan::create([
                            'anggota_id'          => $anggota->id,
                            'pelatihan_detail_id'  => $sesi->id,
                            // status_kehadiran & skor dikosongkan → diisi anggota nanti
                            'status_kehadiran'     => null,
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
                ->body("Anda telah terdaftar di {$inserted} sesi pelatihan \"{$namaLatihan}\". Silakan isi kehadiran Anda pada setiap sesi di dashboard.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title("Selamat datang kembali, {$anggota->nama_lengkap}!")
                ->body("Anda sudah terdaftar di semua sesi pelatihan \"{$namaLatihan}\". Jangan lupa isi kehadiran Anda.")
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
