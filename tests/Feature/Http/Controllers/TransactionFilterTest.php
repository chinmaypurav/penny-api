<?php

use App\Models\Expense;
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
        )
        ->has(Expense::factory()
            ->state(new Sequence(
                ['description' => 'expense -1', 'transacted_at' => now()->subDay()],
                ['description' => 'expense 0', 'transacted_at' => now()],
                ['description' => 'expense 1', 'transacted_at' => now()->addDay()],
            ))->count(3)
        )
        ->create();
});

it('returns all transactions', function () {
    actingAs($this->user)
        ->getJson('api/transactions')
        ->assertOk()
        ->assertJsonCount(6, 'transactions');
});

it('returns transactions after transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_after' => now()->addDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/transactions?'.$query)
        ->assertJsonCount(2, 'transactions')
        ->assertJsonFragment(['description' => 'income 1'])
        ->assertJsonFragment(['description' => 'expense 1']);
});

it('returns transactions before transacted date', function () {
    $query = http_build_query([
        'filter' => [
            'transacted_before' => now()->subDay()->toDateString(),
        ],
    ]);

    actingAs($this->user)
        ->getJson('api/transactions?'.$query)
        ->assertJsonCount(2, 'transactions')
        ->assertJsonFragment(['description' => 'income -1'])
        ->assertJsonFragment(['description' => 'expense -1']);
});
