<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Politik; // Pastikan model Politik sudah ada
use Illuminate\Support\Carbon; // Untuk timestamps

class PolitikSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        $parties = [
            'Partai Kebangkitan Bangsa (PKB)',
            'Partai Gerakan Indonesia Raya (Gerindra)',
            'Partai Demokrasi Indonesia Perjuangan (PDI Perjuangan)',
            'Partai Golkar',
            'Partai NasDem',
            'Partai Buruh',
            'Partai Gelombang Rakyat Indonesia (Gelora)',
            'Partai Keadilan Sejahtera (PKS)',
            'Partai Kebangkitan Nusantara (PKN)',
            'Partai Hati Nurani Rakyat (Hanura)',
            'Partai Garda Republik Indonesia (Garuda)',
            'Partai Amanat Nasional (PAN)',
            'Partai Bulan Bintang (PBB)',
            'Partai Demokrat',
            'Partai Solidaritas Indonesia (PSI)',
            'Partai Partai Persatuan Indonesia (Perindo)',
            'Partai Persatuan Pembangunan (PPP)',
            'Partai Nanggroe Aceh (partai politik lokal Aceh)',
            'Partai Generasi Atjeh Beusaboh Tha\'at Dan Taqwa (partai politik lokal Aceh)',
            'Partai Darul Aceh (partai politik lokal Aceh)',
            'Partai Aceh (partai politik lokal Aceh)',
            'Partai Adil Sejahtera Aceh (partai politik lokal Aceh)',
            'Partai Soliditas Independen Rakyat Aceh (partai politik lokal Aceh)',
            'Partai Ummat'
        ];

        foreach ($parties as $partyName) {
            // Memeriksa apakah partai sudah ada untuk menghindari duplikasi
            // Karena kolom 'partai_politik' adalah unique
            Politik::firstOrCreate(
                ['partai_politik' => $partyName],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }
    }
}
