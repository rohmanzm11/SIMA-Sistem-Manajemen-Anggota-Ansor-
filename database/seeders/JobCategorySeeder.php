<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh data kategori pekerjaan
        $categories = [
            ['nama_pekerjaan' => 'Teknologi Informasi'],
            ['nama_pekerjaan' => 'Keuangan'],
            ['nama_pekerjaan' => 'Pemasaran'],
            ['nama_pekerjaan' => 'Desain Grafis'],
            ['nama_pekerjaan' => 'Sumber Daya Manusia'],
            ['nama_pekerjaan' => 'Kesehatan'],
            ['nama_pekerjaan' => 'Pendidikan'],
            ['nama_pekerjaan' => 'Konstruksi'],
            ['nama_pekerjaan' => 'Manufaktur'],
            ['nama_pekerjaan' => 'Logistik'],
        ];

        // Tambahkan timestamp created_at dan updated_at
        $now = Carbon::now();
        foreach ($categories as $key => $category) {
            $categories[$key]['created_at'] = $now;
            $categories[$key]['updated_at'] = $now;
        }

        // Masukkan data ke dalam tabel 'pekerjaans'
        // Sesuai dengan skema migrasi yang Anda berikan
        DB::table('pekerjaans')->insert($categories);

        $this->command->info('Kategori pekerjaan berhasil ditambahkan ke tabel "pekerjaans"!');
    }
}
