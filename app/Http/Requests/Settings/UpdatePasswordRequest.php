<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'Yangi parol tasdiqlash maydoni bilan mos kelmadi.',
            'password.different' => 'Yangi parol joriy paroldan farq qilishi kerak.',
            'password.min' => 'Yangi parol kamida 8 ta belgidan iborat bo‘lishi kerak.',
            'password.letters' => 'Yangi parolda kamida 1 ta harf bo‘lishi kerak.',
            'password.mixed' => 'Yangi parolda katta va kichik harflar bo‘lishi kerak.',
            'password.numbers' => 'Yangi parolda kamida 1 ta raqam bo‘lishi kerak.',
            'password.symbols' => 'Yangi parolda kamida 1 ta maxsus belgi bo‘lishi kerak.',
        ];
    }
}
