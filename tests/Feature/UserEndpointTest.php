<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('gets the authenticated user object', function () {
    $user = User::factory()->create();
    actingAs($user)
        ->getJson('api/user')
        ->assertOk()
        ->assertSimilarJson([
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->toIso8601ZuluString(),
        ]);
});

it('denies access to user endpoint', function () {
    getJson('api/user')
        ->assertUnauthorized();
});
