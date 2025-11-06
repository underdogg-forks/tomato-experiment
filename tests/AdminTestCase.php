<?php

namespace Tests;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Abstract test case for tests that require an admin user.
 * Extend this class for tests that need admin privileges.
 */
abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;

    protected User $adminUser;

    protected Account $adminAccount;

    /**
     * Set up the test case with an authenticated admin user.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions (cached after first run)
        $this->seedRolesAndPermissions();

        // Create an account with an admin user
        $this->adminAccount = Account::factory()->create([
            'email'    => 'admin@test.com',
            'username' => 'admin',
            'name'     => 'Admin',
        ]);

        $this->adminUser = User::factory()->create([
            'account_id' => $this->adminAccount->id,
            'email'      => 'admin@test.com',
            'name'       => 'Admin',
        ]);

        // Assign admin role
        $this->adminUser->assignRole('admin');
        $this->adminAccount->assignRole('admin');

        // Authenticate the admin user
        $this->actingAs($this->adminUser);
    }

    /**
     * Get the authenticated admin user.
     */
    protected function getAdminUser(): User
    {
        return $this->adminUser;
    }

    /**
     * Get the authenticated admin account.
     */
    protected function getAdminAccount(): Account
    {
        return $this->adminAccount;
    }
}
