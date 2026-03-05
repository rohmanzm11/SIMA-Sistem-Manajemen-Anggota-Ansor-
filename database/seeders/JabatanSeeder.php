<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menonaktifkan pemeriksaan foreign key sementara (meskipun tidak ada foreign key di tabel ini)
        Schema::disableForeignKeyConstraints();

        // Bersihkan tabel jabatans sebelum seeding
        DB::table('jabatans')->truncate();

        // Mengaktifkan kembali pemeriksaan foreign key
        Schema::enableForeignKeyConstraints();

        // Data jabatan
        $jabatans = [
            'Ketua',
            'Wakil Ketua',
            'Sekretaris',
            'Wakil Sekretaris',
            'Bendahara',
            'Wakil Bendahara',
            'Komandan Banser',
            'Wakil Komandan Banser',
            'Anggota GP Ansor',
            'Anggota Banser',
        ];

        foreach ($jabatans as $namaJabatan) {
            // Pastikan nama_jabatan tidak melebihi 100 karakter
            if (strlen($namaJabatan) > 100) {
                $this->command->warn("Nama jabatan '{$namaJabatan}' melebihi 100 karakter dan akan dipotong.");
                $namaJabatan = substr($namaJabatan, 0, 100);
            }

            Jabatan::create([
                'nama_jabatan' => $namaJabatan,
            ]);
        }
    }
}
