<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    Account::factory()->for($this->user)->create();
    Category::factory()->for($this->user)->create();
    $this->income = Income::factory()->for($this->user)->create();
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

it('returns validation errors on income create', function (string $attribute, array $data) {

    actingAs($this->user)
        ->postJson('api/incomes', $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor($attribute);
})->with('getInvalidData');

it('returns no validation errors on income create for nullable fields', function (string $attribute) {

    $payload = Income::factory()->make()->toArray();
    $payload[$attribute] = null;

    actingAs($this->user)
        ->postJson('api/incomes', $payload)
        ->assertCreated()
        ->assertJsonMissingValidationErrors($attribute);
})->with([
    ['category_id'],
]);

it('returns validation errors on income update', function (string $attribute, array $data) {

    actingAs($this->user)
        ->patchJson('api/incomes/'.$this->income->id, $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($attribute)
        ->assertJsonMissingValidationErrors([]); // Test only for single key
})->with('getInvalidData');

it('returns no validation errors on income update for nullable fields', function (string $attribute) {

    $payload = Income::factory()->make()->toArray();
    $payload[$attribute] = null;

    actingAs($this->user)
        ->patchJson('api/incomes/'.$this->income->id, $payload)
        ->assertOk()
        ->assertJsonMissingValidationErrors($attribute);
})->with([
    ['category_id'],
]);
