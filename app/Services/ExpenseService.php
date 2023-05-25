<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ExpenseService
{
    public function index(User $user): Collection
    {
        return $user->expenses()->get();
    }

    public function store(User $user, array $input): Expense
    {
        return $user->expenses()->create($input);
    }

    public function update(Expense $expense, array $input): Expense
    {
        return tap($expense, fn (Expense $expense) => $expense->update($input));
    }

    public function destroy(Expense $expense): Expense
    {
        return tap($expense, fn (Expense $expense) => $expense->delete());
    }
}
