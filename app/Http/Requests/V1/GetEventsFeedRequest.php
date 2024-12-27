<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class GetEventsFeedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Здесь вы можете добавить логику авторизации, если необходимо
    }

    public function rules(): array
    {
        return [
            'latitude'           => 'nullable|numeric|between:-90,90',
            'longitude'          => 'nullable|numeric|between:-180,180',
            'interest_ids'       => 'nullable|array',
            'interest_ids.*'     => 'integer',
            'cursor'             => 'nullable|string',
            'per_page'           => 'nullable|integer|min:1',
            // Добавьте другие правила валидации, если необходимо
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Поле широты (latitude) обязательно.',
            'latitude.numeric'  => 'Поле широты (latitude) должно быть числом.',
            'latitude.between'  => 'Значение широты должно быть между -90 и 90.',
            'longitude.required' => 'Поле долготы (longitude) обязательно.',
            'longitude.numeric'  => 'Поле долготы (longitude) должно быть числом.',
            'longitude.between'  => 'Значение долготы должно быть между -180 и 180.',
            'interest_ids.required' => 'Поле интересов (interest_ids) обязательно.',
            'interest_ids.array' => 'Поле интересов (interest_ids) должно быть массивом.',
            'interest_ids.*.integer' => 'Каждый интерес в поле interest_ids должен быть целым числом.',
            // Добавьте дополнительные сообщения об ошибках при необходимости
        ];
    }
}
