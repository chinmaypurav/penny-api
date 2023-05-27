<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransferPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->id;
    }

    public function delete(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->id;
    }
}
