<?php

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->account1 = Account::factory()->for($this->user)->create();
    $this->account2 = Account::factory()->for($this->user)->create();
    $this->transfer = Transfer::factory()
        ->for($this->user)
        ->for($this->account1, 'debtorAccount')
        ->for($this->account2, 'creditorAccount')
        ->for($this->user)
        ->create();
});

dataset('getInvalidData', function () {
    return [
        ['creditor_id', ['creditor_id' => null]],
        ['creditor_id', ['creditor_id' => 'a1']],
        ['creditor_id', ['creditor_id' => []]],
        ['creditor_id', ['creditor_id' => 3]],
        ['debtor_id', ['debtor_id' => null]],
        ['debtor_id', ['debtor_id' => 'a1']],
        ['debtor_id', ['debtor_id' => []]],
        ['debtor_id', ['debtor_id' => 3]],
        ['transacted_at', ['transacted_at' => null]],
        ['transacted_at', ['transacted_at' => []]],
        ['transacted_at', ['transacted_at' => 'string']],
        ['description', ['description' => null]],
        ['description', ['description' => []]],
        ['description', ['description' => Str::random(256)]],
        ['amount', ['amount' => null]],
        ['amount', ['amount' => 'a1']],
        ['amount', ['amount' => []]],
    ];
});

it('returns no validation errors on transfer update with no data', function () {

    actingAs($this->user)
        ->patchJson('api/transfers/'.$this->transfer->id)
        ->assertOk()
        ->assertJsonMissingValidationErrors();
});

it('returns validation errors on transfer update', function (string $attribute, array $data) {

    actingAs($this->user)
        ->patchJson('api/transfers/'.$this->transfer->id, $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($attribute)
        ->assertJsonMissingValidationErrors([]); // Test only for single key
})->with('getInvalidData');

it('returns validation errors when creditor id and debtor id is same', function () {

    $account = Account::factory()->create();

    $payload = [
        'creditor_id' => $account->id,
        'debtor_id' => $account->id,
    ];

    actingAs($this->user)
        ->patchJson('api/transfers/'.$this->transfer->id, $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'creditor_id', 'debtor_id',
        ]);
});
