<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        // \App\Models\User::factory(10)->create();

        $this->call([
            AccountSeeder::class,
            CategorySeeder::class,
            IncomeSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
