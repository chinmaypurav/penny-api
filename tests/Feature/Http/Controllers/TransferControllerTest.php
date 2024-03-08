<?php

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(now());
});

it('allows user to create a transfer', function () {
    $user = User::factory()
        ->has(
            Account::factory()->state(new Sequence(
                ['name' => 'account 1', 'balance' => 10000],
                ['name' => 'account 2', 'balance' => 20000],
            ))->count(2)
        )
        ->create();

    $creditor = Account::where('name', 'account 1')->first();
    $debtor = Account::where('name', 'account 2')->first();

    $payload = [
        'creditor_id' => $creditor->id,
        'debtor_id' => $debtor->id,
        'amount' => 2000,
        'transacted_at' => Carbon::yesterday()->toIso8601ZuluString(),
        'description' => fake()->sentence(),
    ];

    actingAs($user)
        ->postJson('api/transfers', $payload)
        ->assertCreated()
        ->assertJsonFragment([
            'id' => 1,
            'description' => $payload['description'],
            'amount' => $payload['amount'],
            'transacted_at' => $payload['transacted_at'],
        ]);

    $this->assertDatabaseHas(Transfer::class, [
        'id' => 1,
        'description' => $payload['description'],
        'creditor_id' => $payload['creditor_id'],
        'debtor_id' => $payload['debtor_id'],
        'amount' => $payload['amount'],
        'transacted_at' => $payload['transacted_at'],
    ]);
});

it('allows user update a transfer', function () {
    // TODO
});

it('allows user retrieve a transfer', function () {
    $user = User::factory()
        ->has(Transfer::factory()->count(1))
        ->create();

    $transfer = $user->transfers()->first();

    actingAs($user)
        ->getJson('api/transfers/'.$transfer->id)
        ->assertOk()
        ->assertJsonFragment([
            'id' => 1,
            'description' => $transfer->description,
            'amount' => $transfer->amount,
            'transacted_at' => $transfer->transacted_at->toIso8601ZuluString(),
        ]);
});

it('allows user to retrieve all transfers', function () {

    $user = User::factory()
        ->has(Transfer::factory()->count(2))
        ->create();

    actingAs($user)
        ->getJson('api/transfers')
        ->assertOk()
        ->assertJsonStructure([
            'transfers' => [
                [
                    'id',
                    'description',
                    'creditor',
                    'debtor',
                    'amount',
                    'transacted_at',
                ],
            ],
        ])->assertJsonCount(2, 'transfers');
});

it('allows user to delete a transfer', function () {
    $user = User::factory()
        ->has(Transfer::factory())
        ->create();

    $transfer = $user->transfers()->first();

    $this->assertDatabaseCount(Transfer::class, 1);

    actingAs($user)
        ->deleteJson('api/transfers/'.$transfer->id)
        ->assertNoContent();

    $this->assertDatabaseCount(Transfer::class, 0);
});
