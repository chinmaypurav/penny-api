<?php

use App\Models\Income;
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
    $this->income = Income::factory()->create();
});

it('denies user to retrieve other user income', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->getJson('api/incomes/'.$this->income->id)
        ->assertForbidden();
});

it('denies user to update other user income', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->patchJson('api/incomes/'.$this->income->id)
        ->assertForbidden();
});

it('denies user to delete other user income', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->deleteJson('api/incomes/'.$this->income->id)
        ->assertForbidden();
});

it('denies guest to retrieve any incomes', function () {
    getJson('api/incomes')
        ->assertUnauthorized();
});

it('denies guest to retrieve an income', function () {
    getJson('api/incomes/'.$this->income->id)
        ->assertUnauthorized();
});

it('denies guest to update an income', function () {
    patchJson('api/incomes/'.$this->income->id)
        ->assertUnauthorized();
});

it('denies guest to delete an income', function () {
    deleteJson('api/incomes/'.$this->income->id)
        ->assertUnauthorized();
});
