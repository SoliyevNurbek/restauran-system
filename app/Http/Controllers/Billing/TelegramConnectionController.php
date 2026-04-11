<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateTelegramConnectionRequest;
use App\Services\Billing\TelegramLinkingService;
use Illuminate\Http\RedirectResponse;

class TelegramConnectionController extends Controller
{
    public function update(UpdateTelegramConnectionRequest $request): RedirectResponse
    {
        $venue = $request->user()?->venueConnection;
        abort_unless($venue, 404);

        $venue->forceFill([
            'telegram_notifications_enabled' => $request->boolean('telegram_notifications_enabled', true),
        ])->save();

        return redirect()->route('settings.edit', ['section' => 'integrations'])
            ->with('success', 'Telegram bildirishnoma sozlamalari yangilandi.');
    }

    public function regenerate(UpdateTelegramConnectionRequest $request, TelegramLinkingService $linking): RedirectResponse
    {
        $venue = $request->user()?->venueConnection;
        abort_unless($venue, 404);

        $linking->regenerateLinkToken($venue);

        return redirect()->route('settings.edit', ['section' => 'integrations'])
            ->with('success', 'Telegram ulash havolasi yangilandi.');
    }
}
