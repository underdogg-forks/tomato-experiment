<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // 1. Seed Shield roles and permissions
        $this->call(ShieldSeeder::class);

        // 2. Seed super admin account
        $this->call(AccountSeeder::class);

        // 3. Seed tenants
        $this->call(TenantSeeder::class);

        $this->command->info('Database seeding completed successfully!');
    }
}
