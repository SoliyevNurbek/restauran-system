<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTelegramConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'telegram_notifications_enabled' => ['nullable', 'boolean'],
        ];
    }
}
