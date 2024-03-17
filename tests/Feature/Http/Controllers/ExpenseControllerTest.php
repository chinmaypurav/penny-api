<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->service = app(ExpenseService::class);
    $this->user = User::factory()->create();
});

it('can create an expense', function () {
    $account = Account::factory()
        ->for($this->user)
        ->create([
            'balance' => 10000,
        ]);

    $category = Category::factory()
        ->for($this->user)
        ->create();

    $payload = [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'description' => fake()->word(),
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 5000,
    ];

    actingAs($this->user)
        ->postJson('api/expenses', $payload)
        ->assertCreated()->assertSimilarJson([
            'id' => 1,
            'description' => $payload['description'],
            'account_id' => $payload['account_id'],
            'category_id' => $payload['category_id'],
            'amount' => $payload['amount'],
            'transacted_at' => Carbon::parse($payload['transacted_at']),
        ]);

    $expected = $payload;
    $expected['user_id'] = $this->user->id;

    $this->assertDatabaseHas(Expense::class, $expected);
});

it('can retrieve an expense', function () {

    $expense = Expense::factory()
        ->for($this->user)
        ->create()
        ->refresh();

    actingAs($this->user)
        ->getJson('api/expenses/'.$expense->id)
        ->assertOk()
        ->assertSimilarJson([
            'id' => $expense->id,
            'description' => $expense->description,
            'account_id' => $expense->account_id,
            'category_id' => $expense->category_id,
            'amount' => $expense->amount,
            'transacted_at' => $expense->transacted_at,
        ]);
});

it('can retrieve all expenses', function () {

    Expense::factory()
        ->for($this->user)
        ->count(2)
        ->create();

    actingAs($this->user)
        ->getJson('api/expenses')
        ->assertOk()
        ->assertJsonStructure([
            'expenses' => [
                [
                    'id',
                    'description',
                    'account_id',
                    'category_id',
                    'amount',
                    'transacted_at',
                ],
            ],
        ])->assertJsonCount(2, 'expenses');
});

it('can update an expense', function () {

    $expense = Expense::factory()
        ->for($this->user)
        ->create();

    $account = Account::factory()
        ->for($this->user)
        ->create();

    $category = Category::factory()
        ->for($this->user)
        ->create();

    $payload = [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'description' => fake()->word(),
        'transacted_at' => now()->toDateTimeString(),
        'amount' => fake()->randomNumber(),
    ];

    actingAs($this->user)
        ->patchJson('api/expenses/'.$expense->id, $payload)
        ->assertOk()
        ->assertSimilarJson([
            'id' => $expense->id,
            'description' => $payload['description'],
            'account_id' => $payload['account_id'],
            'category_id' => $payload['category_id'],
            'amount' => $payload['amount'],
            'transacted_at' => Carbon::parse($payload['transacted_at']),
        ]);

    $expected = $payload;
    $expected['user_id'] = $this->user->id;

    $this->assertDatabaseHas(Expense::class, $expected);
});

it('can delete an expense', function () {
    $account = Account::factory()
        ->for($this->user)
        ->create([
            'user_id' => $this->user,
        ]);

    $category = Category::factory()
        ->for($this->user)
        ->create();

    $payload = [
        'description' => fake()->word(),
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 10000,
    ];

    $expense = Expense::factory()
        ->for($this->user)
        ->for($account)
        ->for($category)
        ->createQuietly();

    actingAs($this->user)
        ->deleteJson('api/expenses/'.$expense->id, $payload)
        ->assertNoContent();

    $this->assertDatabaseMissing(Expense::class, [
        'id' => $expense->id,
    ]);
});
