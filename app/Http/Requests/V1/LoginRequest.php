<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Или добавить логику авторизации
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email', // unique:users,email
            'password' => 'required|string|min:6', //confirmed
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
