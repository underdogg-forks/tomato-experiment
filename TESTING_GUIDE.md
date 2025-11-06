# Testing Architecture

This document describes the testing architecture for the Tomato Experiment application.

## Overview

The application uses a structured testing approach with abstract test cases and centralized data providers to minimize code duplication and ensure consistency across tests.

## Architecture Components

### 1. Base Test Case

**Location**: `tests/TestCase.php`

The base test case that all other test cases extend. Provides the `CreatesApplication` trait.

### 2. Abstract Test Cases

#### AuthenticatedTestCase

**Location**: `tests/AuthenticatedTestCase.php`

**Purpose**: Provides a pre-authenticated user context for tests.

**Features**:
- Automatically seeds roles and permissions
- Creates an account with an owner user
- Authenticates the user before each test
- Assigns 'user' role to the authenticated user
- Provides helper methods: `getUser()` and `getAccount()`

**Usage**:
```php
use Tests\AuthenticatedTestCase;

class MyFeatureTest extends AuthenticatedTestCase
{
    /** @test */
    public function authenticated_user_can_access_dashboard()
    {
        // $this->user is already authenticated
        $response = $this->get('/dashboard');
        $response->assertOk();
    }
}
```

#### AdminTestCase

**Location**: `tests/AdminTestCase.php`

**Purpose**: Provides a pre-authenticated admin user context for tests.

**Features**:
- Automatically seeds roles and permissions
- Creates an admin account with an owner user
- Authenticates the admin user before each test
- Assigns 'admin' role to both user and account
- Provides helper methods: `getAdminUser()` and `getAdminAccount()`

**Usage**:
```php
use Tests\AdminTestCase;

class AdminFeatureTest extends AdminTestCase
{
    /** @test */
    public function admin_can_delete_users()
    {
        // $this->adminUser is already authenticated as admin
        $response = $this->delete('/api/users/1');
        $response->assertOk();
    }
}
```

### 3. Data Providers

#### HasDataProviders Trait

**Location**: `tests/HasDataProviders.php`

**Purpose**: Centralized data providers for consistent test data across all tests.

**Available Providers**:

1. **validAccountDataProvider()**: Provides valid account data sets
2. **validUserDataProvider()**: Provides valid user data sets
3. **validTenantDataProvider()**: Provides valid tenant data sets
4. **roleNamesProvider()**: Provides role names (super_admin, admin, user, owner, manager)
5. **permissionNamesProvider()**: Provides permission names (view_any_user, create_user, etc.)

**Usage**:
```php
use Tests\HasDataProviders;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    use HasDataProviders;

    /**
     * @test
     * @dataProvider validAccountDataProvider
     */
    public function can_create_account_with_valid_data(array $accountData)
    {
        $account = Account::create($accountData);
        
        $this->assertDatabaseHas('accounts', [
            'email' => $accountData['email'],
        ]);
    }
}
```

## Test Organization

### Feature Tests

Located in `tests/Feature/`, feature tests verify application behavior from an end-user perspective.

**Examples**:
- `TenantCreationValidationTest.php`: Tests tenant creation validation rules
- `RolesAndPermissionsTest.php`: Tests role and permission system
- `AuthenticatedUserActionsTest.php`: Tests actions performed by authenticated users
- `UserAccountTenantRelationshipTest.php`: Tests model relationships
- `SeederTest.php`: Tests database seeders

### Unit Tests

Located in `tests/Unit/`, unit tests verify individual components in isolation.

## Best Practices

### 1. Extend the Right Abstract Test Case

```php
// For tests requiring a regular authenticated user
class MyTest extends AuthenticatedTestCase { }

// For tests requiring an admin user
class MyAdminTest extends AdminTestCase { }

// For tests that don't need authentication
class MyBasicTest extends TestCase 
{
    use RefreshDatabase;
}
```

### 2. Use Data Providers for Test Data

```php
use Tests\HasDataProviders;

class MyTest extends TestCase
{
    use RefreshDatabase;
    use HasDataProviders;

    /**
     * @test
     * @dataProvider roleNamesProvider
     */
    public function user_can_have_role(string $role)
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        
        $this->assertTrue($user->hasRole($role));
    }
}
```

