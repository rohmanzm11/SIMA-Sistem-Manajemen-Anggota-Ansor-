<?php

namespace App\Filament\Resources\LevelResource\Pages;

use App\Filament\Resources\LevelResource;
use App\Models\Level;
use App\Models\Pac;
use App\Models\Pc;
use App\Models\Ranting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListLevels extends ListRecords
{
    protected static string $resource = LevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_levels')
                ->label('Generate Level Otomatis')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Level Otomatis')
                ->modalDescription('Sistem akan membuat Level untuk semua PC, PAC, dan Ranting yang belum terdaftar. Data yang sudah ada tidak akan ditimpa.')
                ->modalSubmitActionLabel('Ya, Generate Sekarang')
                ->action(function () {
                    $generated = 0;

                    // --- Generate PC ---
                    $existingPcIds = Level::where('level_type', 'PC')
                        ->pluck('reference_id')
                        ->toArray();

                    $newPcs = Pc::whereNotIn('id', $existingPcIds)->get();

                    foreach ($newPcs as $pc) {
                        Level::create([
                            'level_type'   => 'PC',
                            'reference_id' => $pc->id,
                        ]);
                        $generated++;
                    }

                    // --- Generate PAC ---
                    $existingPacIds = Level::where('level_type', 'PAC')
                        ->pluck('reference_id')
                        ->toArray();

                    $newPacs = Pac::whereNotIn('id', $existingPacIds)->get();

                    foreach ($newPacs as $pac) {
                        Level::create([
                            'level_type'   => 'PAC',
                            'reference_id' => $pac->id,
                        ]);
                        $generated++;
                    }

                    // --- Generate Ranting ---
                    $existingRantingIds = Level::where('level_type', 'RANTING')
                        ->pluck('reference_id')
                        ->toArray();

                    $newRantings = Ranting::whereNotIn('id', $existingRantingIds)->get();

                    foreach ($newRantings as $ranting) {
                        Level::create([
                            'level_type'   => 'RANTING',
                            'reference_id' => $ranting->id,
                        ]);
                        $generated++;
                    }

                    // --- Notifikasi hasil ---
                    if ($generated > 0) {
                        Notification::make()
                            ->title('Generate Berhasil')
                            ->body("{$generated} Level baru berhasil dibuat.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Tidak Ada Data Baru')
                            ->body('Semua PC, PAC, dan Ranting sudah terdaftar sebagai Level.')
                            ->info()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}
