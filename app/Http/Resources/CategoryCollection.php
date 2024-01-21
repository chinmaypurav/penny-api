<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see Category */
class CategoryCollection extends ResourceCollection
{
    public static $wrap = 'categories';
}
