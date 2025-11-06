# Database Seeding Guide

This guide explains how to use the seeders and factories for setting up your Laravel Filament application with multi-tenancy.

## Relationship Structure

The application follows a hierarchical relationship model with strict validation:

**1 User (Owner) → 1 Account → 3-5 Tenants**

- Each **User** owns exactly **1 Account** (User is the owner of the Account)
- Each **Account** can own **multiple Tenants** (3-5 are seeded)
- **Tenant creation requires** the Account to have an owner (User)
- The **super_admin** can see everything

### Important Validation Rules

1. **Account Ownership**: Every Account must have an owner (User). This is enforced at the database level via the `account_id` foreign key on the `users` table.

2. **Tenant Creation**: When creating a Tenant, the system validates:
   - The Tenant must be associated with an Account (`account_id` is required)
   - The Account must exist
   - The Account must have an owner (User)
   
   If any of these conditions are not met, a `ValidationException` is thrown.

## What Was Created

### Factories

1. **UserFactory** (`database/factories/UserFactory.php`)
   - Creates users with all required fields
   - Has a `withAccount()` method to automatically create an account for the user
   - Fields: name, email, password, username, packages, account_id

2. **AccountFactory** (`database/factories/AccountFactory.php`)
   - Creates accounts with all required fields
   - Has a `superAdmin()` state for creating super admin accounts
   - Fields: name, email, username, phone, loginBy, address, lang, password, type, is_active, is_login

3. **TenantFactory** (`database/factories/TenantFactory.php`)
   - Creates tenants with unique IDs and domains
   - Automatically associates with an account via account_id
   - Fields: id, name, email, phone, password, is_active, packages, account_id

### Seeders

1. **ShieldSeeder** (`database/seeders/ShieldSeeder.php`)
   - Creates roles: super_admin, admin, user, owner, manager
   - Creates basic permissions for users, tenants, and accounts
   - Assigns all permissions to super_admin role

2. **AccountSeeder** (`database/seeders/AccountSeeder.php`)
   - Creates 1 super admin account
   - Creates 1 user for that super admin account (1 user → 1 account)
   - Assigns super_admin role to both the account and the user

3. **UserSeeder** (`database/seeders/UserSeeder.php`)
   - Creates regular users with their accounts (1 user → 1 account)
   - Each user gets exactly 1 account
   - Assigns user role to both the account and the user

4. **TenantSeeder** (`database/seeders/TenantSeeder.php`)
   - **Validates** that each account has an owner before creating tenants
   - Creates 3-5 tenants for each account that has an owner
   - Skips accounts without owners and logs a warning
   - Associates each tenant with its owner account
   - Creates domains for each tenant (tenant1.localhost, etc.)
   - Creates a user inside each tenant's database

5. **DatabaseSeeder** (`database/seeders/DatabaseSeeder.php`)
   - Calls all seeders in the correct order:
     1. ShieldSeeder (roles & permissions)
     2. AccountSeeder (super admin account + user)
     3. UserSeeder (regular users + accounts)
     4. TenantSeeder (3-5 tenants per account)

### Artisan Command
**LetsGo Command** (`app/Console/Commands/LetsGo.php`)
- Command: `php artisan app:lets-go`
- Options:
  - `--fresh`: Drops all tables and re-runs migrations

## Usage

### Option 1: Using the Artisan Command (Recommended)

```bash
# Run migrations and seed the database
php artisan app:lets-go

# Or start fresh (drops all tables and re-runs migrations)
php artisan app:lets-go --fresh
```

### Option 2: Manual Database Seeding

```bash
# Run migrations first (if not already done)
php artisan migrate

# Seed the database
php artisan db:seed
```

### Option 3: Individual Seeders

```bash
# Run specific seeders
php artisan db:seed --class=ShieldSeeder
php artisan db:seed --class=AccountSeeder
php artisan db:seed --class=TenantSeeder
```

## Login Credentials

After seeding, you can log in to the AppsPanelProvider at `/user/login` with:

