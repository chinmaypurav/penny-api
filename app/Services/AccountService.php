<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function index(User $user): Collection
    {
        return $user->accounts()->get();
    }

    public function store(User $user, array $input): Account
    {
        return DB::transaction(
            fn () => $user->accounts()->create($input)
        );
    }

    public function update(Account $account, array $input): Account
    {
        return DB::transaction(
            fn () => tap($account, fn (Account $account) => $account->update($input))
        );
    }

    public function destroy(Account $account): Account
    {
        return DB::transaction(
            fn () => tap($account, fn (Account $account) => $account->delete())
        );
    }

    public function increment(Account $account, float $amount): Account
    {
        return tap($account, fn (Account $account) => $account->increment('balance', $amount));
    }

    public function decrement(Account $account, float $amount): Account
    {
        return tap($account, fn (Account $account) => $account->decrement('balance', $amount));
    }
}
