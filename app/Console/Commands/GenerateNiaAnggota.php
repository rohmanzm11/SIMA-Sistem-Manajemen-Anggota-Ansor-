<?php

namespace App\Console\Commands;

use App\Filament\Resources\AnggotaResource;
use App\Models\Anggota;
use Illuminate\Console\Command;

class GenerateNiaAnggota extends Command
{
    protected $signature = 'anggota:generate-nia 
                            {--force : Generate ulang NIA meskipun sudah terisi}
                            {--only-empty : Hanya generate yang NIA-nya masih kosong (default)}';

    protected $description = 'Generate NIA otomatis untuk semua / sebagian data Anggota';

    public function handle(): int
    {
        $force = $this->option('force');
        $onlyEmpty = $this->option('only-empty') || !$force;

        $query = Anggota::query()
            ->whereNotNull('kecamatan_id')
            ->whereNotNull('desa_id');

        if ($onlyEmpty) {
            $query->where(function ($q) {
                $q->whereNull('nia')->orWhere('nia', '');
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Tidak ada data yang perlu digenerate NIA.');
            return self::SUCCESS;
        }

        $this->info("Menemukan {$total} data Anggota...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;

        $query->chunkById(50, function ($anggotas) use (&$updated, $bar, $force) {
            foreach ($anggotas as $anggota) {
                // Skip jika sudah punya NIA dan tidak force
                if (!$force && filled($anggota->nia)) {
                    $bar->advance();
                    continue;
                }

                $nia = AnggotaResource::generateNia($anggota);

                $anggota->updateQuietly(['nia' => $nia]); // Quietly = tidak trigger observer/event

                $updated++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Berhasil generate NIA untuk {$updated} data Anggota.");

        return self::SUCCESS;
    }
}