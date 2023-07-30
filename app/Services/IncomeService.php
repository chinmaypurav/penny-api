<?php

namespace App\Services;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class IncomeService
{
    public function index(User $user, array $params): Collection
    {
        return $user->incomes()
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

    public function store(User $user, array $input): Income
    {
        return DB::transaction(
            fn () => $user->incomes()->create($input)
        );
    }

    public function update(Income $income, array $input): Income
    {
        return DB::transaction(
            fn () => tap($income, fn (Income $income) => $income->update($input))
        );
    }

    public function destroy(Income $income): Income
    {
        return DB::transaction(
            fn () => tap($income, fn (Income $income) => $income->delete())
        );
    }
}
