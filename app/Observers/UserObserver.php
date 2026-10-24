<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public function created(User $user): void
    {
        // Layer kedua: backup jika user dibuat dari luar Register Page
        // (misal: seeder, tinker, atau admin panel)
        if ($user->roles->isEmpty()) {
            $role = Role::firstOrCreate([
                'name' => 'user',
                'guard_name' => 'web',
            ]);

            $user->assignRole($role);
        }

        // Sync kolom 'role' di tabel users jika kosong
        if (empty($user->role)) {
            $user->updateQuietly(['role' => 'user']);
        }
    }
}
