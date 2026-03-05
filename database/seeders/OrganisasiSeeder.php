<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganisasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organisasis = [
            // Aspek Kemahasiswaan
            ['nama_organisasi' => 'Badan Eksekutif Mahasiswa (BEM)', 'jenis' => 'Kemahasiswaan'],
            ['nama_organisasi' => 'Himpunan Mahasiswa Islam (HMI)', 'jenis' => 'Kemahasiswaan'],
            ['nama_organisasi' => 'Pergerakan Mahasiswa Islam Indonesia (PMII)', 'jenis' => 'Kemahasiswaan'],
            ['nama_organisasi' => 'Ikatan Mahasiswa Muhammadiyah (IMM)', 'jenis' => 'Kemahasiswaan'],

            // Aspek Kepemudaan
            ['nama_organisasi' => 'Karang Taruna Indonesia', 'jenis' => 'Kepemudaan'],
            ['nama_organisasi' => 'Komite Nasional Pemuda Indonesia (KNPI)', 'jenis' => 'Kepemudaan'],
            ['nama_organisasi' => 'Gerakan Pramuka', 'jenis' => 'Kepemudaan'],
            ['nama_organisasi' => 'Purna Paskibraka Indonesia (PPI)', 'jenis' => 'Kepemudaan'],

            // Aspek Karyawan / Profesional
            ['nama_organisasi' => 'Serikat Pekerja Seluruh Indonesia (SPSI)', 'jenis' => 'Karyawan'],
            ['nama_organisasi' => 'Persatuan Guru Republik Indonesia (PGRI)', 'jenis' => 'Karyawan'],
            ['nama_organisasi' => 'Ikatan Dokter Indonesia (IDI)', 'jenis' => 'Karyawan'],
            ['nama_organisasi' => 'Asosiasi Pengusaha Indonesia (APINDO)', 'jenis' => 'Karyawan'],
        ];

        foreach ($organisasis as $org) {
            DB::table('organisasis')->insert(array_merge($org, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
