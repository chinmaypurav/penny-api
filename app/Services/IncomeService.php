<?php

namespace App\Services;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IncomeService
{
    public function index(User $user): Collection
    {
        return QueryBuilder::for(Income::where('user_id', $user->id))
            ->allowedFilters([
                AllowedFilter::scope('transacted_after'),
                AllowedFilter::scope('transacted_before'),
            ])
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
