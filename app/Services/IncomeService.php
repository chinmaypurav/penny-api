<?php

namespace App\Services;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class IncomeService
{
    public function index(User $user): Collection
    {
        return $user->incomes()->get();
    }

    public function store(User $user, array $input): Income
    {
        return $user->incomes()->create($input);
    }

    public function update(Income $income, array $input): Income
    {
        return tap($income, fn (Income $income) => $income->update($input));
    }

    public function destroy(Income $income): Income
    {
        return tap($income, fn (Income $income) => $income->delete());
    }
}
