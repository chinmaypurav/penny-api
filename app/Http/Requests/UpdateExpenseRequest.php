<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'transacted_at' => ['sometimes', 'required', 'date'],
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'amount' => ['sometimes', 'required', 'numeric'],
        ];
    }
}
