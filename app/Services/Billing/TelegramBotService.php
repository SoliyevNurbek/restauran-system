<?php

namespace App\Services\Billing;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TelegramBotService
{
    public function __construct(
        private readonly TelegramSettingsService $settings,
    ) {
    }

    public function configured(): bool
    {
        return $this->settings->enabled();
    }

    public function sendMessage(string $chatId, string $text): array
    {
        $settings = $this->settings->all();

        if (! $this->configured()) {
            return ['ok' => false, 'message' => 'Telegram bot sozlanmagan.'];
        }

        try {
            $response = Http::timeout(15)
                ->asForm()
                ->post($this->baseUrl($settings['bot_token']).'/sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            return [
                'ok' => $response->successful(),
                'message' => $response->successful() ? 'Telegram xabar yuborildi.' : 'Telegram API xatolik qaytardi.',
                'payload' => $response->json(),
            ];
        } catch (\Throwable $exception) {
            return ['ok' => false, 'message' => $exception->getMessage()];
        }
    }

    public function downloadPhoto(string $fileId, string $reference): ?string
    {
        $settings = $this->settings->all();

        if (! $this->configured()) {
            return null;
        }

        try {
            $fileResponse = Http::timeout(15)->get($this->baseUrl($settings['bot_token']).'/getFile', [
                'file_id' => $fileId,
            ]);

            if (! $fileResponse->successful()) {
                return null;
            }

            $filePath = data_get($fileResponse->json(), 'result.file_path');
            if (! $filePath) {
                return null;
            }

            $binary = Http::timeout(20)
                ->get("https://api.telegram.org/file/bot{$settings['bot_token']}/{$filePath}")
                ->body();

            $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
            $storagePath = 'telegram/payment-proofs/'.Str::slug($reference).'-'.Str::random(10).'.'.$extension;
            Storage::disk('local')->put('private/'.$storagePath, $binary);

            return $storagePath;
        } catch (\Throwable) {
            return null;
        }
    }

    public function deepLink(string $reference): ?string
    {
        return $this->startLink('pay_'.$reference);
    }

    public function startLink(string $payload): ?string
    {
        $username = $this->settings->all()['bot_username'];

        return $username ? 'https://t.me/'.$username.'?start='.$payload : null;
    }

    public function adminChatId(): ?string
    {
        return $this->settings->all()['admin_chat_id'];
    }

    private function baseUrl(string $token): string
    {
        return "https://api.telegram.org/bot{$token}";
    }
}
