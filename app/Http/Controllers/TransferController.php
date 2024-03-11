<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Http\Resources\TransferCollection;
use App\Http\Resources\TransferResource;
use App\Models\Transfer;
use App\Services\TransferService;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct(private readonly TransferService $transferService)
    {
        $this->authorizeResource(Transfer::class);
    }

    public function index()
    {
        return TransferCollection::make($this->transferService->index(Auth::id()));
    }

    public function store(StoreTransferRequest $request)
    {
        return TransferResource::make($this->transferService->store(Auth::user(), $request->validated()));
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
