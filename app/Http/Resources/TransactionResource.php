<?php

namespace App\Http\Resources;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Income
 * @mixin Expense
 */
class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'transaction_type' => class_basename(get_class($this->resource)),
            'account_name' => $this->whenLoaded('account', fn () => $this->account->name),
            'category_name' => $this->whenLoaded('category', fn () => $this->category->name),
            'description' => $this->description,
            'amount' => $this->amount,
            'transacted_at' => $this->transacted_at->toIso8601ZuluString(),
        ];
    }
}
