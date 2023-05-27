<?php

namespace App\Observers;

use App\Enums\AccountType;
use App\Models\Income;
use App\Services\AccountService;

class IncomeObserver
{
    public function __construct(protected AccountService $accountService)
    {
    }

    public function creating(Income $income): void
    {
        $this->changeSign($income);
    }

    public function created(Income $income): void
    {
        $this->accountService->increment($income->account, $income->amount);
    }

    public function updating(Income $income): void
    {
        if ($income->isDirty('account_type')) {
            $this->changeSign($income);
        }

        $orginalAmount = $income->getOriginal('amount');
        $modifiedAmount = $income->getAttribute('amount');

        $diff = $orginalAmount - $modifiedAmount;

        $this->accountService->decrement($income->account, $diff);
    }

    public function deleted(Income $income): void
    {
        $this->accountService->decrement($income->account, $income->amount);
    }

    private function changeSign(Income $income): void
    {
        $accountType = $income->account()->value('account_type');

        if ($accountType === AccountType::CREDIT->value) {
            $income->amount *= -1;
        }
    }
}
