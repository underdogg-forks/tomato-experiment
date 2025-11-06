<?php

namespace Tests;

use App\Enums\AccountType;
use App\Enums\LoginBy;

/**
 * Trait for common data providers used across tests.
 * Include this trait in your test classes to access shared data providers.
 */
trait HasDataProviders
{
    /**
     * Provides valid account data for testing.
     *
     * @return array
     */
    public static function validAccountDataProvider(): array
    {
        return [
            'basic account' => [
                [
                    'name'      => 'Test Account',
                    'email'     => 'test@example.com',
                    'phone'     => '+1234567890',
                    'username'  => 'testuser',
                    'loginBy'   => LoginBy::EMAIL,
                    'address'   => 'Test Address',
                    'lang'      => 'en',
                    'password'  => 'password123',
                    'type'      => AccountType::USER,
                    'is_active' => true,
                ],
            ],
            'admin account' => [
                [
                    'name'      => 'Admin Account',
                    'email'     => 'admin@example.com',
                    'phone'     => '+1234567891',
                    'username'  => 'adminuser',
                    'loginBy'   => LoginBy::EMAIL,
                    'address'   => 'Admin Address',
                    'lang'      => 'en',
                    'password'  => 'admin123',
                    'type'      => AccountType::SUPER_ADMIN,
                    'is_active' => true,
                ],
            ],
        ];
    }

    /**
     * Provides valid user data for testing.
     *
     * @return array
     */
    public static function validUserDataProvider(): array
    {
        return [
            'basic user' => [
                [
                    'name'              => 'Test User',
                    'email'             => 'user@example.com',
                    'password'          => 'password123',
                    'email_verified_at' => now(),
                ],
            ],
            'admin user' => [
                [
                    'name'              => 'Admin User',
                    'email'             => 'admin@example.com',
                    'password'          => 'admin123',
                    'email_verified_at' => now(),
                ],
            ],
        ];
    }

    /**
     * Provides valid tenant data for testing.
     *
     * @return array
     */
    public static function validTenantDataProvider(): array
    {
        return [
            'basic tenant' => [
                [
                    'name'      => 'Test Tenant',
                    'email'     => 'tenant@example.com',
                    'phone'     => '+1234567892',
                    'password'  => 'password123',
                    'is_active' => true,
                    'packages'  => [],
                ],
            ],
            'premium tenant' => [
                [
                    'name'      => 'Premium Tenant',
                    'email'     => 'premium@example.com',
                    'phone'     => '+1234567893',
                    'password'  => 'premium123',
                    'is_active' => true,
                    'packages'  => ['package1', 'package2'],
                ],
            ],
        ];
    }

    /**
     * Provides role names for testing.
     *
     * @return array
     */
    public static function roleNamesProvider(): array
    {
        return [
            'super_admin' => ['super_admin'],
            'admin'       => ['admin'],
            'user'        => ['user'],
            'owner'       => ['owner'],
            'manager'     => ['manager'],
        ];
    }

    /**
     * Provides permission names for testing.
     *
     * @return array
     */
    public static function permissionNamesProvider(): array
    {
        return [
            'view_any_user'    => ['view_any_user'],
            'view_user'        => ['view_user'],
            'create_user'      => ['create_user'],
            'update_user'      => ['update_user'],
            'delete_user'      => ['delete_user'],
            'view_any_tenant'  => ['view_any_tenant'],
            'view_tenant'      => ['view_tenant'],
            'create_tenant'    => ['create_tenant'],
            'update_tenant'    => ['update_tenant'],
            'delete_tenant'    => ['delete_tenant'],
            'view_any_account' => ['view_any_account'],
            'view_account'     => ['view_account'],
            'create_account'   => ['create_account'],
            'update_account'   => ['update_account'],
            'delete_account'   => ['delete_account'],
        ];
    }
}
