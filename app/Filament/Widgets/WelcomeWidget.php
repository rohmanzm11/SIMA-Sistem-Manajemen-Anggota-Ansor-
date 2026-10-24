<?php

namespace App\Filament\Widgets;

use App\Models\Anggota;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WelcomeWidget extends Widget
{
    protected static ?int $sort = -3; // paling atas
    // protected int | string | array $columnSpan = 'full';
    protected int | string | array $columnSpan = 3;
    protected static string $view = 'filament.widgets.welcome-widget';

    public function getViewData(): array
    {
        $user    = Auth::user();
        $anggota = Anggota::find($user->anggota_id);

        $fotoUrl = $anggota?->foto
            ? Storage::url($anggota->foto)
            : null;

        $role = match ($user->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'Admin',
            'verifikator' => 'Verifikator',
            default       => 'User',
        };

        $greeting = match (true) {
            now()->hour < 11 => 'Selamat Pagi',
            now()->hour < 15 => 'Selamat Siang',
            now()->hour < 18 => 'Selamat Sore',
            default          => 'Selamat Malam',
        };

        return [
            'user'     => $user,
            'anggota'  => $anggota,
            'fotoUrl'  => $fotoUrl,
            'role'     => $role,
            'greeting' => $greeting,
            'tanggal'  => now()->translatedFormat('l, d F Y'),
        ];
    }
}
