<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string) $this->input('first_name', '')),
            'last_name' => trim((string) $this->input('last_name', '')),
            'username' => Str::lower(trim((string) $this->input('username', ''))),
            'phone' => $this->normalizeNullable('phone'),
            'restaurant_name' => trim((string) $this->input('restaurant_name', '')),
            'message' => $this->normalizeNullable('message'),
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username', 'unique:venue_connections,username'],
            'phone' => ['nullable', 'string', 'max:50'],
            'restaurant_name' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->numbers(),
                'regex:/[A-Z]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            'password_confirmation' => ['required', 'string'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        $messages = match ($this->query('lang', 'uz')) {
            'ru' => [
                'first_name.required' => 'Введите имя.',
                'last_name.required' => 'Введите фамилию.',
                'username.required' => 'Введите логин.',
                'username.alpha_dash' => 'Логин может содержать только буквы, цифры, дефис и подчеркивание.',
                'username.unique' => 'Этот логин уже занят.',
                'restaurant_name.required' => 'Введите название заведения.',
                'password.required' => 'Введите пароль.',
                'password.confirmed' => 'Подтверждение пароля не совпадает.',
                'password.min' => 'Пароль должен содержать минимум 8 символов.',
                'password.letters' => 'Пароль должен содержать буквы.',
                'password.numbers' => 'Пароль должен содержать цифры.',
                'password.regex' => 'Пароль должен содержать хотя бы одну заглавную букву и один специальный символ.',
                'terms.accepted' => 'Необходимо принять условия использования.',
            ],
            'en' => [
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'username.required' => 'Username is required.',
                'username.alpha_dash' => 'Username may only contain letters, numbers, dashes, and underscores.',
                'username.unique' => 'This username is already taken.',
                'restaurant_name.required' => 'Venue name is required.',
                'password.required' => 'Password is required.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.letters' => 'Password must contain letters.',
                'password.numbers' => 'Password must contain numbers.',
                'password.regex' => 'Password must include at least one uppercase letter and one special character.',
                'terms.accepted' => 'You must accept the terms.',
            ],
            default => [
                'first_name.required' => 'Ismni kiriting.',
                'last_name.required' => 'Familiyani kiriting.',
                'username.required' => 'Loginni kiriting.',
                'username.alpha_dash' => "Login faqat harf, raqam, `-` va `_` belgilaridan iborat bo'lishi mumkin.",
                'username.unique' => 'Bu login allaqachon band.',
                'restaurant_name.required' => "To'yxona nomini kiriting.",
                'password.required' => 'Parolni kiriting.',
                'password.confirmed' => 'Parol tasdig‘i mos kelmadi.',
                'password.min' => "Parol kamida 8 ta belgidan iborat bo'lishi kerak.",
                'password.letters' => "Parolda harflar bo'lishi kerak.",
                'password.numbers' => "Parolda raqamlar bo'lishi kerak.",
                'password.regex' => "Parolda kamida 1 ta katta harf va 1 ta maxsus belgi bo'lishi kerak.",
                'terms.accepted' => 'Foydalanish shartlariga rozilik berish kerak.',
            ],
        };

        return $messages;
    }

    private function normalizeNullable(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value !== '' ? $value : null;
    }
}
