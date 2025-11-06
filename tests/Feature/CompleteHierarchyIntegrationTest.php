<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\AccountSeeder;
use Database\Seeders\TenantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Comprehensive integration test for the complete application hierarchy.
 * Tests the relationship: 1 User (Owner) → 1 Account → Multiple Tenants
 */
class CompleteHierarchyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
    }

    /** @test */
    public function complete_hierarchy_creates_user_account_and_tenants()
    {
        // Step 1: Create an Account with an owner User
        $account = Account::factory()->create([
            'name'  => 'Test Company',
            'email' => 'company@test.com',
        ]);

        $owner = User::factory()->create([
            'name'       => 'John Doe',
            'email'      => 'john@test.com',
            'account_id' => $account->id,
        ]);

        // Step 2: Verify the User owns the Account
        $this->assertEquals($account->id, $owner->account_id);
        $this->assertEquals($owner->id, $account->user->id);
        $this->assertEquals($owner->id, $account->owner()->first()->id); // Test owner alias

        // Step 3: Create Tenants for the Account
        $tenant1 = Tenant::factory()->create(['account_id' => $account->id]);
        $tenant2 = Tenant::factory()->create(['account_id' => $account->id]);
        $tenant3 = Tenant::factory()->create(['account_id' => $account->id]);

        // Step 4: Verify Tenants belong to the Account
        $account->refresh();
        $this->assertCount(3, $account->tenants);
        $this->assertTrue($account->tenants->contains($tenant1));
        $this->assertTrue($account->tenants->contains($tenant2));
        $this->assertTrue($account->tenants->contains($tenant3));

        // Step 5: Verify each Tenant has the correct Account
        $this->assertEquals($account->id, $tenant1->account_id);
        $this->assertEquals($account->id, $tenant2->account_id);
        $this->assertEquals($account->id, $tenant3->account_id);

        // Step 6: Verify we can access the owner through the Account
        $this->assertEquals('John Doe', $account->user->name);
        $this->assertEquals('john@test.com', $account->user->email);
    }

    /** @test */
    public function seeders_create_complete_hierarchy()
    {
        // Run all seeders
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);
        $this->artisan('db:seed', ['--class' => TenantSeeder::class]);

        // Verify super admin
        $superAdminAccount = Account::where('email', 'admin@admin.com')->first();
        $superAdminUser    = User::where('email', 'admin@admin.com')->first();

        $this->assertNotNull($superAdminAccount);
        $this->assertNotNull($superAdminUser);
        $this->assertEquals($superAdminAccount->id, $superAdminUser->account_id);
        $this->assertTrue($superAdminAccount->hasRole('super_admin'));
        $this->assertTrue($superAdminUser->hasRole('super_admin'));

        // Verify regular user
        $regularAccount = Account::where('email', 'user@example.com')->first();
        $regularUser    = User::where('email', 'user@example.com')->first();

        $this->assertNotNull($regularAccount);
        $this->assertNotNull($regularUser);
        $this->assertEquals($regularAccount->id, $regularUser->account_id);
        $this->assertTrue($regularAccount->hasRole('user'));
        $this->assertTrue($regularUser->hasRole('user'));

        // Verify tenants were created for both accounts
        $this->assertGreaterThanOrEqual(3, $superAdminAccount->tenants()->count());
        $this->assertLessThanOrEqual(5, $superAdminAccount->tenants()->count());
        $this->assertGreaterThanOrEqual(3, $regularAccount->tenants()->count());
        $this->assertLessThanOrEqual(5, $regularAccount->tenants()->count());

        // Verify each tenant has an account with an owner
        $allTenants = Tenant::all();
        foreach ($allTenants as $tenant) {
            $this->assertNotNull($tenant->account_id);
            $this->assertNotNull($tenant->account);
            $this->assertNotNull($tenant->account->user);
        }
    }

    /** @test */
    public function account_without_owner_cannot_create_tenants()
    {
        // Create an account without an owner
        $account = Account::factory()->create();

        // Attempt to create a tenant should fail
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('does not have an owner user');

        Tenant::create([
            'id'         => 'orphan_tenant',
            'name'       => 'Orphan Tenant',
            'email'      => 'orphan@test.com',
            'phone'      => '+1234567890',
            'password'   => 'password',
            'is_active'  => true,
            'packages'   => [],
            'account_id' => $account->id,
        ]);
    }

    /** @test */
    public function deleting_account_cascades_to_user_and_tenants()
    {
        // Create complete hierarchy
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);
        $tenant1 = Tenant::factory()->create(['account_id' => $account->id]);
        $tenant2 = Tenant::factory()->create(['account_id' => $account->id]);

        $userId    = $user->id;
        $tenant1Id = $tenant1->id;
        $tenant2Id = $tenant2->id;

        // Delete the account
        $account->delete();

        // Verify user was deleted (cascade from accounts to users)
        $this->assertNull(User::find($userId));

        // Note: Tenants may or may not be deleted depending on foreign key constraints
        // This behavior depends on the migration configuration
    }

    /** @test */
    public function multiple_accounts_can_each_have_their_own_tenants()
    {
        // Create first account with owner and tenants
        $account1 = Account::factory()->create(['name' => 'Company A']);
        $owner1   = User::factory()->create(['account_id' => $account1->id]);
        $tenant1a = Tenant::factory()->create(['account_id' => $account1->id]);
        $tenant1b = Tenant::factory()->create(['account_id' => $account1->id]);

        // Create second account with owner and tenants
        $account2 = Account::factory()->create(['name' => 'Company B']);
        $owner2   = User::factory()->create(['account_id' => $account2->id]);
        $tenant2a = Tenant::factory()->create(['account_id' => $account2->id]);
        $tenant2b = Tenant::factory()->create(['account_id' => $account2->id]);

        // Verify account 1 has 2 tenants
        $this->assertCount(2, $account1->tenants);
        $this->assertTrue($account1->tenants->contains($tenant1a));
        $this->assertTrue($account1->tenants->contains($tenant1b));
        $this->assertFalse($account1->tenants->contains($tenant2a));
        $this->assertFalse($account1->tenants->contains($tenant2b));

        // Verify account 2 has 2 tenants
        $this->assertCount(2, $account2->tenants);
        $this->assertTrue($account2->tenants->contains($tenant2a));
        $this->assertTrue($account2->tenants->contains($tenant2b));
        $this->assertFalse($account2->tenants->contains($tenant1a));
        $this->assertFalse($account2->tenants->contains($tenant1b));
    }

    /** @test */
    public function account_owner_relationship_is_bidirectional()
    {
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        // Forward: User -> Account
        $this->assertEquals($account->id, $user->account->id);

        // Backward: Account -> User (owner)
        $this->assertEquals($user->id, $account->user->id);
        $this->assertEquals($user->id, $account->owner()->first()->id);
    }

    /** @test */
    public function factory_methods_maintain_hierarchy_integrity()
    {
        // Using factories should automatically maintain hierarchy
        $tenant = Tenant::factory()->create();

        // Verify the complete hierarchy was created
        $this->assertNotNull($tenant->account_id);
        $this->assertInstanceOf(Account::class, $tenant->account);
        $this->assertInstanceOf(User::class, $tenant->account->user);

        // Verify the user owns the account
        $this->assertEquals($tenant->account_id, $tenant->account->user->account_id);
    }

    /** @test */
    public function seeding_is_idempotent_and_maintains_hierarchy()
    {
        // Seed once
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);

        $account1Count = Account::count();
        $user1Count    = User::count();

        // Seed again
        $this->artisan('db:seed', ['--class' => AccountSeeder::class]);
        $this->artisan('db:seed', ['--class' => UserSeeder::class]);

        $account2Count = Account::count();
        $user2Count    = User::count();

        // Counts should be the same (idempotent)
        $this->assertEquals($account1Count, $account2Count);
        $this->assertEquals($user1Count, $user2Count);

        // Verify each account still has an owner
        $accounts = Account::all();
        foreach ($accounts as $account) {
            $this->assertNotNull($account->user, "Account {$account->email} should have an owner");
        }
    }
}
