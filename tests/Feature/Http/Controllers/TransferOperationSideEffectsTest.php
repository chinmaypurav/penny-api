<?php

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(today());

    $this->user = User::factory()->create();
});

it('changes account balances on transfer create', function () {

    $ca = Account::factory()->for($this->user)->create(['balance' => 1000]);
    $da = Account::factory()->for($this->user)->create(['balance' => 2000]);

    $payload = [
        'description' => fake()->sentence(),
        'creditor_id' => $ca->id,
        'debtor_id' => $da->id,
        'transacted_at' => now()->toDateTimeString(),
        'amount' => 3000,
    ];

    actingAs($this->user)
        ->postJson('api/transfers', $payload)
        ->assertCreated();

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'balance' => 4000,
    ]);
    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'balance' => -1000,
    ]);
});

dataset('update transfer', function () {
    return [
        [1000, 1000, 4000, 4000, null, 3000], // No amount
        [1000, 1000, 4000, 4000, 3000, 3000], // same amount
        [1000, 2000, 4000, 3000, 4000, 4000], // Change in amount
    ];
});

it('changes account balances on transfer update', function (int $caBefore, int $caAfter, int $daBefore, int $daAfter, ?int $aBefore, int $aAfter) {

    $ca = Account::factory()->for($this->user)->create(['balance' => $caBefore]);
    $da = Account::factory()->for($this->user)->create(['balance' => $daBefore]);

    $transfer = Transfer::factory()
        ->for($da, 'debtorAccount')
        ->for($ca, 'creditorAccount')
        ->createQuietly([
            'amount' => 3000,
        ]);

    $payload = array_filter([
        'creditor_id' => $ca->id,
        'debtor_id' => $da->id,
        'amount' => $aBefore,
    ]);

    actingAs($this->user)
        ->patchJson('api/transfers/'.$transfer->id, $payload)
        ->assertOk();

    $this->assertDatabaseHas(Account::class, [
        'id' => $ca->id,
        'balance' => $caAfter,
    ]);

    $this->assertDatabaseHas(Account::class, [
        'id' => $da->id,
        'balance' => $daAfter,
    ]);

    $this->assertDatabaseHas(Transfer::class, [
        'id' => $transfer->id,
        'amount' => $aAfter,
    ]);
})->with('update transfer');
