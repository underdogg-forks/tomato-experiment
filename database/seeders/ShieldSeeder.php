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

        // Create roles for 'web' guard
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user       = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $owner      = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $manager    = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        // Create roles for 'accounts' guard
        $accountSuperAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'accounts']);
        $accountAdmin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'accounts']);
        $accountUser       = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'accounts']);
        $accountOwner      = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'accounts']);
        $accountManager    = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'accounts']);

        // Create basic permissions for 'web' guard
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

        // Create basic permissions for 'accounts' guard
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'accounts']);
        }

        // Give super_admin all permissions (web guard)
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'web')->get());

        // Give super_admin all permissions (accounts guard)
        $accountSuperAdmin->givePermissionTo(Permission::where('guard_name', 'accounts')->get());

        $this->command->info('Shield roles and permissions seeded successfully for both web and accounts guards!');
    }
}
