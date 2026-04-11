<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
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
        ]);
    }

    public function rules(): array
    {
        return [
            'restaurant_name' => ['required', 'string', 'min:2', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'logo' => [
                'nullable',
                File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(4 * 1024),
            ],
            'favicon' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg', 'webp', 'ico'])->max(2 * 1024),
            ],
            'brand_logo' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png', 'webp', 'svg'])->max(4 * 1024),
            ],
            'brand_favicon' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg', 'webp', 'ico'])->max(2 * 1024),
            ],
            'landing_preview_dashboard' => [
                'nullable',
                File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(4 * 1024),
            ],
            'landing_preview_admin' => [
                'nullable',
                File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(4 * 1024),
            ],
            'landing_preview_analytics' => [
                'nullable',
                File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(4 * 1024),
            ],
        ];
    }

    private function normalizeNullableString(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value !== '' ? $value : null;
    }
}
