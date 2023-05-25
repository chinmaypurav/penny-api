<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AccountService
{
    public function index(User $user): Collection
    {
        return $user->accounts()->get();
    }

    public function store(User $user, array $input): Account
    {
        return $user->accounts()->create($input);
    }

    public function update(Account $account, array $input): Account
    {
        return tap($account, fn (Account $account) => $account->update($input));
    }

    public function destroy(Account $account): Account
    {
        return tap($account, fn (Account $account) => $account->delete());
    }
}
