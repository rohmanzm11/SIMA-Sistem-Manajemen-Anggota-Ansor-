<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat atau cari user kamu
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Ganti dengan email akunmu
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Tempelkan role super_admin ke user
        $user->assignRole('super_admin');
    }
}