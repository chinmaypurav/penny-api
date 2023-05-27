<?php

namespace App\Observers;

use App\Models\Transfer;

class TransferObserver
{
    public function created(Transfer $transfer): void
    {
        $transfer->debitorAccount()->decrement('balance', $transfer->amount);
        $transfer->creditorAccount()->increment('balance', $transfer->amount);
    }

    public function deleted(Transfer $transfer): void
    {
        $transfer->debitorAccount()->increment('balance', $transfer->amount);
        $transfer->creditorAccount()->decrement('balance', $transfer->amount);
    }
}
