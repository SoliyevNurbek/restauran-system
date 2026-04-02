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
        $username = trim((string) $this->input('username', ''));

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $username = Str::lower($username);
        }

        $this->merge([
            'username' => $username,
        ]);
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (str_contains((string) $value, '@') && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail($this->genericMessage());
                    }
                },
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
        return "Login yoki parol notog'ri";
    }
}
