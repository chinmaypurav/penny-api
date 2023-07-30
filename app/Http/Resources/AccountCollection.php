<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see Account */
class AccountCollection extends ResourceCollection
{
    public static $wrap = 'accounts';

    public function with(Request $request): array
    {
        return [
            'balance_total' => $this->collection->sum(fn (AccountResource $account) => $account->resource->balance),
        ];
    }
}
