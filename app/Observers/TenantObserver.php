<?php

namespace App\Observers;

use App\Models\Tenant;
use Illuminate\Validation\ValidationException;

class TenantObserver
{
    /**
     * Handle the Tenant "creating" event.
     * Validates that the account has an owner before creating the tenant.
     */
    public function creating(Tenant $tenant): void
    {
        // Check if account_id is set
        if (!$tenant->account_id) {
            throw ValidationException::withMessages([
                'account_id' => ['Tenant must be associated with an account.'],
            ]);
        }

        // Load the account to check if it has an owner
        $account = $tenant->account;

        if (!$account) {
            throw ValidationException::withMessages([
                'account_id' => ['Associated account not found.'],
            ]);
        }

        // Check if account has an owner (user)
        if (!$account->user) {
            throw ValidationException::withMessages([
                'account' => ["Account {$account->name} does not have an owner user. Cannot create tenant."],
            ]);
        }
    }
}
