<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexIncomeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date_to' => ['sometimes', 'date'],
            'date_from' => ['sometimes', 'date'],
        ];
    }
}
