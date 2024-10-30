<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Почта обязательна.',
            'email.email'    => 'Почта не прошла валидацию.',
            'password.required' => 'Пароль обязателен.',
            'password.min'    => 'Пароль должен иметь минимум 6 символов.',
        ];
    }
}
