<?php

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->account1 = Account::factory()->for($this->user)->create();
    $this->account2 = Account::factory()->for($this->user)->create();
});

dataset('getInvalidData', function () {
    return [
        ['creditor_id', ['creditor_id' => null]],
        ['creditor_id', ['creditor_id' => 'a1']],
        ['creditor_id', ['creditor_id' => []]],
        ['creditor_id', ['creditor_id' => 3]],
        ['transacted_at', ['transacted_at' => null]],
        ['transacted_at', ['transacted_at' => []]],
        ['transacted_at', ['transacted_at' => 'string']],
        ['description', ['description' => null]],
        ['description', ['description' => []]],
        ['description', ['description' => Str::random(256)]],
    ];
});

it('returns all validation errors on transfer create with no data', function () {
    actingAs($this->user)
        ->postJson('api/transfers')
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'creditor_id', 'debtor_id', 'description', 'amount', 'transacted_at',
        ])
        ->assertJsonMissingValidationErrors(['transaction_id']);
});

it('returns validation errors on transfer create', function (string $attribute, array $data) {

    actingAs($this->user)
        ->postJson('api/transfers', $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor($attribute);
})->with('getInvalidData');

it('returns validation errors when creditor id and debtor id is same', function () {

    $account = Account::factory()->create();

    $payload = [
        'creditor_id' => $account->id,
        'debtor_id' => $account->id,
    ];

    actingAs($this->user)
        ->postJson('api/transfers', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'creditor_id', 'debtor_id',
        ]);
});
