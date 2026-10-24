<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ranting;
use App\Models\Pac;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RantingsTableSeeder extends Seeder
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

        // Bersihkan tabel rantings sebelum seeding
        DB::table('rantings')->truncate();

        // Mengaktifkan kembali pemeriksaan foreign key
        Schema::enableForeignKeyConstraints();

        // Pastikan tabel pacs dan kecamatans memiliki data
        $pacsCount = Pac::count();
        if ($pacsCount === 0) {
            throw new \Exception('Tabel pacs kosong. Harap jalankan PacSeeder terlebih dahulu.');
        }
        $kecamatansCount = Kecamatan::count();
        if ($kecamatansCount === 0) {
            throw new \Exception('Tabel kecamatans kosong. Harap jalankan KecamatansTableSeeder terlebih dahulu.');
        }

        // Data desa dari DesasTableSeeder
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
            'Kudus' => [
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
                'Kramat',
                'Kajeksan',
                'Kerjasan',
                'Mlati Kidul',
                'Mlati Norowito',
                'Panjunan',
                'Purwosari',
                'Sunggingan',
                'Wergu Kulon',
                'Wergu Wetan',
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
            // Sesuaikan 'Kudus' dengan 'Kota Kudus'
            $actualKecamatanName = ($kecamatanName === 'Kudus') ? 'Kota Kudus' : $kecamatanName;
            $pac = Pac::where('nama_pac', 'PAC ' . $actualKecamatanName)->first();

            if ($pac) {
                foreach (array_unique($desas) as $desaName) {
                    $namaRanting = 'PR ' . $desaName;
                    // Pastikan nama_ranting tidak melebihi 100 karakter
                    if (strlen($namaRanting) > 100) {
                        $this->command->warn("Nama ranting '{$namaRanting}' melebihi 100 karakter dan akan dipotong.");
                        $namaRanting = substr($namaRanting, 0, 100);
                    }
                    Ranting::create([
                        'pac_id' => $pac->id,
                        'nama_ranting' => $namaRanting,
                    ]);
                }
            } else {
                $this->command->error("PAC untuk kecamatan '{$actualKecamatanName}' tidak ditemukan. Pastikan PacSeeder telah dijalankan.");
            }
        }
    }
}
