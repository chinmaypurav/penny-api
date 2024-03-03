<?php

namespace App\Models;

use App\Concerns\Transactable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, Transactable;

    protected $fillable = [
        'account_id', 'category_id', 'description', 'transacted_at', 'scheduled_at', 'completed', 'amount',
    ];

    protected $casts = [
        'transacted_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed' => 'true',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (float $amount) => -1 * $amount
        );
    }
}
