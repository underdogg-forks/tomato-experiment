<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Enums\LoginBy;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => fake()->name(),
            'email'     => fake()->unique()->safeEmail(),
            'phone'     => fake()->phoneNumber(),
            'username'  => fake()->unique()->userName(),
            'loginBy'   => LoginBy::EMAIL,
            'address'   => fake()->address(),
            'lang'      => 'en',
            'password'  => static::$password ??= Hash::make('password'),
            'type'      => AccountType::USER,
            'is_active' => true,
            'is_login'  => false,
        ];
    }

    /**
     * Indicate that the account is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'     => AccountType::SUPER_ADMIN,
            'email'    => 'admin@admin.com',
            'username' => 'admin',
            'name'     => 'Super Admin',
        ]);
    }
}
