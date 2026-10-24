<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use App\Filament\Pages\Peserta; // ← import page Peserta

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';

    protected function handleRegistration(array $data): Model
    {
        $data['role'] = 'user';
        $data['is_active'] = true;

        $user = $this->getUserModel()::create($data);

        $role = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return Peserta::getUrl(); // ← langsung pakai getUrl() dari class Peserta
    }
}
