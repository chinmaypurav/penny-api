<?php

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $transfer = Transfer::factory()->has(User::factory())->create();

    expect($transfer->user)->toBeInstanceOf(User::class);
});

it('belongs to a creditor account', function () {
    $transfer = Transfer::factory()->has(Account::factory(), 'creditorAccount')->create();

    expect($transfer->creditorAccount)->toBeInstanceOf(Account::class);
});

it('belongs to a debtor account', function () {
    $transfer = Transfer::factory()->has(Account::factory(), 'debtorAccount')->create();

    expect($transfer->debtorAccount)->toBeInstanceOf(Account::class);
});
