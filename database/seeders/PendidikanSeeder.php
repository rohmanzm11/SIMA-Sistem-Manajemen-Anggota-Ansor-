<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        $anggotas = DB::table('anggotas')->pluck('id');

        if ($anggotas->isEmpty()) {
            $this->command->warn('Tidak ada data anggota. Jalankan AnggotaSeeder terlebih dahulu.');
            return;
        }

        $data = [];

        foreach ($anggotas as $anggotaId) {
            // Buat jalur pendidikan yang realistis per anggota
            $jalur = $this->generateJalurPendidikan();

            foreach ($jalur as $pendidikan) {
                $data[] = array_merge($pendidikan, [
                    'anggota_id' => $anggotaId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Insert per batch agar tidak timeout
        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('pendidikans')->insert($chunk);
        }

        $this->command->info('PendidikanSeeder berhasil: ' . count($data) . ' data pendidikan untuk ' . count($anggotas) . ' anggota.');
    }

    /**
     * Generate jalur pendidikan yang realistis dan kronologis
     */
    private function generateJalurPendidikan(): array
    {
        $jalur = fake()->randomElement([
            'umum_s1',
            'umum_s2',
            'umum_s3',
            'umum_d3',
            'umum_smk',
            'pesantren_s1',
            'pesantren_s2',
            'diniyyah_s1',
            'ma_s1',
            'ma_s2',
            'sedang_sd',
            'sedang_smp',
            'sedang_sma',
            'sedang_kuliah',
        ]);

        return match ($jalur) {
            'umum_s1'       => $this->jalurUmumS1(),
            'umum_s2'       => $this->jalurUmumS2(),
            'umum_s3'       => $this->jalurUmumS3(),
            'umum_d3'       => $this->jalurUmumD3(),
            'umum_smk'      => $this->jalurUmumSMK(),
            'pesantren_s1'  => $this->jalurPesantrenS1(),
            'pesantren_s2'  => $this->jalurPesantrenS2(),
            'diniyyah_s1'   => $this->jalurDiniyyahS1(),
            'ma_s1'         => $this->jalurMAS1(),
            'ma_s2'         => $this->jalurMAS2(),
            'sedang_sd'     => $this->jalurSedangSD(),
            'sedang_smp'    => $this->jalurSedangSMP(),
            'sedang_sma'    => $this->jalurSedangSMA(),
            'sedang_kuliah' => $this->jalurSedangKuliah(),
            default         => $this->jalurUmumS1(),
        };
    }

    // -----------------------------------------------------------------------
    // JALUR-JALUR PENDIDIKAN
    // -----------------------------------------------------------------------

    /** SD → SMP → SMA → S1 */
    private function jalurUmumS1(): array
    {
        $tahunSD = rand(1995, 2005);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->sma($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 12),
        ];
    }

    /** SD → SMP → SMA → S1 → S2 */
    private function jalurUmumS2(): array
    {
        $tahunSD = rand(1990, 2000);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->sma($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 12, 'Lulus'),
            $this->s2($tahunSD + 16),
        ];
    }

    /** SD → SMP → SMA → S1 → S2 → S3 */
    private function jalurUmumS3(): array
    {
        $tahunSD = rand(1985, 1995);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->sma($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 12, 'Lulus'),
            $this->s2($tahunSD + 16, 'Lulus'),
            $this->s3($tahunSD + 18),
        ];
    }

    /** SD → SMP → SMK → D3 */
    private function jalurUmumD3(): array
    {
        $tahunSD = rand(1995, 2005);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->smk($tahunSD + 9, 'Lulus'),
            $this->d3($tahunSD + 12),
        ];
    }

    /** SD → SMP → SMK (berhenti di SMK) */
    private function jalurUmumSMK(): array
    {
        $tahunSD = rand(1998, 2008);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->smk($tahunSD + 9),
        ];
    }

    /** SD → SMP → Pesantren → S1 */
    private function jalurPesantrenS1(): array
    {
        $tahunSD = rand(1995, 2005);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->pesantren($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 13),
        ];
    }

    /** SD → SMP → Pesantren → S1 → S2 */
    private function jalurPesantrenS2(): array
    {
        $tahunSD = rand(1988, 1998);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->pesantren($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 13, 'Lulus'),
            $this->s2($tahunSD + 17),
        ];
    }

    /** SD → SMP → Diniyyah → S1 */
    private function jalurDiniyyahS1(): array
    {
        $tahunSD = rand(1995, 2005);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->diniyyah($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 13),
        ];
    }

    /** SD → SMP → MA → S1 */
    private function jalurMAS1(): array
    {
        $tahunSD = rand(1995, 2005);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->ma($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 12),
        ];
    }

    /** SD → SMP → MA → S1 → S2 */
    private function jalurMAS2(): array
    {
        $tahunSD = rand(1988, 1998);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->ma($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 12, 'Lulus'),
            $this->s2($tahunSD + 16),
        ];
    }

    /** Masih SD */
    private function jalurSedangSD(): array
    {
        $tahunSD = rand(2018, 2022);
        return [
            $this->sd($tahunSD, 'Sedang Berjalan'),
        ];
    }

    /** SD → Sedang SMP */
    private function jalurSedangSMP(): array
    {
        $tahunSD = rand(2012, 2018);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Sedang Berjalan'),
        ];
    }

    /** SD → SMP → Sedang SMA */
    private function jalurSedangSMA(): array
    {
        $tahunSD = rand(2008, 2015);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->sma($tahunSD + 9, 'Sedang Berjalan'),
        ];
    }

    /** SD → SMP → SMA → Sedang Kuliah S1 */
    private function jalurSedangKuliah(): array
    {
        $tahunSD = rand(2005, 2012);
        return [
            $this->sd($tahunSD, 'Lulus'),
            $this->smp($tahunSD + 6, 'Lulus'),
            $this->sma($tahunSD + 9, 'Lulus'),
            $this->s1($tahunSD + 12, 'Sedang Berjalan'),
        ];
    }

    // -----------------------------------------------------------------------
    // BUILDER PER JENJANG
    // -----------------------------------------------------------------------

    private function sd(int $tahunMasuk, string $status = 'Lulus'): array
    {
        return [
            'jenjang'        => 'SD',
            'nama_institusi' => fake()->randomElement([
                'SD Negeri 1',
                'SD Negeri 2',
                'SD Negeri 3',
                'SD Muhammadiyah',
                'MI Negeri 1',
                'SD Islam Terpadu',
                'SD Al-Azhar',
                'MI Al-Hidayah',
                'SD Integral',
            ]),
            'jurusan'        => null,
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 6,
            'status'         => $status,
        ];
    }

    private function smp(int $tahunMasuk, string $status = 'Lulus'): array
    {
        return [
            'jenjang'        => 'SMP',
            'nama_institusi' => fake()->randomElement([
                'SMP Negeri 1',
                'SMP Negeri 2',
                'SMP Negeri 3',
                'SMP Muhammadiyah',
                'MTs Negeri 1',
                'SMP Islam Terpadu',
                'SMP Al-Azhar',
                'MTs Al-Hidayah',
                'SMP Plus',
            ]),
            'jurusan'        => null,
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 3,
            'status'         => $status,
        ];
    }

    private function sma(int $tahunMasuk, string $status = 'Lulus'): array
    {
        return [
            'jenjang'        => 'SMA',
            'nama_institusi' => fake()->randomElement([
                'SMA Negeri 1',
                'SMA Negeri 2',
                'SMA Negeri 3',
                'SMA Muhammadiyah',
                'SMA Islam Terpadu',
                'SMA Al-Azhar',
                'SMA Plus',
                'SMA Unggulan',
            ]),
            'jurusan'        => null,
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 3,
            'status'         => $status,
        ];
    }

    private function smk(int $tahunMasuk, string $status = 'Lulus'): array
    {
        return [
            'jenjang'        => 'SMK',
            'nama_institusi' => fake()->randomElement([
                'SMK Negeri 1',
                'SMK Negeri 2',
                'SMK Muhammadiyah',
                'SMK Teknologi',
                'SMK Bisnis Manajemen',
                'SMK Kesehatan',
                'SMK Informatika',
                'SMK Pariwisata',
            ]),
            'jurusan'        => null,
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 3,
            'status'         => $status,
        ];
    }

    private function ma(int $tahunMasuk, string $status = 'Lulus'): array
    {
        return [
            'jenjang'        => 'MA',
            'nama_institusi' => fake()->randomElement([
                'MAN 1',
                'MAN 2',
                'MA Al-Hikmah',
                'MA Darul Ulum',
                'MA Al-Falah',
                'MA Nurul Ulum',
                'MA Al-Anwar',
                'MA Al-Islamiyah',
            ]),
            'jurusan'        => null,
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 3,
            'status'         => $status,
        ];
    }

    private function pesantren(int $tahunMasuk, string $status = 'Lulus'): array
    {
        $durasi = rand(3, 6);
        return [
            'jenjang'        => 'Pesantren',
            'nama_institusi' => fake()->randomElement([
                'PP Al-Falah',
                'PP Darul Ulum',
                'PP Sidogiri',
                'PP Lirboyo',
                'PP Al-Anwar',
                'PP Gontor',
                'PP Tebuireng',
                'PP Al-Munawwir',
                'PP Langitan',
                'PP Al-Hidayah',
                'PP Miftahul Huda',
            ]),
            'jurusan'        => null,
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + $durasi,
            'status'         => $status,
        ];
    }

    private function diniyyah(int $tahunMasuk, string $status = 'Lulus'): array
    {
        $durasi = rand(3, 5);
        return [
            'jenjang'        => 'Diniyyah',
            'nama_institusi' => fake()->randomElement([
                'Madrasah Diniyyah Al-Huda',
                'Madin Al-Falah',
                'Madin Nurul Islam',
                'Madin Darul Hikmah',
                'Madin Al-Islamiyah',
                'Madin Bustanul Ulum',
                'Madin Raudhatul Ulum',
                'Madin Al-Ittihad',
            ]),
            'jurusan'        => null,
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + $durasi,
            'status'         => $status,
        ];
    }

    private function d3(int $tahunMasuk, string $status = null): array
    {
        $status = $status ?? fake()->randomElement(['Lulus', 'Lulus', 'Lulus', 'Drop Out', 'Tidak Lulus']);
        return [
            'jenjang'        => 'D3',
            'nama_institusi' => fake()->randomElement($this->daftarKampus()),
            'jurusan'        => fake()->randomElement($this->daftarJurusan()),
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 3,
            'status'         => $status,
        ];
    }

    private function s1(int $tahunMasuk, string $status = null): array
    {
        $status = $status ?? fake()->randomElement(['Lulus', 'Lulus', 'Lulus', 'Sedang Berjalan', 'Drop Out']);
        return [
            'jenjang'        => 'S1',
            'nama_institusi' => fake()->randomElement($this->daftarKampus()),
            'jurusan'        => fake()->randomElement($this->daftarJurusan()),
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 4,
            'status'         => $status,
        ];
    }

    private function s2(int $tahunMasuk, string $status = null): array
    {
        $status = $status ?? fake()->randomElement(['Lulus', 'Lulus', 'Sedang Berjalan', 'Drop Out']);
        return [
            'jenjang'        => 'S2',
            'nama_institusi' => fake()->randomElement($this->daftarKampus()),
            'jurusan'        => fake()->randomElement($this->daftarJurusanS2()),
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 2,
            'status'         => $status,
        ];
    }

    private function s3(int $tahunMasuk, string $status = null): array
    {
        $status = $status ?? fake()->randomElement(['Lulus', 'Lulus', 'Sedang Berjalan']);
        return [
            'jenjang'        => 'S3',
            'nama_institusi' => fake()->randomElement($this->daftarKampus()),
            'jurusan'        => fake()->randomElement($this->daftarJurusanS2()),
            'tahun_masuk'    => $tahunMasuk,
            'tahun_lulus'    => $status === 'Sedang Berjalan' ? null : $tahunMasuk + 4,
            'status'         => $status,
        ];
    }

    // -----------------------------------------------------------------------
    // DATA REFERENSI
    // -----------------------------------------------------------------------

    private function daftarKampus(): array
    {
        return [
            'Universitas Indonesia',
            'Universitas Gadjah Mada',
            'Institut Teknologi Bandung',
            'Universitas Airlangga',
            'Universitas Brawijaya',
            'Universitas Diponegoro',
            'Universitas Padjadjaran',
            'Universitas Hasanuddin',
            'Universitas Sebelas Maret',
            'UIN Syarif Hidayatullah Jakarta',
            'UIN Sunan Kalijaga Yogyakarta',
            'UIN Maulana Malik Ibrahim Malang',
            'UIN Sunan Ampel Surabaya',
            'UIN Walisongo Semarang',
            'IAIN Kudus',
            'IAIN Pekalongan',
            'Universitas Islam Indonesia',
            'Universitas Muhammadiyah Yogyakarta',
            'Universitas Muhammadiyah Malang',
            'Universitas Nahdlatul Ulama',
            'STAI Al-Anwar',
            'STIT Raden Santri',
            'Politeknik Negeri Jakarta',
            'Politeknik Negeri Semarang',
        ];
    }

    private function daftarJurusan(): array
    {
        return [
            'Teknik Informatika',
            'Sistem Informasi',
            'Ilmu Komputer',
            'Manajemen',
            'Akuntansi',
            'Ekonomi Syariah',
            'Perbankan Syariah',
            'Hukum',
            'Hukum Keluarga Islam',
            'Hukum Tata Negara',
            'Pendidikan Agama Islam',
            'Pendidikan Bahasa Arab',
            'Pendidikan Guru MI',
            'Komunikasi dan Penyiaran Islam',
            'Bimbingan Konseling Islam',
            'Tasawuf dan Psikoterapi',
            'Ilmu Al-Quran dan Tafsir',
            'Ilmu Hadis',
            'Aqidah dan Filsafat Islam',
            'Sosiologi',
            'Psikologi',
            'Kedokteran',
            'Keperawatan',
            'Farmasi',
            'Teknik Sipil',
            'Teknik Elektro',
            'Agribisnis',
            'Ilmu Hukum',
            'Administrasi Publik',
        ];
    }

    private function daftarJurusanS2(): array
    {
        return [
            'Manajemen Pendidikan Islam',
            'Pendidikan Agama Islam',
            'Hukum Islam',
            'Ekonomi Syariah',
            'Ilmu Al-Quran dan Tafsir',
            'Studi Islam',
            'Manajemen',
            'Hukum',
            'Teknik Informatika',
            'Ilmu Komunikasi',
            'Psikologi',
            'Administrasi Publik',
            'Ilmu Pemerintahan',
        ];
    }
}
