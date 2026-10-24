<?php

namespace App\Console\Commands;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SyncAnggotaUsers extends Command
{
    protected $signature   = 'anggota:sync-users
                                {--dry-run : Tampilkan preview tanpa menyimpan}';

    protected $description = 'Sinkronisasi akun User untuk semua Anggota yang belum memiliki akun';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $anggotaTanpaUser = Anggota::whereDoesntHave('user')->get();

        if ($anggotaTanpaUser->isEmpty()) {
            $this->info('✅ Semua anggota sudah memiliki akun user.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$anggotaTanpaUser->count()} anggota tanpa akun user.");
        $this->newLine();

        $created = 0;
        $skipped = 0;

        foreach ($anggotaTanpaUser as $anggota) {
            $email = $anggota->alamat_email ?: $anggota->nik . '@member.local';

            // Pastikan email unik
            if (User::where('email', $email)->exists()) {
                $email = $anggota->nik . '_' . $anggota->id . '@member.local';
            }

            $this->line("→ [{$anggota->id}] {$anggota->nama_lengkap} — {$email}");

            if (! $dryRun) {
                User::create([
                    'anggota_id' => $anggota->id,
                    'name'       => $anggota->nama_lengkap,
                    'email'      => $email,
                    'password'   => Hash::make('password123'),
                    'role'       => 'user',
                    'is_active'  => true,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->newLine();

        if ($dryRun) {
            $this->warn("⚠️  Dry-run mode: {$skipped} akun TIDAK dibuat. Jalankan tanpa --dry-run untuk menyimpan.");
        } else {
            $this->info("✅ Berhasil membuat {$created} akun user baru dengan password default 'password123'.");
        }

        return self::SUCCESS;
    }
}
