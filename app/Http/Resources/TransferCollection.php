<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Transfer */
class TransferCollection extends ResourceCollection
{
    public static $wrap = 'transfers';
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
