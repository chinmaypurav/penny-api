<?php

use App\Models\Transfer;
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
    $this->transfer = Transfer::factory()->create();
});

it('denies user retrieve other user transfer', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->getJson('api/transfers/'.$this->transfer->id)
        ->assertForbidden();
});

it('denies user access to update other user transfer', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->patchJson('api/transfers/'.$this->transfer->id, [
            'name' => fake()->name(),
        ])
        ->assertForbidden();
});

it('denies user to delete other user transfer', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->deleteJson('api/transfers/'.$this->transfer->id)
        ->assertForbidden();
});

it('denies guest to retrieve any transfers', function () {
    getJson('api/transfers')
        ->assertUnauthorized();
});

it('denies guest to retrieve a transfer', function () {
    getJson('api/transfers/'.$this->transfer->id)
        ->assertUnauthorized();
});

it('denies guest to update a transfer', function () {
    patchJson('api/transfers/'.$this->transfer->id)
        ->assertUnauthorized();
});

it('denies guest to delete a transfer', function () {
    deleteJson('api/transfers/'.$this->transfer->id)
        ->assertUnauthorized();
});
