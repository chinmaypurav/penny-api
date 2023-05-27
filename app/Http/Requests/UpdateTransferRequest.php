<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'creditor_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'debitor_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'amount' => ['sometimes', 'required', 'numeric'],
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'transaction_id' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
