<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExampleRequest extends FormRequest
{
    // Метод авторизации доступа
    public function authorize(): bool
    {
        return true; // Или добавить логику авторизации
    }

    // Правила валидации
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            // Другие правила...
        ];
    }
}
