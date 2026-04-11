<?php

namespace App\Services;

class SystemNotificationService
{
    public function sendDigest(int $days = 3): array
    {
        return [
            'sent' => false,
            'reason' => 'Email funksiyasi o‘chirildi.',
            'bookings_count' => 0,
            'low_stock_count' => 0,
            'days' => $days,
        ];
    }
}
