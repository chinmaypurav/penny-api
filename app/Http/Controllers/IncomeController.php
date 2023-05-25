<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use App\Models\User;
use App\Services\IncomeService;

class AccountController extends Controller
{
    protected User $user;

    public function __construct(protected IncomeService $incomeService)
    {
        $this->authorizeResource(Account::class);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function index()
    {
        return IncomeResource::collection($this->incomeService->index($this->user));
    }

    public function store(StoreIncomeRequest $request)
    {
        return IncomeResource::make($this->incomeService->store($this->user, $request->validated()));
    }

    public function show(Income $income)
    {
        return IncomeResource::make($income);
    }

    public function update(UpdateIncomeRequest $request, Income $income)
    {
        return IncomeResource::make($this->incomeService->update($income, $request->validated()));
    }

    public function destroy(Income $income)
    {
        $this->incomeService->destroy($income);

        return response()->noContent();
    }
}
