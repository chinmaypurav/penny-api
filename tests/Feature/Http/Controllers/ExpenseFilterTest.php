<?php

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(today());

    $this->user = User::factory()
        ->has(Expense::factory()
            ->state(new Sequence(
                ['description' => 'expense -1', 'transacted_at' => now()->subDay()],
                ['description' => 'expense 0', 'transacted_at' => now()],
                ['description' => 'expense 1', 'transacted_at' => now()->addDay()],
            ))->count(3)
        )->create();
});

it('returns all expenses', function () {
    actingAs($this->user)
        ->getJson('api/expenses')
        ->assertJsonCount(3, 'expenses');
});

it('returns expenses after transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_after' => now()->addDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/expenses?'.$query)
        ->assertJsonCount(1, 'expenses')
        ->assertJsonFragment(['description' => 'expense 1']);
});

it('returns expenses before transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_before' => now()->subDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/expenses?'.$query)
        ->assertJsonCount(1, 'expenses')
        ->assertJsonFragment(['description' => 'expense -1']);
});
