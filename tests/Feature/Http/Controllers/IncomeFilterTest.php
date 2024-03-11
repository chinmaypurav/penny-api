<?php

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(today());

    $this->user = User::factory()
        ->has(Income::factory()
            ->state(new Sequence(
                ['description' => 'income -1', 'transacted_at' => now()->subDay()],
                ['description' => 'income 0', 'transacted_at' => now()],
                ['description' => 'income 1', 'transacted_at' => now()->addDay()],
            ))->count(3)
        )->create();
});

it('returns all incomes', function () {
    actingAs($this->user)
        ->getJson('api/incomes')
        ->assertJsonCount(3, 'incomes');
});

it('returns incomes after transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_after' => now()->addDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/incomes?'.$query)
        ->assertJsonCount(1, 'incomes')
        ->assertJsonFragment(['description' => 'income 1']);
});

it('returns incomes before transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_before' => now()->subDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/incomes?'.$query)
        ->assertJsonCount(1, 'incomes')
        ->assertJsonFragment(['description' => 'income -1']);
});
