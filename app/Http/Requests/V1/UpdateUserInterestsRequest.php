<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserInterestsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'interest_ids' => 'required|array',
            'interest_ids.*' => 'exists:interests,id',
        ];
    }

    public function messages(): array
    {
        return [
            'interest_ids' => 'Ошибка в определении interest_ids.',
        ];
    }
}
