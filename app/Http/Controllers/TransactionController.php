<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionCollection;
use App\Models\Expense;
use App\Models\Income;
use App\Services\ExpenseService;
use App\Services\IncomeService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __invoke(IncomeService $incomeService, ExpenseService $expenseService)
    {
        $transactions = collect()
            ->merge($incomeService->index(Auth::id()))
            ->merge($expenseService->index(Auth::id()))
            ->sort(fn (Income|Expense $model) => $model->transacted_at->getTimestamp());

        return TransactionCollection::make($transactions);
    }
}
