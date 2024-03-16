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

dataset('update account', function () {
    return [
        [1, 2, 3000, 1000, 2000, 3000], // same ids
        [1, 3, 3000, 1000, -1000, 6000], // changed creditor_id
        [3, 2, 3000, 4000, 2000, 0], // changed debtor_id
        [2, 1, 3000, 7000, -4000, 3000], // inter change with amount same
        [2, 1, 5000, 9000, -6000, 3000], // inter change and amount change
    ];
});

it('changes account balances on transfer account update',
    function (?int $debtorId, ?int $creditorId, int $tAmount, int $a1Amount, int $a2Amount, int $a3Amount) {

        $a1 = Account::factory()->for($this->user)->create(['id' => 1, 'balance' => 1000]);
        $a2 = Account::factory()->for($this->user)->create(['id' => 2, 'balance' => 2000]);
        $a3 = Account::factory()->for($this->user)->create(['id' => 3, 'balance' => 3000]);

        $transfer = Transfer::factory()
            ->for($a1, 'debtorAccount')
            ->for($a2, 'creditorAccount')
            ->createQuietly([
                'amount' => 3000,
            ]);

        $payload = array_filter([
            'creditor_id' => $creditorId,
            'debtor_id' => $debtorId,
            'amount' => $tAmount,
        ]);

        actingAs($this->user)
            ->patchJson('api/transfers/'.$transfer->id, $payload)
            ->assertOk();

        $this->assertDatabaseHas(Transfer::class, [
            'id' => $transfer->id,
            'amount' => $tAmount,
        ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $a1->id,
            'balance' => $a1Amount,
        ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $a2->id,
            'balance' => $a2Amount,
        ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $a3->id,
            'balance' => $a3Amount,
        ]);
    })->with('update account');
