<?php

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(today());

    $this->user = User::factory()
        ->has(Transfer::factory()
            ->state(new Sequence(
                ['description' => 'transfer -1', 'transacted_at' => now()->subDay()],
                ['description' => 'transfer 0', 'transacted_at' => now()],
                ['description' => 'transfer 1', 'transacted_at' => now()->addDay()],
            ))->count(3)
        )->create();
});

it('returns all transfers', function () {
    actingAs($this->user)
        ->getJson('api/transfers')
        ->assertJsonCount(3, 'transfers');
});

it('returns transfers after transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_after' => now()->addDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/transfers?'.$query)
        ->assertJsonCount(1, 'transfers')
        ->assertJsonFragment(['description' => 'transfer 1']);
});

it('returns transfers before transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_before' => now()->subDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/transfers?'.$query)
        ->assertJsonCount(1, 'transfers')
        ->assertJsonFragment(['description' => 'transfer -1']);
});
