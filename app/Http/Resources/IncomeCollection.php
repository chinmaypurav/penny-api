<?php

namespace App\Http\Resources;

use App\Models\Income;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see Income */
class IncomeCollection extends ResourceCollection
{
    public static $wrap = 'incomes';
}
