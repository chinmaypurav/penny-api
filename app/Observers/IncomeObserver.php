<?php

namespace App\Observers;

use App\Models\Income;
use App\Services\AccountService;

class IncomeObserver
{
    public function __construct(protected AccountService $accountService)
    {
    }

    public function creating(Income $income): void
    {
    }

    public function created(Income $income): void
    {
        $this->accountService->increment($income->account()->first(), $income->amount);
    }

    public function updating(Income $income): void
    {
        $originalAmount = $income->getOriginal('amount');
        $modifiedAmount = $income->getAttribute('amount');

        $diff = $originalAmount - $modifiedAmount;

        $this->accountService->decrement($income->account()->first(), $diff);
    }

    public function deleted(Income $income): void
    {
        $this->accountService->decrement($income->account, $income->amount);
    }
}
