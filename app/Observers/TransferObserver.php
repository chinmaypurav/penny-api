<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transfer;
use App\Services\AccountService;

readonly class TransferObserver
{
    public function __construct(private AccountService $accountService)
    {
    }

    public function created(Transfer $transfer): void
    {
        $transfer->debtorAccount()->decrement('balance', $transfer->amount);
        $transfer->creditorAccount()->increment('balance', $transfer->amount);
    }

    public function updating(Transfer $transfer): void
    {
        $oldAmount = $transfer->getOriginal('amount');
        $newAmount = $transfer->getAttribute('amount');
        $diff = 0;

        if ($transfer->isDirty('amount')) {
            $diff = $oldAmount - $newAmount;
            $transfer->debtorAccount()->increment('balance', $diff);
            $transfer->creditorAccount()->decrement('balance', $diff);
        }

        if ($transfer->isDirty('creditor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('creditor_id'));
            $newAccount = Account::find($transfer->getAttribute('creditor_id'));

            $this->accountService->decrement($oldAccount, $oldAmount);
            $this->accountService->increment($newAccount, $newAmount + $diff);
        }

        if ($transfer->isDirty('debtor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('debtor_id'));
            $newAccount = Account::find($transfer->getAttribute('debtor_id'));

            $this->accountService->increment($oldAccount, $oldAmount);
            $this->accountService->decrement($newAccount, $newAmount + $diff);
        }

    }

    public function deleted(Transfer $transfer): void
    {
        $transfer->debtorAccount()->increment('balance', $transfer->amount);
        $transfer->creditorAccount()->decrement('balance', $transfer->amount);
    }
}
