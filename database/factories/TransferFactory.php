<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomNumber(),
            'scheduled_at' => now(),
            'transacted_at' => now(),
            'completed' => fake()->boolean(),
            'description' => fake()->text(),
            'transaction_id' => fake()->word(),
            'created_at' => now(),
            'updated_at' => now(),

            'creditor_id' => Account::factory(),
            'debtor_id' => Account::factory(),
        ];
    }
}
