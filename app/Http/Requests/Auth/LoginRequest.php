<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => Str::lower(trim((string) $this->input('username', ''))),
        ]);
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:50',
            ],
            'password' => ['required', 'string', 'min:4', 'max:255'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => $this->genericMessage(),
            'username.string' => $this->genericMessage(),
            'username.max' => $this->genericMessage(),
            'password.required' => $this->genericMessage(),
            'password.string' => $this->genericMessage(),
            'password.min' => $this->genericMessage(),
            'password.max' => $this->genericMessage(),
            'remember.boolean' => $this->genericMessage(),
        ];
    }

    public function loginIdentifier(): string
    {
        return (string) $this->validated('username');
    }

    public function throttleKey(): string
    {
        return 'auth:login:attempts:'.hash('sha256', Str::lower($this->loginIdentifier()).'|'.$this->ip());
    }

    public function lockoutKey(): string
    {
        return 'auth:login:lockout:'.hash('sha256', Str::lower($this->loginIdentifier()).'|'.$this->ip());
    }

    public function failuresKey(): string
    {
        return 'auth:login:failures:'.hash('sha256', Str::lower($this->loginIdentifier()).'|'.$this->ip());
    }

    public function genericMessage(): string
    {
        return match ($this->query('lang', 'uz')) {
            'ru' => 'Логин или пароль неверны',
            'en' => 'Invalid login or password',
            default => "Login yoki parol notog'ri",
        };
    }
}
