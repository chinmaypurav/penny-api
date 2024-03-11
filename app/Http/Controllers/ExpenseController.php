<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseCollection;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    protected User $user;

    public function __construct(private readonly ExpenseService $expenseService)
    {
        $this->authorizeResource(Expense::class);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();

            return $next($request);
        });
    }

    public function index(): JsonResource
    {
        return ExpenseCollection::make($this->expenseService->index(Auth::id()));
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
