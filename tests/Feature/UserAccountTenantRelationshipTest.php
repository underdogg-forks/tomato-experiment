<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UserAccountTenantRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_belongs_to_an_account()
    {
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(Account::class, $user->account);
        $this->assertEquals($account->id, $user->account->id);
    }

    /** @test */
    public function an_account_has_one_user()
    {
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(User::class, $account->user);
        $this->assertEquals($user->id, $account->user->id);
    }

    /** @test */
    public function an_account_can_have_multiple_tenants()
    {
        $account = Account::factory()->create();
        $tenant1 = Tenant::factory()->create(['account_id' => $account->id]);
        $tenant2 = Tenant::factory()->create(['account_id' => $account->id]);
        $tenant3 = Tenant::factory()->create(['account_id' => $account->id]);

        $this->assertCount(3, $account->tenants);
        $this->assertTrue($account->tenants->contains($tenant1));
        $this->assertTrue($account->tenants->contains($tenant2));
        $this->assertTrue($account->tenants->contains($tenant3));
    }

    /** @test */
    public function a_tenant_belongs_to_an_account()
    {
        $account = Account::factory()->create();
        $tenant  = Tenant::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(Account::class, $tenant->account);
        $this->assertEquals($account->id, $tenant->account->id);
    }

    /** @test */
    public function get_tenants_returns_account_tenants()
    {
        $account = Account::factory()->create();
        Tenant::factory()->count(3)->create(['account_id' => $account->id]);

        $panel   = Mockery::mock(\Filament\Panel::class);
        $tenants = $account->getTenants($panel);

        $this->assertCount(3, $tenants);
    }

    /** @test */
    public function user_factory_with_account_creates_both_user_and_account()
    {
        $user = User::factory()->withAccount()->create();

        $this->assertNotNull($user->account_id);
        $this->assertInstanceOf(Account::class, $user->account);
    }

    /** @test */
    public function deleting_account_cascades_to_user()
    {
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        $userId = $user->id;
        $account->delete();

        $this->assertNull(User::find($userId));
    }

    /** @test */
    public function one_user_owns_one_account_relationship()
    {
        // Create account
        $account = Account::factory()->create([
            'name'  => 'Test Account',
            'email' => 'test@example.com',
        ]);

        // Create user for that account
        $user = User::factory()->create([
            'account_id' => $account->id,
            'email'      => 'test@example.com',
        ]);

        // Assertions
        $this->assertEquals($account->id, $user->account_id);
        $this->assertEquals($user->id, $account->user->id);
    }

    /** @test */
    public function one_account_owns_multiple_tenants_relationship()
    {
        $account = Account::factory()->create();

        // Create 3-5 tenants
        $numberOfTenants = rand(3, 5);
        $tenants         = Tenant::factory()->count($numberOfTenants)->create([
            'account_id' => $account->id,
        ]);

        // Assertions
        $this->assertCount($numberOfTenants, $account->tenants);
        foreach ($tenants as $tenant) {
            $this->assertEquals($account->id, $tenant->account_id);
        }
    }

    /** @test */
    public function complete_hierarchy_one_user_one_account_multiple_tenants()
    {
        // Create the hierarchy: 1 User -> 1 Account -> 3-5 Tenants
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);
        $tenants = Tenant::factory()->count(4)->create(['account_id' => $account->id]);

        // Verify hierarchy
        $this->assertEquals($account->id, $user->account_id);
        $this->assertEquals($user->id, $account->user->id);
        $this->assertCount(4, $account->tenants);

        foreach ($tenants as $tenant) {
            $this->assertEquals($account->id, $tenant->account_id);
        }
    }
}
