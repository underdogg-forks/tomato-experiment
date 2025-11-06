<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\LoginBy;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin account
        $superAdmin = Account::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'      => 'Super Admin',
                'username'  => 'admin',
                'phone'     => '+1234567890',
                'loginBy'   => LoginBy::EMAIL,
                'address'   => 'Admin Address',
                'lang'      => 'en',
                'password'  => Hash::make('password'),
                'type'      => AccountType::SUPER_ADMIN,
                'is_active' => true,
                'is_login'  => false,
            ]
        );

        // Assign super_admin role
        if ( ! $superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        // Create a user for the super admin account (1 user -> 1 account)
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'              => 'Super Admin',
                'email'             => 'admin@admin.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'account_id'        => $superAdmin->id,
            ]
        );

        $this->command->info('Super Admin account and user created successfully!');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: password');
    }
}
