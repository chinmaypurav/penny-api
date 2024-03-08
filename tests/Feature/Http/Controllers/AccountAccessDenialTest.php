<?php

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(now());
    $this->account = Account::factory()->create();
});

it('denies user retrieve other user account', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->getJson('api/accounts/'.$this->account->id)
        ->assertForbidden();
});

it('denies user access to update other user account', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->patchJson('api/accounts/'.$this->account->id, [
            'name' => fake()->name(),
        ])
        ->assertForbidden();
});

it('denies user to delete other user account', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->deleteJson('api/accounts/'.$this->account->id)
        ->assertForbidden();
});

it('denies guest to retrieve any accounts', function () {
    getJson('api/accounts')
        ->assertUnauthorized();
});

it('denies guest to retrieve a account', function () {
    getJson('api/accounts/'.$this->account->id)
        ->assertUnauthorized();
});

it('denies guest to update a account', function () {
    patchJson('api/accounts/'.$this->account->id)
        ->assertUnauthorized();
});

it('denies guest to delete a account', function () {
    deleteJson('api/accounts/'.$this->account->id)
        ->assertUnauthorized();
});
