<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function index(User $user, array $params): Collection
    {
        return $user->transfers()
            ->when($params,
                fn (Builder $q) => $q->when($from = Arr::get($params, 'date_from'),
                    fn (Builder $q) => $q->whereDate('transacted_at', '>=', $from)
                )->when($to = Arr::get($params, 'date_to'),
                    fn (Builder $q) => $q->whereDate('transacted_at', '<=', $to)
                )
            )
            ->with(['debtorAccount', 'creditorAccount'])
            ->get();
    }

    public function store(User $user, array $input): Transfer
    {
        return DB::transaction(
            fn () => $user->transfers()->create($input)
        );
    }

    public function update(Transfer $transfer, array $input): Transfer
    {
        return DB::transaction(
            fn () => tap($transfer, fn (Transfer $transfer) => $transfer->update($input))
        );
    }

    public function destroy(Transfer $transfer): Transfer
    {
        return DB::transaction(
            fn () => tap($transfer, fn (Transfer $transfer) => $transfer->delete())
        );
    }
}
