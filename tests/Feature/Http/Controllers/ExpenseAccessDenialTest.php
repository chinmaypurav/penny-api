<?php

use App\Models\Expense;
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
    $this->expense = Expense::factory()->create();
});

it('denies user to retrieve other user expense', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->getJson('api/expenses/'.$this->expense->id)
        ->assertForbidden();
});

it('denies user to update other user expense', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->patchJson('api/expenses/'.$this->expense->id)
        ->assertForbidden();
});

it('denies user to delete other user expense', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->deleteJson('api/expenses/'.$this->expense->id)
        ->assertForbidden();
});

it('denies guest to retrieve any expenses', function () {
    getJson('api/expenses')
        ->assertUnauthorized();
});

it('denies guest to retrieve an expense', function () {
    getJson('api/expenses/'.$this->expense->id)
        ->assertUnauthorized();
});

it('denies guest to update an expense', function () {
    patchJson('api/expenses/'.$this->expense->id)
        ->assertUnauthorized();
});

it('denies guest to delete an expense', function () {
    deleteJson('api/expenses/'.$this->expense->id)
        ->assertUnauthorized();
});
