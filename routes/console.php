<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\SystemNotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('notifications:send-system {--days=3}', function (SystemNotificationService $service) {
    $days = (int) $this->option('days');
    $result = $service->sendDigest($days);

    if (! $result['sent']) {
        $this->warn($result['reason']);

        return self::SUCCESS;
    }

    $this->info('Notification email yuborildi: '.$result['email']);
    $this->line('Yaqin bronlar: '.$result['bookings_count']);
    $this->line('Kam qolgan mahsulotlar: '.$result['low_stock_count']);

    return self::SUCCESS;
})->purpose('Yaqin bronlar va kam qolgan mahsulotlar bo`yicha notification email yuboradi.');

Schedule::command('notifications:send-system')->dailyAt('08:00');