### 3. Seed Roles Before Testing Permissions

```php
protected function setUp(): void
{
    parent::setUp();
    
    // Always seed roles before testing permissions
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
}
```

### 4. Test One Thing Per Test Method

```php
/** @test */
public function tenant_requires_account_with_owner()
{
    // This test only validates one specific requirement
    $account = Account::factory()->create(); // No owner
    
    $this->expectException(ValidationException::class);
    
    Tenant::create([
        'id' => 'test_tenant',
        'account_id' => $account->id,
        // ... other fields
    ]);
}
```

## Key Test Scenarios

### 1. Tenant Creation Validation

**File**: `tests/Feature/TenantCreationValidationTest.php`

Tests that validate:
- Tenant cannot be created without account_id
- Tenant cannot be created with invalid account_id
- Tenant cannot be created when account has no owner
- Tenant can be created when account has owner
- Multiple tenants can belong to same account

### 2. Roles and Permissions

**File**: `tests/Feature/RolesAndPermissionsTest.php`

Tests that validate:
- Roles are seeded for both web and accounts guards
- Permissions are seeded correctly
- Super admin has all permissions
- Users can be assigned roles
- Seeders assign roles correctly

### 3. User Account Tenant Relationships

**File**: `tests/Feature/UserAccountTenantRelationshipTest.php`

Tests that validate:
- User belongs to Account
- Account has one User
- Account has many Tenants
- Tenant belongs to Account
- Complete hierarchy: 1 User → 1 Account → Multiple Tenants

## Running Tests

```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/TenantCreationValidationTest.php

# Run specific test method
php artisan test --filter=tenant_creation_fails_without_account_id

# Run tests in parallel
php artisan test --parallel

# Run only feature tests
php artisan test --testsuite=Feature

# Run only unit tests
php artisan test --testsuite=Unit
```

## Writing New Tests

### Example: Testing a Feature with Authentication

```php
<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Tests\AuthenticatedTestCase;

class TenantManagementTest extends AuthenticatedTestCase
{
    /** @test */
    public function authenticated_user_can_create_tenant_for_their_account()
    {
        $tenantData = [
            'id' => 'test_tenant_001',
            'name' => 'My Tenant',
            'email' => 'tenant@test.com',
            'phone' => '+1234567890',
            'password' => 'password',
            'is_active' => true,
            'packages' => [],
            'account_id' => $this->account->id,
        ];
        
        $tenant = Tenant::create($tenantData);
        
        $this->assertDatabaseHas('tenants', [
            'id' => 'test_tenant_001',
            'account_id' => $this->account->id,
        ]);
        
        $this->assertCount(1, $this->account->fresh()->tenants);
    }
}
```

### Example: Testing with Data Providers

```php
<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HasDataProviders;
use Tests\TestCase;

class AccountCreationTest extends TestCase
{
    use RefreshDatabase;
    use HasDataProviders;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
    }

    /**
     * @test
     * @dataProvider validAccountDataProvider
     */
    public function can_create_account_with_valid_data(array $data)
    {
        $account = Account::create($data);
        
        $this->assertNotNull($account);
        $this->assertEquals($data['email'], $account->email);
    }
}
```

## Continuous Integration

The tests are designed to work in CI/CD pipelines. Ensure your CI configuration:

1. Sets up the database (SQLite in-memory recommended for tests)
2. Runs migrations
3. Seeds necessary data
4. Executes tests with coverage

Example GitHub Actions workflow:

```yaml
- name: Run Tests
  run: |
    php artisan test --coverage
```

## Troubleshooting

### Tests Failing Due to Missing Roles

**Solution**: Ensure ShieldSeeder is called in setUp():
```php
protected function setUp(): void
{
    parent::setUp();
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ShieldSeeder']);
}
```

### Tests Failing Due to Database State

**Solution**: Use RefreshDatabase trait to ensure clean database for each test:
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
    
    // ...
}
```

### Tests Failing Due to Missing Authentication

**Solution**: Extend AuthenticatedTestCase or AdminTestCase instead of TestCase:
```php
// Instead of
class MyTest extends TestCase { }

// Use
class MyTest extends AuthenticatedTestCase { }
```
