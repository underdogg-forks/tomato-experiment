<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds 3-5 tenants for each account in the system.
     */
    public function run(): void
    {
        // Get all accounts
        $accounts = Account::all();

        if ($accounts->isEmpty()) {
            $this->command->error('No accounts found. Please run AccountSeeder first.');

            return;
        }

        $totalTenantsCreated = 0;

        foreach ($accounts as $account) {
            // Verify account has an owner (user)
            if ( ! $account->user) {
                $this->command->warn("Account {$account->name} ({$account->email}) has no owner user. Skipping tenant creation.");

                continue;
            }

            // Create 3-5 random tenants per account
            $numberOfTenants = rand(3, 5);

            $this->command->info("Creating {$numberOfTenants} tenants for account: {$account->name} ({$account->email})");

            for ($i = 1; $i <= $numberOfTenants; $i++) {
                $totalTenantsCreated++;
                $tenantId = 'tenant_' . mb_str_pad($totalTenantsCreated, 3, '0', STR_PAD_LEFT);

                $tenant = Tenant::firstOrCreate(
                    ['id' => $tenantId],
                    [
                        'name'       => "{$account->name} - Tenant {$i}",
                        'email'      => "tenant{$totalTenantsCreated}@example.com",
                        'phone'      => '+1234567890' . $totalTenantsCreated,
                        'password'   => Hash::make('password'),
                        'is_active'  => true,
                        'packages'   => [],
                        'account_id' => $account->id,
                    ]
                );

                // Create domain for the tenant
                $tenant->domains()->firstOrCreate(
                    ['domain' => "tenant{$totalTenantsCreated}.localhost"],
                    ['domain' => "tenant{$totalTenantsCreated}.localhost"]
                );

                // Create a user inside the tenant's database
                $tenant->run(function () use ($totalTenantsCreated) {
                    User::firstOrCreate(
                        ['email' => "tenant{$totalTenantsCreated}@example.com"],
                        [
                            'name'     => "Tenant User {$totalTenantsCreated}",
                            'email'    => "tenant{$totalTenantsCreated}@example.com",
                            'password' => Hash::make('password'),
                        ]
                    );
                });

                $this->command->info("  - Tenant {$i} created: {$tenant->name} ({$tenant->id})");
            }
        }

        $this->command->info("{$totalTenantsCreated} Tenants seeded successfully across {$accounts->count()} accounts!");
    }
}
