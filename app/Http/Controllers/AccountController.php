<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\User;
use App\Services\AccountService;

class AccountController extends Controller
{
    protected User $user;

    public function __construct(protected AccountService $accountService)
    {
        $this->authorizeResource(Account::class);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function index()
    {
        return AccountResource::collection($this->accountService->index($this->user));
    }

    public function store(StoreAccountRequest $request)
    {
        return AccountResource::make($this->accountService->store($this->user, $request->validated()));
    }

    public function show(Account $account)
    {
        return AccountResource::make($account);
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        return AccountResource::make($this->accountService->update($account, $request->validated()));
    }

    public function destroy(Account $account)
    {
        $this->accountService->destroy($account);

        return response()->noContent();
    }
}
