<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexExpenseRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseService;

class ExpenseController extends Controller
{
    protected User $user;

    public function __construct(protected ExpenseService $expenseService)
    {
        $this->authorizeResource(Account::class);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();

            return $next($request);
        });
    }

    public function index(IndexExpenseRequest $request)
    {
        return ExpenseResource::collection($this->expenseService->index($this->user, $request->input()));
    }

    public function store(StoreExpenseRequest $request)
    {
        return ExpenseResource::make($this->expenseService->store($this->user, $request->validated()));
    }

    public function show(Expense $expense)
    {
        return ExpenseResource::make($expense);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        return ExpenseResource::make($this->expenseService->update($expense, $request->validated()));
    }

    public function destroy(Expense $expense)
    {
        $this->expenseService->destroy($expense);

        return response()->noContent();
    }
}
