# Quick Start - Setting Up Users/Accounts

## Issue: "No users attached to the account"

This means your database hasn't been seeded yet. Follow these steps to set up the application with users (accounts):

## Solution

Run the setup command:

```bash
php artisan app:lets-go
```

**Or if you want to start completely fresh:**

```bash
php artisan app:lets-go --fresh
```

This command will:
1. Run all database migrations
2. Create roles and permissions (via ShieldSeeder)
3. Create a super admin account (via AccountSeeder)
4. Create 10 sample tenants (via TenantSeeder)

## After Setup

You can log in with:
- **Email**: `admin@admin.com`
- **Password**: `password`

## Manual Alternative

If you prefer to run steps manually:

```bash
# 1. Run migrations
php artisan migrate

# 2. Seed the database
php artisan db:seed
```

## What Gets Created

- **1 Super Admin Account** with full permissions
- **10 Tenants** (tenant_001 to tenant_010) associated with the super admin
- **Roles**: super_admin, admin, user, owner, manager
- **Permissions** for users, tenants, and accounts

See `SEEDING_GUIDE.md` for more detailed information.
