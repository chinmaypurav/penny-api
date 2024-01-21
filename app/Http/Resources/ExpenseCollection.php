<?php

namespace App\Http\Resources;

use App\Models\Expense;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see Expense */
class ExpenseCollection extends ResourceCollection
{
    public static $wrap = 'expenses';
}
