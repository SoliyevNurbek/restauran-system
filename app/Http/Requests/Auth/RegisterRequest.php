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
            'username' => ['required', 'string', 'max:50', 'alpha_dash'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
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
        return match ($this->query('lang', 'uz')) {
            'ru' => [
                'first_name.required' => 'Р’РІРµРґРёС‚Рµ РёРјСЏ.',
                'last_name.required' => 'Р’РІРµРґРёС‚Рµ С„Р°РјРёР»РёСЋ.',
                'username.required' => 'Р’РІРµРґРёС‚Рµ Р»РѕРіРёРЅ.',
                'username.alpha_dash' => 'Р›РѕРіРёРЅ РјРѕР¶РµС‚ СЃРѕРґРµСЂР¶Р°С‚СЊ С‚РѕР»СЊРєРѕ Р±СѓРєРІС‹, С†РёС„СЂС‹, РґРµС„РёСЃ Рё РїРѕРґС‡РµСЂРєРёРІР°РЅРёРµ.',
                'phone.regex' => 'РќРµРєРѕСЂСЂРµРєС‚РЅС‹Р№ С„РѕСЂРјР°С‚ С‚РµР»РµС„РѕРЅР°.',
                'restaurant_name.required' => 'Р’РІРµРґРёС‚Рµ РЅР°Р·РІР°РЅРёРµ Р·Р°РІРµРґРµРЅРёСЏ.',
                'password.required' => 'Р’РІРµРґРёС‚Рµ РїР°СЂРѕР»СЊ.',
                'password.confirmed' => 'РџРѕРґС‚РІРµСЂР¶РґРµРЅРёРµ РїР°СЂРѕР»СЏ РЅРµ СЃРѕРІРїР°РґР°РµС‚.',
                'password.min' => 'РџР°СЂРѕР»СЊ РґРѕР»Р¶РµРЅ СЃРѕРґРµСЂР¶Р°С‚СЊ РјРёРЅРёРјСѓРј 8 СЃРёРјРІРѕР»РѕРІ.',
                'password.letters' => 'РџР°СЂРѕР»СЊ РґРѕР»Р¶РµРЅ СЃРѕРґРµСЂР¶Р°С‚СЊ Р±СѓРєРІС‹.',
                'password.numbers' => 'РџР°СЂРѕР»СЊ РґРѕР»Р¶РµРЅ СЃРѕРґРµСЂР¶Р°С‚СЊ С†РёС„СЂС‹.',
                'password.regex' => 'РџР°СЂРѕР»СЊ РґРѕР»Р¶РµРЅ СЃРѕРґРµСЂР¶Р°С‚СЊ С…РѕС‚СЏ Р±С‹ РѕРґРЅСѓ Р·Р°РіР»Р°РІРЅСѓСЋ Р±СѓРєРІСѓ Рё РѕРґРёРЅ СЃРїРµС†СЃРёРјРІРѕР».',
                'terms.accepted' => 'РќРµРѕР±С…РѕРґРёРјРѕ РїСЂРёРЅСЏС‚СЊ СѓСЃР»РѕРІРёСЏ РёСЃРїРѕР»СЊР·РѕРІР°РЅРёСЏ.',
            ],
            'en' => [
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'username.required' => 'Username is required.',
                'username.alpha_dash' => 'Username may only contain letters, numbers, dashes, and underscores.',
                'phone.regex' => 'Phone number format is invalid.',
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
                'phone.regex' => "Telefon raqami formati noto'g'ri.",
                'restaurant_name.required' => "To'yxona nomini kiriting.",
                'password.required' => 'Parolni kiriting.',
                'password.confirmed' => "Parol tasdig'i mos kelmadi.",
                'password.min' => "Parol kamida 8 ta belgidan iborat bo'lishi kerak.",
                'password.letters' => "Parolda harflar bo'lishi kerak.",
                'password.numbers' => "Parolda raqamlar bo'lishi kerak.",
                'password.regex' => "Parolda kamida 1 ta katta harf va 1 ta maxsus belgi bo'lishi kerak.",
                'terms.accepted' => 'Foydalanish shartlariga rozilik berish kerak.',
            ],
        };
    }

    private function normalizeNullable(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value !== '' ? $value : null;
    }
}
