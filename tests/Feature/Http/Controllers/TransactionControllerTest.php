<?php

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('return all transactions', function () {

    Income::factory()
        ->for($this->user)
        ->count(2)
        ->create();

    Expense::factory()
        ->for($this->user)
        ->count(3)
        ->create();

    actingAs($this->user)
        ->getJson('api/transactions')
        ->assertOk()
        ->assertJsonStructure([
            'transactions' => [
                '*' => [
                    'transaction_type',
                    'account_name',
                    'category_name',
                    'description',
                    'amount',
                    'transacted_at',
                ],
            ],
        ])
        ->assertJsonCount(5, 'transactions');
});
