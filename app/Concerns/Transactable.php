<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait Transactable
{
    public function scopeTransactedAfter(Builder $query, string $date): Builder
    {
        return $query->whereDate('transacted_at', '>=', Carbon::parse($date));
    }

    public function scopeTransactedBefore(Builder $query, string $date): Builder
    {
        return $query->whereDate('transacted_at', '<=', Carbon::parse($date));
    }
}
