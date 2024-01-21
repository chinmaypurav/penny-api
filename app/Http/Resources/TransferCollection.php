<?php

namespace App\Http\Resources;

use App\Models\Transfer;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see Transfer */
class TransferCollection extends ResourceCollection
{
    public static $wrap = 'transfers';
}
