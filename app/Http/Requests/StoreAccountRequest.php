<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string', Rule::in(AccountType::all())],
            'balance' => ['sometimes', 'numeric'],
        ];
    }
}
