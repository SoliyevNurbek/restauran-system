<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use App\Models\VenueConnection;

class TelegramTenantMessageService
{
    public function __construct(
        private readonly TelegramBotService $bot,
        private readonly TelegramMessageLogService $logs,
    ) {
    }

    public function sendToBusiness(
        VenueConnection $venue,
        string $message,
        string $messageType = 'alert',
        ?SubscriptionPayment $payment = null,
        array $meta = [],
    ): array {
        if (! $venue->telegram_notifications_enabled) {
            return ['ok' => false, 'message' => 'Biznes uchun Telegram bildirishnomalari o‘chirilgan.'];
        }

        if (! $venue->telegram_chat_id) {
            return ['ok' => false, 'message' => 'Biznes Telegram chatiga ulanmagan.'];
        }

        $sent = $this->bot->sendMessage($venue->telegram_chat_id, $message);

        if ($sent['ok']) {
            $this->logs->log(
                direction: 'outgoing',
                chatId: $venue->telegram_chat_id,
                messageType: $messageType,
                payment: $payment,
                venue: $venue,
                content: $message,
                telegramMessageId: (string) data_get($sent, 'payload.result.message_id'),
                meta: array_merge($meta, $sent['payload'] ?? []),
            );
        }

        return $sent;
    }
}
