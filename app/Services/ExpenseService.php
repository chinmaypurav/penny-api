<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function index(User $user, array $params = []): Collection
    {
        return $user->expenses()
            ->when($params,
                fn (Builder $q) => $q->when($from = Arr::get($params, 'date_from'),
                    fn (Builder $q) => $q->whereDate('transacted_at', '>=', $from)
                )->when($to = Arr::get($params, 'date_to'),
                    fn (Builder $q) => $q->whereDate('transacted_at', '<=', $to)
                )
            )
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
