<?php

namespace App\Observers;

use App\Enums\AccountType;
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
        $this->accountService->decrement($expense->account, $expense->amount);
    }

    public function updating(Expense $expense): void
    {
        $originalAmount = $expense->getOriginal('amount');
        $modifiedAmount = $expense->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        $this->accountService->increment($expense->account, $diff);
    }

    public function deleted(Expense $expense): void
    {
        $this->accountService->increment($expense->account, $expense->amount);
    }

    private function changeSign(Expense $expense): void
    {
        $accountType = $expense->account()->value('account_type');

        if ($accountType === AccountType::CREDIT->value) {
            $expense->amount *= -1;
        }
    }
}
