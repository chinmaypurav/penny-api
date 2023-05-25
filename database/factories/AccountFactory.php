<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->word(),
            'account_type' => fake()->randomElement(['SAVINGS', 'CURRENT', 'CREDIT', 'TRADING']),
            'balance' => fake()->numberBetween(0, 10000),
        ];
    }
}
