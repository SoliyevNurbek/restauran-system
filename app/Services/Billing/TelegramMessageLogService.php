<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use App\Models\TelegramMessage;
use App\Models\VenueConnection;

class TelegramMessageLogService
{
    public function log(
        string $direction,
        string $chatId,
        string $messageType,
        ?SubscriptionPayment $payment = null,
        ?VenueConnection $venue = null,
        ?string $content = null,
        ?string $filePath = null,
        ?string $telegramMessageId = null,
        array $meta = [],
    ): TelegramMessage {
        return TelegramMessage::query()->create([
            'subscription_payment_id' => $payment?->getKey(),
            'venue_connection_id' => $venue?->getKey() ?? $payment?->venue_connection_id,
            'direction' => $direction,
            'chat_id' => $chatId,
            'telegram_message_id' => $telegramMessageId,
            'message_type' => $messageType,
            'content' => $content,
            'file_path' => $filePath,
            'meta' => $meta,
        ]);
    }
}
