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
    Carbon::setTestNow(today());

    $this->user = User::factory()->create();
});

it('adds account balance when created', function () {

    $account = Account::factory()->create([
        'balance' => 1000,
    ]);
    $category = Category::factory()->create();

    $payload = [
        'description' => fake()->sentence(),
        'account_id' => $account->id,
        'category_id' => $category->id,
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 3000,
    ];

    actingAs($this->user)
        ->postJson('api/incomes', $payload)
        ->assertCreated();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => 4000,
    ]);
});

it('subtracts account balance when removed', function () {

    $account = Account::factory()
        ->for($this->user)
        ->create(['balance' => 1000]);

    $income = Income::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 3000,
        ]);

    actingAs($this->user)
        ->deleteJson('api/incomes/'.$income->id)
        ->assertNoContent();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => -2000,
    ]);
});
