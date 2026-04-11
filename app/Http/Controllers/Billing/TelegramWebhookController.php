<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Services\Billing\TelegramAdminAlertService;
use App\Services\Billing\TelegramBotService;
use App\Services\Billing\TelegramLinkingService;
use App\Services\Billing\TelegramMessageLogService;
use App\Services\Billing\TelegramSettingsService;
use App\Services\Billing\TelegramTenantMessageService;
use App\Services\Billing\TelegramWorkflowMessageBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramSettingsService $settings,
        private readonly TelegramBotService $bot,
        private readonly TelegramLinkingService $linking,
        private readonly TelegramTenantMessageService $tenantMessages,
        private readonly TelegramAdminAlertService $adminAlerts,
        private readonly TelegramMessageLogService $logs,
        private readonly TelegramWorkflowMessageBuilder $messages,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->settings->enabled()) {
            return response()->json(['ok' => true]);
        }

        $message = (array) ($request->input('message') ?: $request->input('edited_message') ?: []);
        if (! $message) {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) data_get($message, 'chat.id');
        $text = trim((string) data_get($message, 'text', data_get($message, 'caption', '')));
        $telegramMessageId = (string) data_get($message, 'message_id');
        $fromId = (string) data_get($message, 'from.id');
        $username = data_get($message, 'from.username');

        if (Str::startsWith($text, '/start ')) {
            $payload = Str::after($text, '/start ');
            $this->handleStart($payload, $chatId, $fromId, $username, $telegramMessageId);

            return response()->json(['ok' => true]);
        }

        if (data_get($message, 'photo')) {
            $this->handleProof($message, $chatId, $text, $telegramMessageId);

            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => true]);
    }

    private function handleStart(string $payload, string $chatId, string $fromId, ?string $username, string $telegramMessageId): void
    {
        if (Str::startsWith($payload, 'pay_')) {
            $reference = Str::after($payload, 'pay_');
            $this->handlePaymentStart($reference, $chatId, $fromId, $username, $telegramMessageId);

            return;
        }

        $venue = $this->linking->linkByToken($payload, $chatId, $username, $fromId);

        if (! $venue) {
            $this->bot->sendMessage($chatId, $this->messages->linkFailed());

            return;
        }

        $this->logs->log(
            direction: 'incoming',
            chatId: $chatId,
            messageType: 'system',
            venue: $venue,
            content: '/start '.$payload,
            telegramMessageId: $telegramMessageId,
            meta: ['from_id' => $fromId, 'username' => $username],
        );

        $this->tenantMessages->sendToBusiness($venue, $this->messages->linked($venue), 'system');
    }

    private function handlePaymentStart(string $reference, string $chatId, string $fromId, ?string $username, string $telegramMessageId): void
    {
        $payment = SubscriptionPayment::query()
            ->with(['venueConnection', 'plan'])
            ->where('transaction_reference', $reference)
            ->whereIn('status', ['pending', 'payment_details_sent', 'awaiting_proof'])
            ->latest()
            ->first();

        if (! $payment) {
            $this->bot->sendMessage($chatId, "To'lov ref kodi topilmadi. Iltimos, paneldagi havola orqali qayta kiring.");

            return;
        }

        if ($payment->venueConnection && $payment->venueConnection->telegram_chat_id && $payment->venueConnection->telegram_chat_id !== $chatId) {
            $this->bot->sendMessage($chatId, 'Bu to\'lov boshqa Telegram chatga biriktirilgan.');

            return;
        }

        if ($payment->venueConnection && $payment->venueConnection->newQuery()->where('telegram_chat_id', $chatId)->whereKeyNot($payment->venueConnection->getKey())->exists()) {
            $this->bot->sendMessage($chatId, 'Ushbu chat boshqa biznesga biriktirilgan. Tenant paneldan yangi link oling.');

            return;
        }

        DB::transaction(function () use ($payment, $chatId, $fromId, $username) {
            $payment->venueConnection?->forceFill([
                'telegram_chat_id' => $chatId,
                'telegram_user_id' => $fromId,
                'telegram_username' => $username,
                'telegram_linked_at' => now(),
                'telegram_verified_at' => now(),
            ])->save();

            $payment->update([
                'telegram_chat_id' => $chatId,
                'status' => 'payment_details_sent',
            ]);
        });

        $this->logs->log(
            direction: 'incoming',
            chatId: $chatId,
            messageType: 'system',
            payment: $payment,
            venue: $payment->venueConnection,
            content: '/start pay_'.$reference,
            telegramMessageId: $telegramMessageId,
            meta: ['from_id' => $fromId, 'username' => $username],
        );

        if ($payment->venueConnection) {
            $sent = $this->tenantMessages->sendToBusiness(
                $payment->venueConnection,
                $this->messages->paymentInstruction($payment),
                'payment_instruction',
                $payment,
            );

            if ($sent['ok']) {
                $payment->update([
                    'telegram_message_id' => (string) data_get($sent, 'payload.result.message_id'),
                    'instruction_sent_at' => now(),
                ]);
            }
        }
    }

    private function handleProof(array $message, string $chatId, string $caption, string $telegramMessageId): void
    {
        $reference = $this->extractReference($caption);

        $payment = SubscriptionPayment::query()
            ->with(['venueConnection', 'plan'])
            ->when($reference, fn ($query) => $query->where('transaction_reference', $reference))
            ->whereHas('venueConnection', fn ($query) => $query->where('telegram_chat_id', $chatId))
            ->whereIn('status', ['pending', 'payment_details_sent', 'awaiting_proof'])
            ->latest()
            ->first();

        if (! $payment) {
            $this->bot->sendMessage($chatId, "Aktiv to'lov topilmadi. Iltimos, paneldagi to'lov havolasidan qayta boshlang yoki ref kodini yuboring.");

            return;
        }

        $largestPhoto = collect((array) data_get($message, 'photo'))->last();
        $fileId = data_get($largestPhoto, 'file_id');
        $filePath = $fileId ? $this->bot->downloadPhoto($fileId, $payment->transaction_reference ?: (string) $payment->getKey()) : null;

        if (! $filePath) {
            $this->bot->sendMessage($chatId, "Chek rasmini qabul qilib bo'lmadi. Iltimos, qayta yuboring.");

            return;
        }

        $payment->update([
            'status' => 'under_review',
            'proof_file_path' => $filePath,
            'proof_note' => $caption ?: null,
            'proof_received_at' => now(),
            'telegram_chat_id' => $chatId,
            'telegram_message_id' => $telegramMessageId,
        ]);

        $this->logs->log(
            direction: 'incoming',
            chatId: $chatId,
            messageType: 'receipt',
            payment: $payment,
            venue: $payment->venueConnection,
            content: $caption ?: null,
            filePath: $filePath,
            telegramMessageId: $telegramMessageId,
            meta: ['file_id' => $fileId],
        );

        if ($payment->venueConnection) {
            $this->tenantMessages->sendToBusiness($payment->venueConnection, $this->messages->proofReceived($payment), 'system', $payment);
        }

        $this->adminAlerts->send(
            $this->messages->superadminReviewAlert($payment),
            'alert',
            $payment,
            $payment->venueConnection,
        );
    }

    private function extractReference(string $text): ?string
    {
        if (preg_match('/([A-Z]{3,10}-[A-Z0-9]{4,})/u', Str::upper($text), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
