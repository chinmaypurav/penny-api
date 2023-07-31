<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date_to' => ['sometimes', 'date'],
            'date_from' => ['sometimes', 'date'],
        ];
    }
}
