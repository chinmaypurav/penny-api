<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'creditor_id' => ['required', 'integer', 'exists:accounts,id'],
            'debtor_id' => ['required', 'integer', 'exists:accounts,id'],
            'amount' => ['required', 'numeric'],
            'transacted_at' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_id' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
