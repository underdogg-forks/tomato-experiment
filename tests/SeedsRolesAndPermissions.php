<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;

/**
 * Trait for seeding roles and permissions once for all tests.
 * Use this trait in tests that need roles/permissions to improve performance.
 */
trait SeedsRolesAndPermissions
{
    /**
     * Track if roles have been seeded in this test run.
     */
    protected static bool $rolesSeeded = false;

    /**
     * Seed roles and permissions if not already seeded.
     */
    protected function seedRolesAndPermissions(): void
    {
        if ( ! static::$rolesSeeded) {
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
            static::$rolesSeeded = true;
        }
    }

    /**
     * Reset the seeded flag when tests are complete.
     */
    public static function tearDownAfterClass(): void
    {
        static::$rolesSeeded = false;
        parent::tearDownAfterClass();
    }
}
