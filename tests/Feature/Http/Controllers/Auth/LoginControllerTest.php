<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in a user', function () {
    $user = User::factory()->create();

    $this->assertGuest();

    $this->postJson('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertOk();

    $this->assertAuthenticated();
});

it('logs out a user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->assertAuthenticated();

    $this->postJson('/logout')
        ->assertNoContent();

    $this->assertGuest();
});

it('prevents a guest to log out', function () {
    $user = User::factory()->create();

    $this->assertGuest();

    $this->postJson('/logout')
        ->assertNoContent();
});

it('redirects a user on login again', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->assertAuthenticated();

    $this->postJson('/login')
        ->assertRedirect(RouteServiceProvider::HOME);
});

it('prevents login with incorrect email', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    $this->assertGuest();

    $this->postJson('/login', [
        'email' => 'jane@example.com',
        'password' => 'password',
    ])
        ->assertUnprocessable();
});

it('prevents login with incorrect password', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    $this->assertGuest();

    $this->postJson('/login', [
        'email' => 'john@example.com',
        'password' => 'drowssap',
    ])
        ->assertUnprocessable();
});
