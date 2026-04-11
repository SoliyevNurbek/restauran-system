<?php

namespace App\Services\SuperAdmin;

use App\Services\Billing\TelegramBotService;
use App\Services\Billing\TelegramSettingsService;

class TelegramNotificationService
{
    public function __construct(
        private readonly TelegramSettingsService $settings,
        private readonly TelegramBotService $bot,
    ) {
    }

    public function isConfigured(): bool
    {
        return $this->settings->enabled();
    }

    public function send(string $message): array
    {
        $settings = $this->settings->all();
        $chatId = $settings['admin_chat_id'];

        if (! $this->settings->enabled() || ! $chatId) {
            return ['ok' => false, 'message' => 'Telegram integratsiyasi sozlanmagan.'];
        }

        try {
            return $this->bot->sendMessage($chatId, $message);
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function format(string $heading, string $eventType, string $subject, array $lines = []): string
    {
        $parts = [
            '<b>'.e($heading).'</b>',
            'Kategoriya: <b>'.e($eventType).'</b>',
            'Subyekt: <b>'.e($subject).'</b>',
        ];

        foreach ($lines as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $parts[] = e((string) $label).': <b>'.e((string) $value).'</b>';
        }

        $parts[] = 'Vaqt: <b>'.now()->format('d.m.Y H:i').'</b>';

        return implode("\n", $parts);
    }
}
