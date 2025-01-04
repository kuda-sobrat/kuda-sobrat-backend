<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class GetEventsByCoordinatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Если нужна логика авторизации, добавьте ее здесь
    }

    public function rules(): array
    {
        return [
            'latitude'           => 'required|numeric|between:-90,90',
            'longitude'          => 'required|numeric|between:-180,180',
            'radius'             => 'nullable|numeric|min:0',
            'weight_popularity'  => 'nullable|numeric|min:0',
            'weight_distance'    => 'nullable|numeric|min:0',
            'per_page'           => 'nullable|integer|min:1',
            'page'               => 'nullable|integer|min:1',
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
            // Добавьте дополнительные сообщения об ошибках при необходимости
        ];
    }
}
