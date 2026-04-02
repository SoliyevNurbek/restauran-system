<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'restaurant_name' => trim((string) $this->input('restaurant_name', '')),
            'contact_phone' => $this->normalizeNullableString('contact_phone'),
            'notification_email' => $this->normalizeNullableEmail('notification_email'),
        ]);
    }

    public function rules(): array
    {
        return [
            'restaurant_name' => ['required', 'string', 'min:2', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'notification_email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'logo' => [
                'nullable',
                File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(4 * 1024),
            ],
            'favicon' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg', 'webp', 'ico'])->max(2 * 1024),
            ],
        ];
    }

    private function normalizeNullableString(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value !== '' ? $value : null;
    }

    private function normalizeNullableEmail(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value !== '' ? Str::lower($value) : null;
    }
}
