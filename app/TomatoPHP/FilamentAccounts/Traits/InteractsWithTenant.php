<?php

namespace TomatoPHP\FilamentAccounts\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait InteractsWithTenant
{
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'account_tenant', 'account_id', 'tenant_id');
    }

    public function attachTenant(Tenant $tenant): void
    {
        $this->tenants()->syncWithoutDetaching([$tenant->getKey()]);
    }
}
