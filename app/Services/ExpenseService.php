<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ExpenseService
{
    public function index(int $userId): Collection
    {
        return QueryBuilder::for(Expense::where('user_id', $userId))
            ->allowedFilters([
                AllowedFilter::scope('transacted_after'),
                AllowedFilter::scope('transacted_before'),
            ])
            ->with(['account', 'category'])
            ->get();
    }

    public function store(User $user, array $input): Expense
    {
        return DB::transaction(
            fn () => $user->expenses()->create($input)
        );
    }

    public function update(Expense $expense, array $input): Expense
    {
        return DB::transaction(
            fn () => tap($expense, fn (Expense $expense) => $expense->update($input))
        );
    }

    public function destroy(Expense $expense): Expense
    {
        return DB::transaction(
            fn () => tap($expense, fn (Expense $expense) => $expense->delete())
        );
    }
}
