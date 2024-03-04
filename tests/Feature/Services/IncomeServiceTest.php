<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Services\IncomeService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IncomeServiceTest extends TestCase
{
    use DatabaseMigrations;

    private IncomeService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(IncomeService::class);
        $this->user = User::factory()->create();
    }
}
