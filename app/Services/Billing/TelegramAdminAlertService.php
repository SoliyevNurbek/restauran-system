<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use App\Models\VenueConnection;

class TelegramAdminAlertService
{
    public function __construct(
        private readonly TelegramBotService $bot,
        private readonly TelegramMessageLogService $logs,
    ) {
    }

    public function send(
        string $message,
        string $messageType = 'alert',
        ?SubscriptionPayment $payment = null,
        ?VenueConnection $venue = null,
        array $meta = [],
    ): array {
        $chatId = $this->bot->adminChatId();

        if (! $chatId) {
            return ['ok' => false, 'message' => 'Admin chat ID sozlanmagan.'];
        }

        $sent = $this->bot->sendMessage($chatId, $message);

        if ($sent['ok']) {
            $this->logs->log(
                direction: 'outgoing',
                chatId: $chatId,
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
