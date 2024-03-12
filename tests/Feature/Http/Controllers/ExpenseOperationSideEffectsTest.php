<?php

use App\Models\Account;
use App\Models\Expense;
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

    $payload = [
        'description' => fake()->sentence(),
        'account_id' => $account->id,
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 3000,
    ];

    actingAs($this->user)
        ->postJson('api/expenses', $payload)
        ->assertCreated();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => -2000,
    ]);
});

it('subtracts account balance when removed', function () {

    $account = Account::factory()
        ->for($this->user)
        ->create(['balance' => 1000]);

    $expense = Expense::factory()
        ->for($this->user)
        ->for($account)
        ->createQuietly([
            'amount' => 3000,
        ]);

    actingAs($this->user)
        ->deleteJson('api/expenses/'.$expense->id)
        ->assertNoContent();

    $this->assertDatabaseHas(Account::class, [
        'id' => $account->id,
        'balance' => 4000,
    ]);
});
