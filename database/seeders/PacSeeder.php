<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pac;
use App\Models\Pcs;
use App\Models\Kecamatan;
use App\Models\Pc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menonaktifkan pemeriksaan foreign key sementara
        Schema::disableForeignKeyConstraints();

        // Bersihkan tabel pacs sebelum seeding
        DB::table('pacs')->truncate();

        // Mengaktifkan kembali pemeriksaan foreign key
        Schema::enableForeignKeyConstraints();

        // Pastikan tabel pcs dan kecamatans memiliki data
        $pcsCount = Pc::count();
        if ($pcsCount === 0) {
            throw new \Exception('Tabel pcs kosong. Harap seed tabel pcs terlebih dahulu.');
        }
        $kecamatansCount = Kecamatan::count();
        if ($kecamatansCount === 0) {
            throw new \Exception('Tabel kecamatans kosong. Harap jalankan KecamatansTableSeeder terlebih dahulu.');
        }

        // Ambil semua kecamatan
        $kecamatans = Kecamatan::pluck('id', 'nama_kecamatan')->toArray();

        // Data PAC berdasarkan kecamatan
        $pacs = [
            'Kaliwungu' => ['nama_pac' => 'PAC Kaliwungu'],
            'Kota Kudus' => ['nama_pac' => 'PAC Kota Kudus'],
            'Jati' => ['nama_pac' => 'PAC Jati'],
            'Undaan' => ['nama_pac' => 'PAC Undaan'],
            'Mejobo' => ['nama_pac' => 'PAC Mejobo'],
            'Jekulo' => ['nama_pac' => 'PAC Jekulo'],
            'Gebog' => ['nama_pac' => 'PAC Gebog'],
            'Dawe' => ['nama_pac' => 'PAC Dawe'],
            'Bae' => ['nama_pac' => 'PAC Bae'],
        ];

        // Ambil semua ID dari tabel pcs untuk distribusi
        $pcIds = Pc::pluck('id')->toArray();
        $pcIndex = 0;

        foreach ($pacs as $kecamatanName => $pacData) {
            if (isset($kecamatans[$kecamatanName])) {
                // Gunakan pc_id secara berurutan, ulang jika kehabisan
                $pcId = $pcIds[$pcIndex % count($pcIds)];
                Pac::create([
                    'pc_id' => $pcId,
                    'nama_pac' => $pacData['nama_pac'],
                ]);
                $pcIndex++;
            } else {
                $this->command->error("Kecamatan '{$kecamatanName}' tidak ditemukan di tabel kecamatans.");
            }
        }
    }
}
