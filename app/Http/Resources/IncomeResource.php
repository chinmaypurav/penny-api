<?php

namespace App\Http\Resources;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Income */
class IncomeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'account' => AccountResource::make($this->whenLoaded('account')),
            'category_id' => $this->category_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'description' => $this->description,
            'amount' => number_format($this->amount, 2),
            'created_at' => $this->created_at,
        ];
    }
}
