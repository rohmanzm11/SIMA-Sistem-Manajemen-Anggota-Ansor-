<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Buat permissions
        $permissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'delete_anggota',
            'manage_roles',
            'manage_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat roles dan assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Assign semua permissions ke admin
        $adminRole->syncPermissions($permissions);

        // Assign permissions terbatas ke editor
        $editorRole->syncPermissions([
            'view_users',
            'view_anggota',
            'edit_anggota',
        ]);

        // Assign permissions minimal ke member
        $memberRole->syncPermissions([
            'view_anggota',
        ]);

        // Assign role admin ke user tertentu (opsional)
        $user = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
