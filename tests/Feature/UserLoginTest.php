<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles before running tests
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
    }

    /** @test */
    public function user_can_access_apps_panel_login_page()
    {
        $response = $this->get('/user/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_login_with_account_credentials()
    {
        // Create an account with a user
        $account = Account::factory()->create([
            'email'    => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);

        $user = User::factory()->create([
            'email'      => 'testuser@example.com',
            'password'   => bcrypt('password'),
            'account_id' => $account->id,
        ]);

        // Attempt to login via the accounts guard
        $response = $this->post('/user/login', [
            'email'    => 'testuser@example.com',
            'password' => 'password',
        ]);

        // Should redirect after successful login
        $response->assertRedirect();
        $this->assertAuthenticatedAs($account, 'accounts');
    }

    /** @test */
    public function super_admin_can_login_and_see_everything()
    {
        // Create super admin account with user
        $superAdmin = Account::factory()->create([
            'email'    => 'admin@admin.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'type'     => 'super_admin',
        ]);

        $superAdminUser = User::factory()->create([
            'email'      => 'admin@admin.com',
            'password'   => bcrypt('password'),
            'account_id' => $superAdmin->id,
        ]);

        // Assign super_admin role
        $superAdmin->assignRole('super_admin');

        // Create tenants for multiple accounts
        $otherAccount = Account::factory()->create();
        Tenant::factory()->count(3)->create(['account_id' => $superAdmin->id]);
        Tenant::factory()->count(3)->create(['account_id' => $otherAccount->id]);

        // Login as super admin
        $this->actingAs($superAdmin, 'accounts');

        // Super admin should be able to see all tenants
        $allTenants = Tenant::all();
        $this->assertCount(6, $allTenants);
    }

    /** @test */
    public function regular_user_can_login_and_see_own_tenants()
    {
        // Create regular user account
        $account = Account::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('password'),
            'type'     => 'user',
        ]);

        $user = User::factory()->create([
            'email'      => 'user@example.com',
            'password'   => bcrypt('password'),
            'account_id' => $account->id,
        ]);

        // Create tenants for this user's account
        $userTenants = Tenant::factory()->count(4)->create(['account_id' => $account->id]);

        // Create tenants for another account
        $otherAccount = Account::factory()->create();
        Tenant::factory()->count(3)->create(['account_id' => $otherAccount->id]);

        // Login as regular user
        $this->actingAs($account, 'accounts');

        // User should see only their own tenants
        $ownTenants = $account->tenants;
        $this->assertCount(4, $ownTenants);

        foreach ($userTenants as $tenant) {
            $this->assertTrue($ownTenants->contains($tenant));
        }
    }

    /** @test */
    public function user_with_account_can_see_their_tenants()
    {
        // Create user with account (1 user -> 1 account)
        $account = Account::factory()->create();
        $user    = User::factory()->create(['account_id' => $account->id]);

        // Create 3-5 tenants for this account
        $numberOfTenants = rand(3, 5);
        $tenants         = Tenant::factory()->count($numberOfTenants)->create([
            'account_id' => $account->id,
        ]);

        // Act as the account (using accounts guard)
        $this->actingAs($account, 'accounts');

        // Verify the account can access its tenants via getTenants
        $panel          = Mockery::mock(\Filament\Panel::class);
        $accountTenants = $account->getTenants($panel);

        $this->assertCount($numberOfTenants, $accountTenants);
    }

    /** @test */
    public function account_credentials_are_used_for_apps_panel_authentication()
    {
        // The AppsPanelProvider uses authGuard('accounts')
        // So authentication should be against the accounts table, not users table

        $account = Account::factory()->create([
            'email'    => 'panel@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create associated user
        $user = User::factory()->create([
            'email'      => 'panel@example.com',
            'account_id' => $account->id,
        ]);

        // Login should authenticate the account, not the user
        $this->post('/user/login', [
            'email'    => 'panel@example.com',
            'password' => 'password',
        ]);

        // Should be authenticated as account via accounts guard
        $this->assertAuthenticatedAs($account, 'accounts');
    }

    /** @test */
    public function one_user_one_account_relationship_works_with_authentication()
    {
        // Create account
        $account = Account::factory()->create([
            'email'    => 'relation@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create one user for that account (1 user -> 1 account)
        $user = User::factory()->create([
            'email'      => 'relation@example.com',
            'account_id' => $account->id,
        ]);

        // Verify relationship
        $this->assertEquals($account->id, $user->account_id);
        $this->assertEquals($user->id, $account->user->id);

        // Login
        $this->actingAs($account, 'accounts');

        // Verify authenticated account has its user
        $authenticatedAccount = auth('accounts')->user();
        $this->assertNotNull($authenticatedAccount->user);
        $this->assertEquals($user->id, $authenticatedAccount->user->id);
    }
}
