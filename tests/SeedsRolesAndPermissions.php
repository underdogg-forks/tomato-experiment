<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;

/**
 * Trait for seeding roles and permissions for tests.
 * Use this trait in tests that need roles/permissions.
 * 
 * Note: This seeds roles for each test to work correctly with RefreshDatabase trait,
 * which rolls back database transactions after each test.
 */
trait SeedsRolesAndPermissions
{
    /**
     * Seed roles and permissions for the current test.
     * Called in setUp() to ensure roles are present after RefreshDatabase rollback.
     */
    protected function seedRolesAndPermissions(): void
    {
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
    }
}
