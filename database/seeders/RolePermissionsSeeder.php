<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $roles = ['admin', 'user'];

        // Define permissions
        $adminPermissions = [      
            'view dashboard',      
            'manage users',
            'manage permissions',
        ];

        $userPermissions = [
            'view dashboard',
        ];

        // Create all permissions
        $allPermissions = array_unique(array_merge($adminPermissions, $userPermissions));
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create roles and assign permissions
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($roleName === 'admin') {
                $role->syncPermissions($adminPermissions);
            } elseif ($roleName === 'user') {
                $role->syncPermissions($userPermissions);
            }
        }
    }
}