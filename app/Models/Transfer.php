<?php

namespace App\Models;

use App\Concerns\Transactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory, Transactable;

    protected $fillable = [
        'creditor_id', 'debtor_id', 'amount', 'transacted_at', 'scheduled_at', 'completed', 'description', 'transaction_id',
    ];

    protected $casts = [
        'transacted_at' => 'datetime:Y-m-d',
        'scheduled_at' => 'datetime:Y-m-d',
        'completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creditorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'creditor_id');
    }

    public function debtorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debtor_id');
    }
}
