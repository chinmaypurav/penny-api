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
        if ($transfer->isDirty('amount')) {
            $originalAmount = $transfer->getOriginal('amount');
            $modifiedAmount = $transfer->getAttribute('amount');
            $diff = $originalAmount - $modifiedAmount;

            $transfer->debtorAccount()->increment('balance', $diff);
            $transfer->creditorAccount()->decrement('balance', $diff);
        }

        if ($transfer->isDirty('creditor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('creditor_id'));
            $newAccount = Account::find($transfer->getAttribute('creditor_id'));

            $this->accountService->decrement($oldAccount, $transfer->amount);
            $this->accountService->increment($newAccount, $transfer->amount);
        }

        if ($transfer->isDirty('debtor_id')) {
            $oldAccount = Account::find($transfer->getOriginal('debtor_id'));
            $newAccount = Account::find($transfer->getAttribute('debtor_id'));

            $this->accountService->increment($oldAccount, $transfer->amount);
            $this->accountService->decrement($newAccount, $transfer->amount);
        }

    }

    public function deleted(Transfer $transfer): void
    {
        $transfer->debtorAccount()->increment('balance', $transfer->amount);
        $transfer->creditorAccount()->decrement('balance', $transfer->amount);
    }
}
