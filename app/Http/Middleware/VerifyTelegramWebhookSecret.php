<?php

namespace App\Http\Middleware;

use App\Services\Billing\TelegramSettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTelegramWebhookSecret
{
    public function __construct(
        private readonly TelegramSettingsService $settings,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $configuredSecret = $this->settings->all()['webhook_secret'] ?: config('security.telegram.webhook_secret');

        if ($configuredSecret) {
            $providedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');

            abort_unless(hash_equals((string) $configuredSecret, $providedSecret), 403);
        }

        return $next($request);
    }
}
