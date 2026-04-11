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

    $this->warn($result['reason']);

    return self::SUCCESS;
})->purpose('Email funksiyasi o‘chirilgan tizim eslatmasi komandasi.');

Schedule::command('notifications:send-system')->dailyAt('08:00');
