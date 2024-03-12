<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can create an income', function () {
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
        'amount' => (string) fake()->randomNumber(),
    ];

    actingAs($this->user)
        ->postJson('api/incomes', $payload)
        ->assertCreated()
        ->assertSimilarJson([
            'id' => 1,
            'description' => $payload['description'],
            'account_id' => $payload['account_id'],
            'category_id' => $payload['category_id'],
            'amount' => $payload['amount'],
            'transacted_at' => Carbon::parse($payload['transacted_at']),
        ]);

    $expected = $payload;
    $expected['user_id'] = $this->user->id;

    $this->assertDatabaseHas(Income::class, $expected);
});

it('can retrieve an income', function () {

    $income = Income::factory()
        ->for($this->user)
        ->create()
        ->refresh();

    actingAs($this->user)
        ->getJson('api/incomes/'.$income->id)
        ->assertOk()
        ->assertSimilarJson([
            'id' => $income->id,
            'description' => $income->description,
            'account_id' => $income->account_id,
            'category_id' => $income->category_id,
            'amount' => $income->amount,
            'transacted_at' => $income->transacted_at,
        ]);
});

it('can retrieve all incomes', function () {

    $income = Income::factory()
        ->for($this->user)
        ->count(2)
        ->create();

    actingAs($this->user)
        ->getJson('api/incomes')
        ->assertOk()
        ->assertJsonStructure([
            'incomes' => [
                [
                    'id',
                    'description',
                    'account_id',
                    'category_id',
                    'amount',
                    'transacted_at',
                ],
            ],
        ])->assertJsonCount(2, 'incomes');
});

it('can update an income', function () {

    Income::factory()
        ->for($this->user)
        ->create();

    $income = $this->user->incomes()->first();

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
        ->patchJson('api/incomes/'.$income->id, $payload)
        ->assertOk()
        ->assertSimilarJson([
            'id' => $income->id,
            'description' => $payload['description'],
            'account_id' => $payload['account_id'],
            'category_id' => $payload['category_id'],
            'amount' => $payload['amount'],
            'transacted_at' => Carbon::parse($payload['transacted_at']),
        ]);

    $expected = $payload;
    $expected['user_id'] = $this->user->id;

    $this->assertDatabaseHas(Income::class, $expected);
});

it('can delete an income', function () {
    $account = Account::factory()
        ->for($this->user)
        ->create();

    $category = Category::factory()
        ->for($this->user)
        ->create();

    $payload = [
        'description' => fake()->word(),
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 10000,
    ];

    $income = Income::factory()
        ->for($this->user)
        ->for($account)
        ->for($category)
        ->createQuietly();

    actingAs($this->user)
        ->deleteJson('api/incomes/'.$income->id, $payload)
        ->assertNoContent();

    $this->assertDatabaseMissing(Income::class, [
        'id' => $income->id,
    ]);
});
