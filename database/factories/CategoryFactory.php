<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->word(),
            'category_type' => fake()->randomElement(CategoryType::all()),
        ];
    }
}
