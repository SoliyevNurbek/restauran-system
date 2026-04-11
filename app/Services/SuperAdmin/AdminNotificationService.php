<?php

namespace App\Services\SuperAdmin;

use App\Models\AdminNotification;
use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Schema;

class AdminNotificationService
{
    public function __construct(
        private readonly TelegramNotificationService $telegram,
    ) {
    }

    public function create(
        string $type,
        string $title,
        ?string $description = null,
        string $status = 'info',
        string $icon = 'bell',
        ?string $actionUrl = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
        array $meta = [],
        bool $sendTelegram = false,
        ?string $telegramMessage = null,
    ): AdminNotification {
        if (! Schema::hasTable('admin_notifications')) {
            return new AdminNotification([
                'type' => $type,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'icon' => $icon,
                'action_url' => $actionUrl,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'occurred_at' => now(),
                'meta' => $meta,
            ]);
        }

        $notification = AdminNotification::query()->create([
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'icon' => $icon,
            'action_url' => $actionUrl,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'occurred_at' => now(),
            'meta' => $meta,
        ]);

        if ($sendTelegram && $this->telegramEnabledFor($type) && $telegramMessage) {
            $this->telegram->send($telegramMessage);
        }

        return $notification;
    }

    public function telegramEnabledFor(string $type): bool
    {
        $newSettings = json_decode(IntegrationSetting::valueFor('telegram.notifications') ?: '{}', true);

        if (is_array($newSettings) && array_key_exists('superadmin_message', $newSettings)) {
            return (bool) ($newSettings['superadmin_message'] ?? false);
        }

        $raw = IntegrationSetting::valueFor('telegram.alerts');

        if (! $raw) {
            return false;
        }

        $alerts = json_decode($raw, true);

        return is_array($alerts) && in_array($type, $alerts, true);
    }
}
