<?php

namespace App\View\Components\Filament;

use Closure;
use App\Models\Anggota;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class UserMenu extends Component
{
    public string  $inisial;
    public ?string $fotoUrl;
    public string  $namaUser;
    public string  $role;
    public ?Anggota $anggota;

    public function __construct()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->anggota  = $user->anggota_id ? Anggota::find($user->anggota_id) : null;
        $this->namaUser = $user->nama_lengkap ?? $user->name;
        $this->inisial  = strtoupper(substr($this->namaUser, 0, 1));
        $this->fotoUrl  = $this->resolveFotoUrl($this->anggota?->foto);
        $this->role     = $this->resolveRole($user);
    }

    // ----------------------------------------------------------------

    private function resolveFotoUrl(?string $foto): ?string
    {
        if (blank($foto)) {
            return null;
        }

        if (str_starts_with($foto, 'http')) {
            return $foto;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if ($disk->exists($foto)) {
            return $disk->url($foto);
        }

        return asset('storage/' . ltrim($foto, '/'));
    }

    private function resolveRole(mixed $user): string
    {
        return match (true) {
            method_exists($user, 'getRoleNames') => $user->getRoleNames()->first() ?? 'Admin',
            isset($user->role)                   => $user->role,
            default                              => 'Admin',
        };
    }

    // ----------------------------------------------------------------

    public function render(): View|Closure|string
    {
        return view('filament.components.user-menu');
    }
}
