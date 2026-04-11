<?php

namespace App\Http\Requests\SuperAdmin;

use App\Services\Billing\TelegramSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTelegramWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $notificationSettings = [];

        foreach (TelegramSettingsService::NOTIFICATION_TYPES as $type) {
            $notificationSettings[$type] = $this->boolean('notification_settings.'.$type);
        }

        $this->merge([
            'notification_settings' => $notificationSettings,
            'message_templates' => [
                'payment_instruction' => trim((string) $this->input('message_templates.payment_instruction', '')),
                'payment_approved' => trim((string) $this->input('message_templates.payment_approved', '')),
                'payment_rejected' => trim((string) $this->input('message_templates.payment_rejected', '')),
                'expiry_reminder' => trim((string) $this->input('message_templates.expiry_reminder', '')),
            ],
        ]);
    }

    public function rules(): array
    {
        return [
            'is_enabled' => ['nullable', 'boolean'],
            'bot_token' => ['nullable', 'string', 'max:255'],
            'bot_username' => ['nullable', 'string', 'max:255'],
            'webhook_secret' => ['nullable', 'string', 'min:16', 'max:255'],
            'admin_chat_id' => ['nullable', 'string', 'max:120'],
            'payment_card_number' => ['nullable', 'string', 'max:64'],
            'payment_card_holder' => ['nullable', 'string', 'max:255'],
            'notification_settings' => ['nullable', 'array'],
            'notification_settings.*' => ['boolean'],
            'message_templates' => ['nullable', 'array'],
            'message_templates.*' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
