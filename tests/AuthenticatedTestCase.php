<?php

namespace Tests;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Abstract test case for tests that require an authenticated user.
 * Extend this class for tests that need a logged-in user.
 */
abstract class AuthenticatedTestCase extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;

    protected User $user;

    protected Account $account;

    /**
     * Set up the test case with an authenticated user.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions (cached after first run)
        $this->seedRolesAndPermissions();

        // Create an account with an owner user
        $this->account = Account::factory()->create();
        $this->user    = User::factory()->create(['account_id' => $this->account->id]);

        // Assign user role
        $this->user->assignRole('user');

        // Authenticate the user
        $this->actingAs($this->user);
    }

    /**
     * Get the authenticated user.
     */
    protected function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the authenticated user's account.
     */
    protected function getAccount(): Account
    {
        return $this->account;
    }
}
