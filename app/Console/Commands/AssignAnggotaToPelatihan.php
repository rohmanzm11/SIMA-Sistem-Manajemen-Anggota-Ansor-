<?php

namespace App\Console\Commands;

use App\Models\Anggota;
use App\Models\AnggotaPelatihan;
use App\Models\PelatihanDetail;
use Illuminate\Console\Command;

class AssignAnggotaToPelatihan extends Command
{
    protected $signature = 'anggota:assign-pelatihan
                            {--clear : Hapus semua data anggota_pelatihans terlebih dahulu}
                            {--all-materi : Daftarkan semua anggota ke SEMUA pelatihan detail yang ada}
                            {--hadir-percentage=85 : Persentase yang hadir}
                            {--lulus-percentage=75 : Persentase yang lulus dari yang hadir}';

    protected $description = 'Daftarkan semua anggota ke semua materi pelatihan';

    public function handle(): int
    {
        // ========================================
        // 1. CLEAR DATA LAMA
        // ========================================
        if ($this->option('clear')) {
            $total = AnggotaPelatihan::count();
            AnggotaPelatihan::truncate(); // atau ->delete() kalau takut foreign key
            $this->warn("Berhasil menghapus {$total} data assign sebelumnya.");
            $this->newLine();
        }

        // ========================================
        // 2. AMBIL SEMUA DATA
        // ========================================
        $anggotas = Anggota::all();
        $pelatihanDetails = PelatihanDetail::with('materi')->get();

        if ($anggotas->isEmpty()) {
            $this->error('Tidak ada data Anggota.');
            return self::FAILURE;
        }

        if ($pelatihanDetails->isEmpty()) {
            $this->error('Tidak ada data Pelatihan Detail / Materi.');
            return self::FAILURE;
        }

        $this->info("Anggota      : {$anggotas->count()}");
        $this->info("Materi       : {$pelatihanDetails->count()}");
        $this->info("Total record : " . ($anggotas->count() * $pelatihanDetails->count()));
        $this->newLine();

        if (!$this->confirm('Lanjutkan proses assign semua anggota ke semua materi?', true)) {
            $this->info('Dibatalkan.');
            return self::SUCCESS;
        }

        // ========================================
        // 3. ASSIGN SEMUA KE SEMUA
        // ========================================
        $hadirPercentage = (int) $this->option('hadir-percentage');
        $lulusPercentage = (int) $this->option('lulus-percentage');

        $bar = $this->output->createProgressBar($anggotas->count() * $pelatihanDetails->count());
        $bar->start();

        $created = 0;

        foreach ($anggotas as $anggota) {
            foreach ($pelatihanDetails as $detail) {
                $skorMaksimal = $detail->materi->skor_maksimal ?? 100;
                $batasLulus   = $skorMaksimal * 0.6;

                $isHadir = rand(1, 100) <= $hadirPercentage;

                $statusKehadiran = $isHadir ? 'Hadir' : 'Tidak Hadir';
                $skor            = null;
                $sertifikatNomor = null;
                $tanggalTerbit   = null;
                $keterangan      = null;

                if ($isHadir) {
                    $isLulus = rand(1, 100) <= $lulusPercentage;

                    if ($isLulus) {
                        $skor = round(rand((int) ($batasLulus * 100), (int) ($skorMaksimal * 100)) / 100, 2);
                        $sertifikatNomor = 'SERT/' . now()->year . '/' . str_pad($anggota->id, 3, '0', STR_PAD_LEFT) . '-' . $detail->id;
                        $tanggalTerbit = now()->subDays(rand(5, 90))->toDateString();
                    } else {
                        $skor = round(rand(25, (int) (($batasLulus - 1) * 100)) / 100, 2);
                        $keterangan = 'Belum mencapai nilai minimal kelulusan';
                    }
                } else {
                    $alasan = ['Sakit', 'Izin keluarga', 'Tidak ada kabar', 'Kendala transportasi', 'Urusan mendesak'];
                    $keterangan = $alasan[array_rand($alasan)];
                }

                AnggotaPelatihan::create([
                    'anggota_id'                => $anggota->id,
                    'pelatihan_detail_id'       => $detail->id,
                    'status_kehadiran'          => $statusKehadiran,
                    'skor'                      => $skor,
                    'keterangan'                => $keterangan,
                    'sertifikat_nomor'          => $sertifikatNomor,
                    'sertifikat_path'           => null,
                    'tanggal_terbit_sertifikat' => $tanggalTerbit,
                ]);

                $created++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Berhasil membuat {$created} data assign (semua anggota × semua materi).");

        return self::SUCCESS;
    }
}