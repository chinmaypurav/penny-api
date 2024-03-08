<?php

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Carbon::setTestNow(now());
});

it('allows user to create an category', function () {

    $user = User::factory()->create();

    $payload = [
        'name' => fake()->word(),
        'category_type' => fake()->randomElement(CategoryType::all()),
    ];

    actingAs($user)
        ->postJson('api/categories', $payload)
        ->assertCreated()
        ->assertSimilarJson([
            'id' => 1,
            'name' => $payload['name'],
            'category_type' => $payload['category_type'],
        ]);

    $this->assertDatabaseHas(Category::class, [
        'id' => 1,
        'name' => $payload['name'],
        'category_type' => $payload['category_type'],
    ]);
});

it('allows user update an category', function () {
    $user = User::factory()
        ->has(
            Category::factory()
                ->state(fn (array $attributes, User $user) => ['name' => 'old category'])->count(1)
        )
        ->create();

    $category = $user->categories()->first();

    $updatedCategory = [
        'name' => 'new category',
    ];

    actingAs($user)
        ->patchJson('api/categories/'.$category->id, $updatedCategory)
        ->assertOk()
        ->assertSimilarJson([
            'id' => 1,
            'name' => $updatedCategory['name'],
            'category_type' => $category->category_type,
        ]);

    $this->assertDatabaseHas(Category::class, [
        'id' => $category->id,
        'name' => $updatedCategory['name'],
    ]);
});

it('allows user retrieve an category', function () {
    $user = User::factory()
        ->has(Category::factory()->count(2))
        ->create();

    $category = $user->categories()->first();

    actingAs($user)
        ->getJson('api/categories/'.$category->id)
        ->assertOk()
        ->assertSimilarJson([
            'id' => 1,
            'name' => $category->name,
            'category_type' => $category->category_type,
        ]);
});

it('allows user to retrieve all categories', function () {

    $user = User::factory()
        ->has(Category::factory()->count(2))
        ->create();

    actingAs($user)
        ->getJson('api/categories')
        ->assertOk()
        ->assertJsonStructure([
            'categories' => [
                [
                    'id',
                    'name',
                ],
            ],
        ])->assertJsonCount(2, 'categories');
});

it('allows user to delete an category', function () {
    $user = User::factory()
        ->has(Category::factory())
        ->create();

    $category = $user->categories()->first();

    $this->assertDatabaseCount(Category::class, 1);

    actingAs($user)
        ->deleteJson('api/categories/'.$category->id)
        ->assertNoContent();

    $this->assertDatabaseCount(Category::class, 0);
});
