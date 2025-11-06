<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a regular user with an account
        $account = Account::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'      => 'Regular User Account',
                'username'  => 'user',
                'phone'     => '+1234567891',
                'loginBy'   => 'email',
                'address'   => 'User Address',
                'lang'      => 'en',
                'password'  => Hash::make('password'),
                'type'      => 'user',
                'is_active' => true,
                'is_login'  => false,
            ]
        );

        // Assign user role to account
        if ( ! $account->hasRole('user')) {
            $account->assignRole('user');
        }

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'              => 'Regular User',
                'email'             => 'user@example.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'account_id'        => $account->id,
            ]
        );

        // Assign user role to user
        if ( ! $user->hasRole('user')) {
            $user->assignRole('user');
        }

        $this->command->info('Regular user created successfully!');
        $this->command->info('Email: user@example.com');
        $this->command->info('Password: password');
    }
}
