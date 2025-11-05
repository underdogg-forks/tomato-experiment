# Database Seeding Guide

This guide explains how to use the newly created seeders, factories, and artisan command for setting up your Laravel Filament application with multi-tenancy.

## What Was Created

### Factories
1. **AccountFactory** (`database/factories/AccountFactory.php`)
   - Creates accounts with all required fields
   - Has a `superAdmin()` state for creating super admin accounts

2. **TenantFactory** (`database/factories/TenantFactory.php`)
   - Creates tenants with unique IDs and domains
   - Supports all tenant fields including packages

3. **UserFactory** (already existed)
   - Creates basic user records

### Seeders
1. **ShieldSeeder** (`database/seeders/ShieldSeeder.php`)
   - Creates roles: super_admin, admin, user, owner, manager
   - Creates basic permissions for users, tenants, and accounts
   - Assigns all permissions to super_admin role

2. **AccountSeeder** (`database/seeders/AccountSeeder.php`)
   - Creates 1 super admin account
   - Assigns super_admin role to the account

3. **TenantSeeder** (`database/seeders/TenantSeeder.php`)
   - Creates 10 tenants (tenant_001 to tenant_010)
   - Associates each tenant with the super admin account
   - Creates domains for each tenant (tenant1.localhost, etc.)

4. **DatabaseSeeder** (`database/seeders/DatabaseSeeder.php`)
   - Updated to call all seeders in the correct order

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

After seeding, you can log in with:

- **Email**: `admin@admin.com`
- **Password**: `password`

## Created Tenants

The seeder creates 10 tenants:

1. **tenant_001** - domain: tenant1.localhost
2. **tenant_002** - domain: tenant2.localhost
3. **tenant_003** - domain: tenant3.localhost
4. **tenant_004** - domain: tenant4.localhost
5. **tenant_005** - domain: tenant5.localhost
6. **tenant_006** - domain: tenant6.localhost
7. **tenant_007** - domain: tenant7.localhost
8. **tenant_008** - domain: tenant8.localhost
9. **tenant_009** - domain: tenant9.localhost
10. **tenant_010** - domain: tenant10.localhost

Each tenant:
- Has email: `tenant1@example.com` to `tenant10@example.com`
- Has password: `password`
- Is associated with the super admin account (as owner)

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
- Modify the number of tenants in `TenantSeeder.php`
- Add more roles/permissions in `ShieldSeeder.php`

## Troubleshooting

If you encounter any issues:

1. Make sure all migrations have been run
2. Ensure the database connection is properly configured in `.env`
3. Check that Redis is properly configured (if used)
4. Verify that the accounts and tenants tables exist in your database

## Notes

- All seeders use `firstOrCreate()` to avoid duplicate entries
- You can run the seeders multiple times without creating duplicates
- The super admin account is automatically assigned the super_admin role
- All tenants are associated with the super admin account (account_id)
