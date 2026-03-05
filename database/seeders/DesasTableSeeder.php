<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Tambahkan ini untuk Schema

class DesasTableSeeder extends Seeder
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

        // Bersihkan tabel desa sebelum seeding
        // Menggunakan delete() daripada truncate() karena truncate() sering bermasalah dengan foreign key
        // Meskipun disableForeignKeyConstraints() sudah dilakukan, delete() lebih aman.
        // Atau, jika ingin tetap menggunakan truncate(), pastikan tabel anggotas juga dikosongkan terlebih dahulu
        // jika data di anggotas tidak penting untuk dipertahankan saat seeding.
        DB::table('desas')->truncate(); // Atau gunakan Desa::truncate();

        // Mengaktifkan kembali pemeriksaan foreign key
        Schema::enableForeignKeyConstraints();

        // Data desa yang sudah diperbaiki dan diverifikasi sesuai kecamatan di Kudus berdasarkan Wikipedia
        $dataDesa = [
            'Bae' => [
                'Bacin',
                'Bae',
                'Dersalam',
                'Gondangmanis',
                'Karangbener',
                'Ngembalrejo',
                'Panjang',
                'Pedawang',
                'Peganjaran',
                'Purworejo',
            ],
            'Dawe' => [
                'Cendono',
                'Colo',
                'Cranggang',
                'Dukuhwaringin',
                'Glagah Kulon',
                'Japan',
                'Kajar',
                'Kandangmas',
                'Kuwukan',
                'Lau',
                'Margorejo',
                'Piji',
                'Puyoh',
                'Rejosari',
                'Samirejo',
                'Soco',
                'Tergo',
                'Ternadi',
            ],
            'Gebog' => [
                'Besito',
                'Getasrabi',
                'Gondosari',
                'Gribig',
                'Jurang',
                'Karangmalang',
                'Kedungsari',
                'Klumpit',
                'Menawan',
                'Padurenan',
                'Rahtawu',
            ],
            'Jati' => [
                'Getaspejaten',
                'Jati Kulon',
                'Jati Wetan',
                'Jepangpakis',
                'Jetiskapuan',
                'Loram Kulon',
                'Loram Wetan',
                'Megawon',
                'Ngembal Kulon',
                'Pasuruhan Kidul',
                'Pasuruhan Lor',
                'Ploso',
                'Tanjungkarang',
                'Tumpangkrasak',
            ],
            'Jekulo' => [
                'Bulung Kulon',
                'Bulungcangkring',
                'Gondoharum',
                'Hadipolo',
                'Honggosoco',
                'Jekulo',
                'Klaling',
                'Pladen',
                'Sadang',
                'Sidomulyo',
                'Tanjungrejo',
                'Terban',
            ],
            'Kaliwungu' => [
                'Bakalankrapyak',
                'Banget',
                'Blimbing Kidul',
                'Gamong',
                'Garung Kidul',
                'Garung Lor',
                'Des Kaliwungu',
                'Karangampel',
                'Kedungdowo',
                'Mijen',
                'Papringan',
                'Prambatan Kidul',
                'Prambatan Lor',
                'Setrokalangan',
                'Sidorekso',
            ],
            'Kudus' => [ // Ini adalah Kecamatan Kota Kudus
                'Barongan',
                'Burikan',
                'Damaran',
                'Demaan',
                'Demangan',
                'Glantengan',
                'Janggalan',
                'Kaliputu',
                'Kauman',
                'Krandon',
                'Langgardalem',
                'Mlati Lor',
                'Nganguk',
                'Rendeng',
                'Singocandi',
                'Kramat', // Desa
                'Kajeksan',
                'Kerjasan',
                'Mlati Kidul',
                'Mlati Norowito',
                'Panjunan',
                'Purwosari',
                'Sunggingan',
                'Wergu Kulon',
                'Wergu Wetan', // Kelurahan
            ],
            'Mejobo' => [
                'Golantepus',
                'Gulang',
                'Hadiwarno',
                'Jepang',
                'Jojo',
                'Kesambi',
                'Kirig',
                'Mejobo',
                'Payaman',
                'Temulus',
                'Tenggeles',
            ],
            'Undaan' => [
                'Glagahwaru',
                'Kalirejo',
                'Karangrowo',
                'Kutuk',
                'Lambangan',
                'Larikrejo',
                'Medini',
                'Ngemplak',
                'Sambung',
                'Terangmas',
                'Undaan Kidul',
                'Undaan Lor',
                'Undaan Tengah',
                'Wates',
                'Wonosoco',
                'Berugenjang',
            ],
        ];


        foreach ($dataDesa as $kecamatanName => $desas) {
            // Perhatikan bahwa 'Kudus' di data desa merujuk ke 'Kota Kudus' di tabel kecamatan
            $actualKecamatanName = ($kecamatanName === 'Kudus') ? 'Kota Kudus' : $kecamatanName;
            $kecamatan = Kecamatan::where('nama_kecamatan', $actualKecamatanName)->first();

            if ($kecamatan) {
                foreach (array_unique($desas) as $desaName) {
                    Desa::create([
                        'nama_desa' => $desaName,
                        'kecamatan_id' => $kecamatan->id,
                    ]);
                }
            } else {
                $this->command->error("Kecamatan **'{$actualKecamatanName}'** tidak ditemukan. Pastikan **KecamatansTableSeeder** telah dijalankan terlebih dahulu dan nama kecamatan di seeder desa sesuai dengan seeder kecamatan.");
            }
        }
    }
}
