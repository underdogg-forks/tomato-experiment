<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

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
        // Create an account with an owner user
        $account = \App\Models\Account::factory()->create();
        \App\Models\User::factory()->create(['account_id' => $account->id]);

        return [
            'id'         => 'tenant_' . Str::random(8),
            'name'       => fake()->company(),
            'email'      => fake()->unique()->companyEmail(),
            'phone'      => fake()->phoneNumber(),
            'password'   => static::$password ??= Hash::make('password'),
            'is_active'  => true,
            'packages'   => [],
            'account_id' => $account->id,
        ];
    }
}
