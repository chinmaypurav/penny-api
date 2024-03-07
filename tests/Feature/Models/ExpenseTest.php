<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $income = Expense::factory()->has(User::factory())->create();

    expect($income->user)->toBeInstanceOf(User::class);
});

it('belongs to a account', function () {
    $income = Expense::factory()->has(Account::factory())->create();

    expect($income->account)->toBeInstanceOf(Account::class);
});

it('belongs to a category', function () {
    $income = Expense::factory()->has(Category::factory())->create();

    expect($income->category)->toBeInstanceOf(Category::class);
});
