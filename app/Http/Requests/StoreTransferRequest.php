<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'creditor_id' => ['required', 'integer', 'exists:accounts,id'],
            'debitor_id' => ['required', 'integer', 'exists:accounts,id'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_id' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
