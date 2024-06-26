<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $expense = Expense::factory()->has(User::factory())->create();

    expect($expense->user)->toBeInstanceOf(User::class);
});

it('belongs to a account', function () {
    $expense = Expense::factory()->has(Account::factory())->create();

    expect($expense->account)->toBeInstanceOf(Account::class);
});

it('belongs to a category', function () {
    $expense = Expense::factory()->has(Category::factory())->create();

    expect($expense->category)->toBeInstanceOf(Category::class);
});

it('returns default category for null category', function () {
    $expense = Expense::factory()->noCategory()->create();

    expect($expense->category)->toBeInstanceOf(Category::class);
});
