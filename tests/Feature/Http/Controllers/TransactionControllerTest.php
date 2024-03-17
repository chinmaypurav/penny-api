<?php

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(now());
    $this->user = User::factory()->create();
});

it('assert all transactions are sorted by transacted_at in asc order', function () {

    Income::factory()
        ->for($this->user)
        ->state(new Sequence(
            ['description' => 'income 1', 'transacted_at' => now()->subDays(5)], // 0
            ['description' => 'income 2', 'transacted_at' => now()->subDays(3)], // 2
        ))
        ->count(2)
        ->create();

    Expense::factory()
        ->for($this->user)
        ->state(new Sequence(
            ['description' => 'expense 1', 'transacted_at' => now()->subDays(1)], // 4
            ['description' => 'expense 2', 'transacted_at' => now()->subDays(4)], // 1
            ['description' => 'expense 3', 'transacted_at' => now()->subDays(2)], // 3
        ))
        ->count(3)
        ->create();

    actingAs($this->user)
        ->getJson('api/transactions')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('transactions.0', fn (AssertableJson $json) => $json->where('description', 'income 1')->etc())
            ->has('transactions.1', fn (AssertableJson $json) => $json->where('description', 'expense 2')->etc())
            ->has('transactions.2', fn (AssertableJson $json) => $json->where('description', 'income 2')->etc())
            ->has('transactions.3', fn (AssertableJson $json) => $json->where('description', 'expense 3')->etc())
            ->has('transactions.4', fn (AssertableJson $json) => $json->where('description', 'expense 1')->etc())
        )
        ->assertJsonCount(5, 'transactions');
});

it('checks the transactions endpoint response', function () {

    $income = Income::factory()
        ->for($this->user)
        ->create([
            'transacted_at' => now()->subDay(),
        ]);

    $expense = Expense::factory()
        ->for($this->user)
        ->create([
            'transacted_at' => now(),
        ]);

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
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('transactions.0', fn (AssertableJson $json) => $json
                ->where('transaction_type', 'Income')
                ->where('account_name', $income->account->name)
                ->where('category_name', $income->category->name)
                ->where('description', $income->description)
                ->where('amount', $income->amount)
                ->where('transacted_at', now()->subDay()->toIso8601ZuluString())
            )
            ->has('transactions.1', fn (AssertableJson $json) => $json
                ->where('transaction_type', 'Expense')
                ->where('account_name', $expense->account->name)
                ->where('category_name', $expense->category->name)
                ->where('description', $expense->description)
                ->where('amount', $expense->amount)
                ->where('transacted_at', now()->toIso8601ZuluString())
            )
        )
        ->assertJsonCount(2, 'transactions');
});
