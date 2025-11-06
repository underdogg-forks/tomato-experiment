<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\SeedsRolesAndPermissions;
use Tests\TestCase;

class TenantCreationValidationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles (cached after first run)
        $this->seedRolesAndPermissions();
    }

    /** @test */
    public function tenant_creation_fails_without_account_id()
    {
        $this->expectException(ValidationException::class);

        Tenant::create([
            'id'        => 'tenant_test_001',
            'name'      => 'Test Tenant',
            'email'     => 'tenant@example.com',
            'phone'     => '+1234567890',
            'password'  => 'password',
            'is_active' => true,
            'packages'  => [],
            // No account_id provided - should fail validation
        ]);
    }

    /** @test */
    public function tenant_creation_fails_with_invalid_account_id()
    {
        $this->expectException(ValidationException::class);

        Tenant::create([
            'id'         => 'tenant_test_002',
            'name'       => 'Test Tenant',
            'email'      => 'tenant@example.com',
            'phone'      => '+1234567890',
            'password'   => 'password',
            'is_active'  => true,
            'packages'   => [],
            'account_id' => 9999, // Non-existent account
        ]);
    }

    /** @test */
    public function tenant_creation_fails_when_account_has_no_owner()
    {
        // Create an account without an owner user
        $account = Account::factory()->create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('does not have an owner user');

        Tenant::create([
            'id'         => 'tenant_test_003',
            'name'       => 'Test Tenant',
            'email'      => 'tenant@example.com',
            'phone'      => '+1234567890',
            'password'   => 'password',
            'is_active'  => true,
            'packages'   => [],
            'account_id' => $account->id,
        ]);
    }

    /** @test */
    public function tenant_creation_succeeds_when_account_has_owner()
    {
        // Create an account with an owner user
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        $tenant = Tenant::create([
            'id'         => 'tenant_test_004',
            'name'       => 'Test Tenant',
            'email'      => 'tenant@example.com',
            'phone'      => '+1234567890',
            'password'   => 'password',
            'is_active'  => true,
            'packages'   => [],
            'account_id' => $account->id,
        ]);

        $this->assertNotNull($tenant);
        $this->assertEquals($account->id, $tenant->account_id);
        $this->assertInstanceOf(Account::class, $tenant->account);
        $this->assertInstanceOf(User::class, $tenant->account->user);
    }

    /** @test */
    public function tenant_factory_creates_tenant_with_account_owner()
    {
        // Using factory should automatically create account with owner
        $tenant = Tenant::factory()->create();

        $this->assertNotNull($tenant->account_id);
        $this->assertInstanceOf(Account::class, $tenant->account);
        $this->assertInstanceOf(User::class, $tenant->account->user);
    }

    /** @test */
    public function multiple_tenants_can_belong_to_same_account_with_owner()
    {
        // Create an account with an owner user
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        // Create multiple tenants for the same account
        $tenant1 = Tenant::create([
            'id'         => 'tenant_multi_001',
            'name'       => 'Tenant 1',
            'email'      => 'tenant1@example.com',
            'phone'      => '+1234567890',
            'password'   => 'password',
            'is_active'  => true,
            'packages'   => [],
            'account_id' => $account->id,
        ]);

        $tenant2 = Tenant::create([
            'id'         => 'tenant_multi_002',
            'name'       => 'Tenant 2',
            'email'      => 'tenant2@example.com',
            'phone'      => '+1234567891',
            'password'   => 'password',
            'is_active'  => true,
            'packages'   => [],
            'account_id' => $account->id,
        ]);

        $this->assertCount(2, $account->tenants);
        $this->assertTrue($account->tenants->contains($tenant1));
        $this->assertTrue($account->tenants->contains($tenant2));
    }
}
