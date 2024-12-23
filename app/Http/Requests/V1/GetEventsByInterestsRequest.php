<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class GetEventsByInterestsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Добавьте логику авторизации при необходимости
    }

    public function rules(): array
    {
        return [
            'interests' => 'nullable|array',
            'interests.*' => 'integer|exists:interests,id',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'interests.array' => 'Поле интересов должно быть массивом.',
            'interests.*.integer' => 'Идентификатор интереса должен быть числом.',
            'interests.*.exists' => 'Выбранный интерес не существует.',
            // Добавьте дополнительные сообщения об ошибках при необходимости
        ];
    }
}
