<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\HasDataProviders;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use HasDataProviders;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
    }

    /** @test */
    public function roles_are_seeded_correctly_for_web_guard()
    {
        $expectedRoles = ['super_admin', 'admin', 'user', 'owner', 'manager'];

        foreach ($expectedRoles as $roleName) {
            $role = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            $this->assertNotNull($role, "Role {$roleName} should exist for web guard");
        }
    }

    /** @test */
    public function roles_are_seeded_correctly_for_accounts_guard()
    {
        $expectedRoles = ['super_admin', 'admin', 'user', 'owner', 'manager'];

        foreach ($expectedRoles as $roleName) {
            $role = Role::where('name', $roleName)
                ->where('guard_name', 'accounts')
                ->first();

            $this->assertNotNull($role, "Role {$roleName} should exist for accounts guard");
        }
    }

    /** @test */
    public function permissions_are_seeded_correctly_for_web_guard()
    {
        $expectedPermissions = [
            'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',
            'view_any_tenant', 'view_tenant', 'create_tenant', 'update_tenant', 'delete_tenant',
            'view_any_account', 'view_account', 'create_account', 'update_account', 'delete_account',
        ];

        foreach ($expectedPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->first();

            $this->assertNotNull($permission, "Permission {$permissionName} should exist for web guard");
        }
    }

    /** @test */
    public function super_admin_role_has_all_permissions()
    {
        $superAdminRole = Role::where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->first();

        $allPermissions = Permission::where('guard_name', 'web')->count();

        $this->assertEquals($allPermissions, $superAdminRole->permissions->count());
    }

    /** @test */
    public function user_can_be_assigned_role()
    {
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        $user->assignRole('user');

        $this->assertTrue($user->hasRole('user'));
    }

    /** @test */
    public function account_can_be_assigned_role()
    {
        $account = Account::factory()->create();

        $account->assignRole('admin');

        $this->assertTrue($account->hasRole('admin'));
    }

    /**
     * @test
     *
     * @dataProvider roleNamesProvider
     */
    public function user_can_be_assigned_any_role(string $roleName)
    {
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        $user->assignRole($roleName);

        $this->assertTrue($user->hasRole($roleName));
    }

    /**
     * @test
     *
     * @dataProvider permissionNamesProvider
     */
    public function super_admin_has_all_permissions(string $permissionName)
    {
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        $user->assignRole('super_admin');

        $this->assertTrue($user->hasPermissionTo($permissionName));
    }

    /** @test */
    public function seeded_super_admin_account_has_role()
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\AccountSeeder']);

        $account = Account::where('email', 'admin@admin.com')->first();

        $this->assertNotNull($account);
        $this->assertTrue($account->hasRole('super_admin'));
    }

    /** @test */
    public function seeded_super_admin_user_has_role()
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\AccountSeeder']);

        $user = User::where('email', 'admin@admin.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('super_admin'));
    }

    /** @test */
    public function seeded_regular_user_has_role()
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\UserSeeder']);

        $user = User::where('email', 'user@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('user'));
    }

    /** @test */
    public function seeded_regular_account_has_role()
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\UserSeeder']);

        $account = Account::where('email', 'user@example.com')->first();

        $this->assertNotNull($account);
        $this->assertTrue($account->hasRole('user'));
    }
}
