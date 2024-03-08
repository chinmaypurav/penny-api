<?php

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(now());
});

it('allows user to create an account', function () {

    $user = User::factory()->create();

    $payload = [
        'name' => fake()->word(),
        'account_type' => fake()->randomElement(AccountType::all()),
        'balance' => fake()->randomNumber(),
    ];

    actingAs($user)
        ->postJson('api/accounts', $payload)
        ->assertCreated()
        ->assertSimilarJson([
            'id' => 1,
            'name' => $payload['name'],
            'account_type' => $payload['account_type'],
            'balance' => $payload['balance'],
            'created_at' => now()->toIso8601ZuluString(),
        ]);

    $this->assertDatabaseHas(Account::class, [
        'id' => 1,
        'balance' => $payload['balance'],
        'account_type' => $payload['account_type'],
    ]);
});

it('allows user get its account by id', function () {
    $user = User::factory()
        ->has(Account::factory()->count(2))
        ->create();

    $account = $user->accounts()->first();

    actingAs($user)
        ->getJson('api/accounts/'.$account->id)
        ->assertOk()
        ->assertSimilarJson([
            'id' => 1,
            'name' => $account->name,
            'account_type' => $account->account_type,
            'balance' => $account->balance,
            'created_at' => now()->toIso8601ZuluString(),
        ]);
});

it('allows user to get all accounts', function () {

    $user = User::factory()
        ->has(Account::factory()->count(2))
        ->create();

    actingAs($user)
        ->getJson('api/accounts')
        ->assertOk()
        ->assertJsonStructure([
            'accounts' => [
                [
                    'id',
                    'name',
                    'balance',
                    'created_at',
                ],
            ],
        ])->assertJsonCount(2, 'accounts');
});

it('allows user delete account', function () {
    $user = User::factory()
        ->has(Account::factory())
        ->create();

    $account = $user->accounts()->first();

    $this->assertDatabaseCount(Account::class, 1);

    actingAs($user)
        ->deleteJson('api/accounts/'.$account->id)
        ->assertNoContent();

    $this->assertDatabaseCount(Account::class, 0);
});
