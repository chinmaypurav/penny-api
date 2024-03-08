<?php

use App\Models\Category;
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
    $this->category = Category::factory()->create();
});

it('denies user retrieve other user category', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->getJson('api/categories/'.$this->category->id)
        ->assertForbidden();
});

it('denies user access to update other user category', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->patchJson('api/categories/'.$this->category->id, [
            'name' => fake()->name(),
        ])
        ->assertForbidden();
});

it('denies user to delete other user category', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->deleteJson('api/categories/'.$this->category->id)
        ->assertForbidden();
});

it('denies guest to retrieve any categories', function () {
    getJson('api/categories')
        ->assertUnauthorized();
});

it('denies guest to retrieve a category', function () {
    getJson('api/categories/'.$this->category->id)
        ->assertUnauthorized();
});

it('denies guest to update a category', function () {
    patchJson('api/categories/'.$this->category->id)
        ->assertUnauthorized();
});

it('denies guest to delete a category', function () {
    deleteJson('api/categories/'.$this->category->id)
        ->assertUnauthorized();
});
