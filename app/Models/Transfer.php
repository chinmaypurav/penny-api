<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'creditor_id', 'debtor_id', 'amount', 'transacted_at', 'scheduled_at', 'completed', 'description', 'transaction_id',
    ];

    protected $casts = [
        'transacted_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed' => 'boolean',
    ];

    public function creditorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'creditor_id');
    }

    public function debtorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debtor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
