<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Tests\AuthenticatedTestCase;

/**
 * Example test using AuthenticatedTestCase.
 * This demonstrates how to extend the abstract test case for tests requiring authentication.
 */
class AuthenticatedUserActionsTest extends AuthenticatedTestCase
{
    /** @test */
    public function authenticated_user_can_access_their_account()
    {
        $account = $this->getAccount();

        $this->assertEquals($this->user->account_id, $account->id);
        $this->assertEquals($this->user->id, $account->user->id);
    }

    /** @test */
    public function authenticated_user_has_user_role()
    {
        $this->assertTrue($this->user->hasRole('user'));
    }

    /** @test */
    public function authenticated_user_account_can_have_tenants()
    {
        // Create tenants for the authenticated user's account
        $tenant1 = Tenant::factory()->create(['account_id' => $this->account->id]);
        $tenant2 = Tenant::factory()->create(['account_id' => $this->account->id]);

        $this->account->refresh();

        $this->assertCount(2, $this->account->tenants);
        $this->assertTrue($this->account->tenants->contains($tenant1));
        $this->assertTrue($this->account->tenants->contains($tenant2));
    }

    /** @test */
    public function authenticated_user_is_account_owner()
    {
        // The user should be the owner of their account
        $this->assertNotNull($this->account->user);
        $this->assertEquals($this->user->id, $this->account->user->id);
    }
}
