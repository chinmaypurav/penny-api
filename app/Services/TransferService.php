<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function index(User $user): Collection
    {
        return $user->transfers()->get();
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
