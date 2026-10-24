<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pc;

class PcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pc::create([
            'nama_pc' => 'PC GP Ansor Kudus',
        ]);
    }
}