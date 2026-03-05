<?php

namespace App\Observers;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AnggotaObserver
{
    /**
     * Handle the Anggota "created" event.
     * Otomatis membuat User baru saat Anggota dibuat.
     */
    public function created(Anggota $anggota): void
    {
        // Hindari duplikat jika user sudah ada untuk anggota ini
        if (User::where('anggota_id', $anggota->id)->exists()) {
            return;
        }

        // Gunakan email anggota, atau fallback ke nik@domain.com jika kosong
        $email = $anggota->alamat_email
            ?: $anggota->nik . '@member.local';

        // Pastikan email unik
        if (User::where('email', $email)->exists()) {
            $email = $anggota->nik . '_' . $anggota->id . '@member.local';
        }

        User::create([
            'anggota_id' => $anggota->id,
            'name'       => $anggota->nama_lengkap,
            'email'      => $email,
            'password'   => Hash::make('password123'),
            'role'       => 'user',
            'is_active'  => true,
        ]);
    }

    /**
     * Handle the Anggota "updated" event.
     * Sinkronisasi nama & email ke User jika berubah.
     */
    public function updated(Anggota $anggota): void
    {
        $user = User::where('anggota_id', $anggota->id)->first();

        if (! $user) {
            // Jika belum punya user (misal data lama), buat sekarang
            $this->created($anggota);
            return;
        }

        $updates = [];

        // Sinkronisasi nama
        if ($user->name !== $anggota->nama_lengkap) {
            $updates['name'] = $anggota->nama_lengkap;
        }

        // Sinkronisasi email jika anggota punya email dan berbeda
        if (
            $anggota->alamat_email
            && $user->email !== $anggota->alamat_email
            && ! User::where('email', $anggota->alamat_email)->where('id', '!=', $user->id)->exists()
        ) {
            $updates['email'] = $anggota->alamat_email;
        }

        if (! empty($updates)) {
            $user->update($updates);
        }
    }

    /**
     * Handle the Anggota "deleted" event.
     * Nonaktifkan User saat Anggota dihapus (soft delete akun).
     */
    public function deleted(Anggota $anggota): void
    {
        User::where('anggota_id', $anggota->id)
            ->update(['is_active' => false]);
    }
}
