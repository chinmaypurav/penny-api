<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'amount' => $this->faker->randomFloat(),
            'scheduled_at' => Carbon::now(),
            'transacted_at' => Carbon::now(),
            'completed' => $this->faker->boolean(),
            'description' => $this->faker->text(),
            'transaction_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'creditor_id' => Account::factory(),
            'debtor_id' => Account::factory(),
        ];
    }
}
