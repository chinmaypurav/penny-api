<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = ['creditor_id', 'debitor_id', 'amount', 'description', 'transaction_id'];

    public function creditorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'creditor_id');
    }

    public function debitorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debitor_id');
    }
}