### Super Admin
- **Email**: `admin@admin.com`
- **Password**: `password`
- **Account**: Super Admin Account
- **Can see**: Everything (all tenants across all accounts)

### Regular User
- **Email**: `user@example.com`
- **Password**: `password`
- **Account**: Regular User Account
- **Can see**: Their own tenants (3-5 tenants)

## Created Tenants

The seeder creates 3-5 tenants for each account:

### For Super Admin Account
- **tenant_001** to **tenant_00X** (3-5 tenants)
- Domain: tenant1.localhost, tenant2.localhost, etc.

### For Regular User Account
- **tenant_00X** to **tenant_00Y** (3-5 tenants)
- Domain: tenantX.localhost, tenantY.localhost, etc.

Each tenant:
- Has a unique email: `tenant1@example.com`, `tenant2@example.com`, etc.
- Has password: `password`
- Is associated with its owner account (account_id)
- Has a user inside the tenant's database with the same credentials

## Roles Created

The following roles are created by ShieldSeeder:

1. **super_admin** - Has all permissions
2. **admin** - Administrative role
3. **user** - Basic user role
4. **owner** - Tenant owner role
5. **manager** - Manager role

## Permissions Created

Basic permissions for:
- Users (view_any_user, view_user, create_user, update_user, delete_user)
- Tenants (view_any_tenant, view_tenant, create_tenant, update_tenant, delete_tenant)
- Accounts (view_any_account, view_account, create_account, update_account, delete_account)

## Customization

You can customize the seeders by editing the files in `database/seeders/`:
- Change the super admin email/password in `AccountSeeder.php`
- Add more regular users in `UserSeeder.php`
- Modify the number of tenants (currently random 3-5) in `TenantSeeder.php`
- Add more roles/permissions in `ShieldSeeder.php`

## Troubleshooting

If you encounter any issues:

1. Make sure all migrations have been run (including the new `add_account_id_to_users_table` migration)
2. Ensure the database connection is properly configured in `.env`
3. Check that Redis is properly configured (if used)
4. Verify that the accounts, tenants, and users tables exist in your database
5. Ensure the foreign key relationship between users and accounts exists

## Notes

- **Relationship hierarchy**: 1 User (Owner) → 1 Account → 3-5 Tenants
- **Tenant validation**: All Tenant creations are validated via `TenantObserver` to ensure the Account has an owner
- **Role assignment**: Users and Accounts are automatically assigned roles during seeding
- All seeders use `firstOrCreate()` to avoid duplicate entries
- You can run the seeders multiple times without creating duplicates
- The super admin account and user are automatically assigned the super_admin role
- Regular users and their accounts are automatically assigned the user role
- Each account can own multiple tenants (3-5 are seeded randomly per account)
- Each user has exactly one account (via the `account_id` foreign key)
- The super_admin can see all tenants across all accounts
- Regular users can only see their own account's tenants
- Login at `/user/login` (AppsPanelProvider) to access the user panel
- Each tenant has its own database with users inside it

## Testing

The application includes comprehensive tests:

### Abstract Test Cases

1. **AuthenticatedTestCase** (`tests/AuthenticatedTestCase.php`)
   - Extend this class for tests requiring a logged-in user
   - Automatically creates an account with owner and authenticates
   - Provides `getUser()` and `getAccount()` helper methods

2. **AdminTestCase** (`tests/AdminTestCase.php`)
   - Extend this class for tests requiring admin privileges
   - Automatically creates an admin account with owner and authenticates
   - Provides `getAdminUser()` and `getAdminAccount()` helper methods

3. **HasDataProviders** (`tests/HasDataProviders.php`)
   - Trait providing centralized data providers for tests
   - Includes providers for valid account data, user data, tenant data, roles, and permissions
   - Use `@dataProvider` annotations to access these providers

### Example Test Usage

```php
use Tests\AuthenticatedTestCase;

class MyFeatureTest extends AuthenticatedTestCase
{
    /** @test */
    public function user_can_access_dashboard()
    {
        // $this->user is automatically authenticated
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run specific test class
php artisan test tests/Feature/TenantCreationValidationTest.php
```
