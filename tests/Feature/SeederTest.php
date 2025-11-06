<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\AccountSeeder;
use Database\Seeders\TenantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles before running tests
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
    }

    /** @test */
    public function account_seeder_creates_super_admin_account_and_user()
    {
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);

        $account = Account::where('email', 'admin@admin.com')->first();
        $this->assertNotNull($account);
        $this->assertEquals('Super Admin', $account->name);
        $this->assertEquals('admin', $account->username);
        $this->assertTrue($account->hasRole('super_admin'));

        // Check that user was created for this account
        $user = User::where('email', 'admin@admin.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($account->id, $user->account_id);
        $this->assertEquals('Super Admin', $user->name);
    }

    /** @test */
    public function user_seeder_creates_regular_user_with_account()
    {
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);

        $account = Account::where('email', 'user@example.com')->first();
        $this->assertNotNull($account);
        $this->assertEquals('Regular User Account', $account->name);

        $user = User::where('email', 'user@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($account->id, $user->account_id);
        $this->assertEquals('Regular User', $user->name);
    }

    /** @test */
    public function tenant_seeder_creates_3_to_5_tenants_per_account()
    {
        // Create test accounts first
        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $this->artisan('db:seed', ['--class' => TenantSeeder::class]);

        // Check account 1 has 3-5 tenants
        $account1->refresh();
        $tenantCount1 = $account1->tenants()->count();
        $this->assertGreaterThanOrEqual(3, $tenantCount1);
        $this->assertLessThanOrEqual(5, $tenantCount1);

        // Check account 2 has 3-5 tenants
        $account2->refresh();
        $tenantCount2 = $account2->tenants()->count();
        $this->assertGreaterThanOrEqual(3, $tenantCount2);
        $this->assertLessThanOrEqual(5, $tenantCount2);

        // Verify all tenants have account_id set
        $allTenants = Tenant::all();
        foreach ($allTenants as $tenant) {
            $this->assertNotNull($tenant->account_id);
            $this->assertContains($tenant->account_id, [$account1->id, $account2->id]);
        }
    }

    /** @test */
    public function full_database_seeding_creates_proper_hierarchy()
    {
        // Run full seeding
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);
        $this->artisan('db:seed', ['--class' => TenantSeeder::class]);

        // Verify super admin
        $superAdmin = Account::where('email', 'admin@admin.com')->first();
        $this->assertNotNull($superAdmin);
        $superAdminUser = $superAdmin->user;
        $this->assertNotNull($superAdminUser);
        $this->assertEquals('admin@admin.com', $superAdminUser->email);

        // Verify regular user
        $regularAccount = Account::where('email', 'user@example.com')->first();
        $this->assertNotNull($regularAccount);
        $regularUser = $regularAccount->user;
        $this->assertNotNull($regularUser);
        $this->assertEquals('user@example.com', $regularUser->email);

        // Verify tenants were created for both accounts
        $superAdminTenants = $superAdmin->tenants()->count();
        $this->assertGreaterThanOrEqual(3, $superAdminTenants);
        $this->assertLessThanOrEqual(5, $superAdminTenants);

        $regularUserTenants = $regularAccount->tenants()->count();
        $this->assertGreaterThanOrEqual(3, $regularUserTenants);
        $this->assertLessThanOrEqual(5, $regularUserTenants);
    }

    /** @test */
    public function seeding_is_idempotent()
    {
        // Run seeders twice
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);

        // Should still only have one super admin and one regular user
        $this->assertEquals(1, Account::where('email', 'admin@admin.com')->count());
        $this->assertEquals(1, User::where('email', 'admin@admin.com')->count());
        $this->assertEquals(1, Account::where('email', 'user@example.com')->count());
        $this->assertEquals(1, User::where('email', 'user@example.com')->count());
    }

    /** @test */
    public function each_account_gets_correct_number_of_tenants()
    {
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);
        $this->artisan('db:seed', ['--class' => TenantSeeder::class]);

        $accounts = Account::all();

        foreach ($accounts as $account) {
            $tenantCount = $account->tenants()->count();
            $this->assertGreaterThanOrEqual(3, $tenantCount, "Account {$account->name} should have at least 3 tenants");
            $this->assertLessThanOrEqual(5, $tenantCount, "Account {$account->name} should have at most 5 tenants");
        }
    }
}
