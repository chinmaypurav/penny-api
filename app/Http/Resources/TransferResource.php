<?php

namespace App\Http\Resources;

use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Transfer */
class TransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'creditor' => AccountResource::make($this->creditorAccount),
            'debtor' => AccountResource::make($this->debtorAccount),
            'transacted_at' => $this->transacted_at,
        ];
    }
}
