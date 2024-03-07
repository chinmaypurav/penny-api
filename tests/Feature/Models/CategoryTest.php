<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $category = Category::factory()->has(User::factory())->create();

    expect($category->user)->toBeInstanceOf(User::class);
});
