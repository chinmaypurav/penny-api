<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Http\Resources\TransferResource;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;

class TransferController extends Controller
{
    protected User $user;

    public function __construct(protected TransferService $transferService)
    {
        $this->authorizeResource(Transfer::class);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function index()
    {
        return TransferResource::collection($this->transferService->index($this->user));
    }

    public function store(StoreTransferRequest $request)
    {
        return TransferResource::make($this->transferService->store($this->user, $request->validated()));
    }

    public function show(Transfer $transfer)
    {
        return TransferResource::make($transfer);
    }

    public function update(UpdateTransferRequest $request, Transfer $transfer)
    {
        return TransferResource::make($this->transferService->update($transfer, $request->validated()));
    }

    public function destroy(Transfer $transfer)
    {
        $this->transferService->destroy($transfer);

        return response()->noContent();
    }
}
