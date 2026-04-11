<?php

namespace App\Services\Billing;

use App\Models\IntegrationSetting;

class TelegramSettingsService
{
    public const NOTIFICATION_TYPES = [
        'payment_instructions',
        'subscription_activated',
        'payment_rejected',
        'expiry_reminder',
        'low_stock_alert',
        'debt_alert',
        'booking_alert',
        'superadmin_message',
    ];

    public function all(): array
    {
        $notificationSettings = json_decode(IntegrationSetting::valueFor('telegram.notifications') ?: '{}', true);
        $messageTemplates = json_decode(IntegrationSetting::valueFor('telegram.templates') ?: '{}', true);

        return [
            'is_enabled' => (bool) IntegrationSetting::valueFor('telegram.is_enabled'),
            'bot_token' => IntegrationSetting::valueFor('telegram.bot_token'),
            'bot_username' => IntegrationSetting::valueFor('telegram.bot_username'),
            'admin_chat_id' => IntegrationSetting::valueFor('telegram.chat_id'),
            'webhook_secret' => IntegrationSetting::valueFor('telegram.webhook_secret') ?: config('security.telegram.webhook_secret'),
            'payment_card_number' => IntegrationSetting::valueFor('telegram.payment_card_number'),
            'payment_card_holder' => IntegrationSetting::valueFor('telegram.payment_card_holder'),
            'notification_settings' => is_array($notificationSettings) ? $notificationSettings : [],
            'message_templates' => is_array($messageTemplates) ? $messageTemplates : [],
        ];
    }

    public function enabled(): bool
    {
        $settings = $this->all();

        return $settings['is_enabled']
            && filled($settings['bot_token'])
            && filled($settings['admin_chat_id']);
    }

    public function notificationEnabled(string $type): bool
    {
        $settings = $this->all();

        return (bool) ($settings['notification_settings'][$type] ?? false);
    }

    public function put(array $payload): void
    {
        IntegrationSetting::putValue('telegram.is_enabled', ! empty($payload['is_enabled']) ? '1' : '0');
        IntegrationSetting::putValue('telegram.bot_username', $payload['bot_username'] ?? null);
        IntegrationSetting::putValue('telegram.chat_id', $payload['admin_chat_id'] ?? null);
        if (! empty($payload['webhook_secret'])) {
            IntegrationSetting::putValue('telegram.webhook_secret', $payload['webhook_secret'], true);
        }
        IntegrationSetting::putValue('telegram.payment_card_number', $payload['payment_card_number'] ?? null);
        IntegrationSetting::putValue('telegram.payment_card_holder', $payload['payment_card_holder'] ?? null);

        if (! empty($payload['bot_token'])) {
            IntegrationSetting::putValue('telegram.bot_token', $payload['bot_token'], true);
        }

        IntegrationSetting::putValue('telegram.notifications', json_encode($payload['notification_settings'] ?? []));
        IntegrationSetting::putValue('telegram.templates', json_encode($payload['message_templates'] ?? []));
    }
}
