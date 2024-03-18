<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'creditor_id' => ['sometimes', 'required', 'integer', 'different:debtor_id', 'exists:accounts,id'],
            'debtor_id' => ['sometimes', 'required', 'integer', 'different:creditor_id', 'exists:accounts,id'],
            'amount' => ['sometimes', 'required', 'numeric'],
            'transacted_at' => ['sometimes', 'required', 'date'],
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'transaction_id' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
