<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Tambahkan ini untuk Schema

class KecamatansTableSeeder extends Seeder
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

        // Bersihkan tabel kecamatans sebelum seeding
        DB::table('kecamatans')->truncate(); // Atau gunakan Kecamatan::truncate();

        // Mengaktifkan kembali pemeriksaan foreign key
        Schema::enableForeignKeyConstraints();

        $kecamatans = [
            'Kaliwungu',
            'Kota Kudus',
            'Jati',
            'Undaan',
            'Mejobo',
            'Jekulo',
            'Gebog',
            'Dawe',
            'Bae',
        ];

        foreach ($kecamatans as $kecamatanName) {
            Kecamatan::create([
                'nama_kecamatan' => $kecamatanName,
            ]);
        }
    }
}
