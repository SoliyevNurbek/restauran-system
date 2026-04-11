<?php

namespace App\Services\Billing;

use App\Models\VenueConnection;
use Illuminate\Support\Str;

class TelegramLinkingService
{
    public function __construct(
        private readonly TelegramBotService $bot,
    ) {
    }

    public function ensureLinkToken(VenueConnection $venue): VenueConnection
    {
        if ($venue->telegram_link_token) {
            return $venue;
        }

        $venue->forceFill([
            'telegram_link_token' => $this->generateToken(),
        ])->save();

        return $venue->fresh();
    }

    public function regenerateLinkToken(VenueConnection $venue): VenueConnection
    {
        $venue->forceFill([
            'telegram_link_token' => $this->generateToken(),
        ])->save();

        return $venue->fresh();
    }

    public function deepLinkForVenue(VenueConnection $venue): ?string
    {
        $venue = $this->ensureLinkToken($venue);

        return $this->bot->startLink($venue->telegram_link_token);
    }

    public function linkByToken(string $token, string $chatId, ?string $username = null, ?string $telegramUserId = null): ?VenueConnection
    {
        $venue = VenueConnection::query()
            ->where('telegram_link_token', $token)
            ->where('is_system_workspace', false)
            ->first();

        if (! $venue) {
            return null;
        }

        $occupied = VenueConnection::query()
            ->where('telegram_chat_id', $chatId)
            ->whereKeyNot($venue->getKey())
            ->exists();

        if ($occupied) {
            return null;
        }

        $venue->forceFill([
            'telegram_chat_id' => $chatId,
            'telegram_user_id' => $telegramUserId,
            'telegram_username' => $username,
            'telegram_linked_at' => now(),
            'telegram_verified_at' => now(),
        ])->save();

        return $venue->fresh();
    }

    public function maskChatId(?string $chatId): string
    {
        if (! $chatId) {
            return 'Ulanmagan';
        }

        if (mb_strlen($chatId) <= 4) {
            return str_repeat('*', mb_strlen($chatId));
        }

        return str_repeat('*', max(mb_strlen($chatId) - 4, 0)).mb_substr($chatId, -4);
    }

    private function generateToken(): string
    {
        return Str::lower(Str::random(32));
    }
}
