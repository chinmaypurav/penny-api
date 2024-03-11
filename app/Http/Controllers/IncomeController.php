<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Http\Resources\IncomeCollection;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use App\Services\IncomeService;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function __construct(protected IncomeService $incomeService)
    {
        $this->authorizeResource(Income::class);
    }

    public function index()
    {
        return IncomeCollection::make($this->incomeService->index(Auth::id()));
    }

    public function store(StoreIncomeRequest $request)
    {
        return IncomeResource::make($this->incomeService->store(Auth::user(), $request->validated()));
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
