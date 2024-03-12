<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    Account::factory()->for($this->user)->create();
    Category::factory()->for($this->user)->create();
    $this->expense = Expense::factory()->for($this->user)->create();
});

dataset('getInvalidData', function () {
    return [
        ['account_id', ['account_id' => null]],
        ['account_id', ['account_id' => 'a1']],
        ['account_id', ['account_id' => []]],
        ['account_id', ['account_id' => 3]],
        ['category_id', ['category_id' => 'a1']],
        ['category_id', ['category_id' => []]],
        ['category_id', ['category_id' => 3]],
        ['transacted_at', ['transacted_at' => null]],
        ['transacted_at', ['transacted_at' => []]],
        ['transacted_at', ['transacted_at' => 'string']],
        ['description', ['description' => null]],
        ['description', ['description' => []]],
        ['description', ['description' => Str::random(256)]],
    ];
});

it('returns all validation errors on expense store with no data', function () {
    actingAs($this->user)
        ->postJson('api/expenses')
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'account_id', 'transacted_at', 'description', 'amount',
        ])
        ->assertJsonMissingValidationErrors(['category_id']);
});

it('returns no validation errors on expense update with no data', function () {

    actingAs($this->user)
        ->patchJson('api/expenses/'.$this->expense->id)
        ->assertOk()
        ->assertJsonMissingValidationErrors();
});

it('returns validation errors on expense create', function (string $attribute, array $data) {

    actingAs($this->user)
        ->postJson('api/expenses', $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor($attribute);
})->with('getInvalidData');

it('returns no validation errors on expense create for nullable fields', function (string $attribute) {

    $payload = Expense::factory()->make()->toArray();
    $payload[$attribute] = null;

    actingAs($this->user)
        ->postJson('api/expenses', $payload)
        ->assertCreated()
        ->assertJsonMissingValidationErrors($attribute);
})->with([
    ['category_id'],
]);

it('returns validation errors on expense update', function (string $attribute, array $data) {

    actingAs($this->user)
        ->patchJson('api/expenses/'.$this->expense->id, $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($attribute)
        ->assertJsonMissingValidationErrors([]); // Test only for single key
})->with('getInvalidData');

it('returns no validation errors on expense update for nullable fields', function (string $attribute) {

    $payload = Expense::factory()->make()->toArray();
    $payload[$attribute] = null;

    actingAs($this->user)
        ->patchJson('api/expenses/'.$this->expense->id, $payload)
        ->assertOk()
        ->assertJsonMissingValidationErrors($attribute);
})->with([
    ['category_id'],
]);
