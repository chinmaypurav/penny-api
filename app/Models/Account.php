<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'account_type', 'balance'];

    protected $casts = [
        'balance' => 'double',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function creditTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'creditor_id');
    }

    public function debitTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'debitor_id');
    }
}
