<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = ['creditor_id', 'debitor_id', 'amount', 'transacted_at', 'scheduled_at', 'completed', 'description', 'transaction_id'];

    protected $casts = [
        'transacted_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed' => 'true',
    ];

    public function creditorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'creditor_id');
    }

    public function debitorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debitor_id');
    }
}
