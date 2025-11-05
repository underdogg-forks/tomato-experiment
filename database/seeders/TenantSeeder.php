<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the super admin account
        $superAdmin = Account::where('email', 'admin@admin.com')->first();

        if ( ! $superAdmin) {
            $this->command->error('Super Admin account not found. Please run AccountSeeder first.');

            return;
        }

        // Create 10 tenants
        for ($i = 1; $i <= 10; $i++) {
            $tenant = Tenant::firstOrCreate(
                ['id' => 'tenant_' . mb_str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'name'       => "Tenant Company {$i}",
                    'email'      => "tenant{$i}@example.com",
                    'phone'      => '+1234567890' . $i,
                    'password'   => Hash::make('password'),
                    'is_active'  => true,
                    'packages'   => [],
                    'account_id' => $superAdmin->id,
                ]
            );

            // Create domain for the tenant
            $tenant->domains()->firstOrCreate(
                ['domain' => "tenant{$i}.localhost"],
                ['domain' => "tenant{$i}.localhost"]
            );

            $this->command->info("Tenant {$i} created: {$tenant->name} ({$tenant->id})");
        }

        $this->command->info('10 Tenants seeded successfully!');
    }
}
