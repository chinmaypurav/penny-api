<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionCollection;
use App\Services\ExpenseService;
use App\Services\IncomeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __invoke(IncomeService $incomeService, ExpenseService $expenseService, Request $request)
    {
        $transactions = collect()
            ->merge($incomeService->index($request->user()))
            ->merge($expenseService->index($request->user()))
            ->sort(fn (Model $model) => $model->transacted_at->getTimestamp());

        return TransactionCollection::make($transactions);
    }
}
