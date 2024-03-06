<?php

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
use App\Models\User;
use App\Services\IncomeService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->service = app(IncomeService::class);
    $this->user = User::factory()->create();
});

dataset('getIncomeDataset', function () {
    return [
        [AccountType::SAVINGS],
        [AccountType::CREDIT],
        [AccountType::TRADING],
        [AccountType::CURRENT],
    ];
});

it('stores income to incomes table and adjusts account', function (AccountType $accountType) {
    $account = Account::factory()->create([
        'user_id' => $this->user,
        'account_type' => $accountType,
    ]);

    $category = Category::factory()->create([
        'user_id' => $this->user,
    ]);

    $payload = [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'description' => fake()->word(),
        'transacted_at' => now()->toDateTimeString(),
        'amount' => fake()->randomFloat(2),
    ];

    actingAs($this->user)
        ->postJson('api/incomes', $payload)
        ->assertCreated();

    $expected = $payload;
    $expected['user_id'] = $this->user->id;

    $this->assertDatabaseHas(Income::class, $expected);
    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => $account->balance + $payload['amount'],
    ]);
})->with('getIncomeDataset');

it('deletes income from incomes table and adjusts account', function (AccountType $accountType) {
    $account = Account::factory()->create([
        'user_id' => $this->user,
        'account_type' => $accountType,
    ]);

    $category = Category::factory()->create([
        'user_id' => $this->user,
    ]);

    $payload = [
        'description' => fake()->word(),
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 10000,
    ];

    $income = Income::factory()->createQuietly([
        'user_id' => $this->user,
        'account_id' => $account,
        'category_id' => $category,
    ]);

    actingAs($this->user)
        ->deleteJson('api/incomes/'.$income->id, $payload)
        ->assertNoContent();

    $this->assertDatabaseMissing(Income::class, [
        'id' => $income->id,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => $account->balance - $income->amount,
    ]);
})->with('getIncomeDataset');
