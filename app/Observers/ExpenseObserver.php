<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\AccountService;

class ExpenseObserver
{
    public function __construct(protected AccountService $accountService)
    {
    }

    public function creating(Expense $expense): void
    {
    }

    public function created(Expense $expense): void
    {
        $this->accountService->decrement($expense->account()->first(), $expense->amount);
    }

    public function updating(Expense $expense): void
    {
        $originalAmount = $expense->getOriginal('amount');
        $modifiedAmount = $expense->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        $this->accountService->increment($expense->account()->first(), $diff);
    }

    public function deleted(Expense $expense): void
    {
        $this->accountService->increment($expense->account, $expense->amount);
    }
}
