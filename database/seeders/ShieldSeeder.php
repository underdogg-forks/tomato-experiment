<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user       = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $owner      = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $manager    = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        // Create basic permissions
        $permissions = [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'view_any_tenant',
            'view_tenant',
            'create_tenant',
            'update_tenant',
            'delete_tenant',
            'view_any_account',
            'view_account',
            'create_account',
            'update_account',
            'delete_account',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Give super_admin all permissions
        $superAdmin->givePermissionTo(Permission::all());

        $this->command->info('Shield roles and permissions seeded successfully!');
    }
}
