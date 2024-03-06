<?php

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->service = app(ExpenseService::class);
    $this->user = User::factory()->create();
});

dataset('getExpenseDataset', function () {
    return [
        [AccountType::SAVINGS],
        [AccountType::CREDIT],
        [AccountType::TRADING],
        [AccountType::CURRENT],
    ];
});

it('stores expense to expenses table and adjusts account', function (AccountType $accountType) {
    $account = Account::factory()->create([
        'user_id' => $this->user,
        'account_type' => $accountType,
        'balance' => 10000,
    ]);

    $category = Category::factory()->create([
        'user_id' => $this->user,
    ]);

    $payload = [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'description' => fake()->word(),
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 5000,
    ];

    actingAs($this->user)
        ->postJson('api/expenses', $payload)
        ->assertCreated();

    $expected = $payload;
    $expected['user_id'] = $this->user->id;

    $this->assertDatabaseHas(Expense::class, $expected);
    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => $account->balance - $payload['amount'],
    ]);
})->with('getExpenseDataset');

it('deletes expense from expenses table and adjusts account', function (AccountType $accountType) {
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

    $expense = Expense::factory()->createQuietly([
        'user_id' => $this->user,
        'account_id' => $account,
        'category_id' => $category,
    ]);

    actingAs($this->user)
        ->deleteJson('api/expenses/'.$expense->id, $payload)
        ->assertNoContent();

    $this->assertDatabaseMissing(Expense::class, [
        'id' => $expense->id,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => $account->balance + $expense->amount,
    ]);
})->with('getExpenseDataset');
