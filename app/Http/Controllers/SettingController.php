<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Models\BusinessSubscription;
use App\Models\MediaAsset;
use App\Models\Setting;
use App\Services\Billing\TelegramLinkingService;
use App\Services\Billing\TelegramSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(TelegramLinkingService $telegramLinking, TelegramSettingsService $telegramSettings): View
    {
        $user = auth()->user();
        $activeSection = in_array(request('section'), ['business', 'notifications', 'integrations', 'security'], true)
            ? request('section')
            : 'business';
        $subscription = null;
        $venue = $user?->venueConnection;

        if ($user && ! $user->isSuperAdmin() && $user->venue_connection_id) {
            $subscription = BusinessSubscription::query()
                ->with('plan')
                ->where('venue_connection_id', $user->venue_connection_id)
                ->latest('starts_at')
                ->first();
        }

        if ($venue) {
            $venue = $telegramLinking->ensureLinkToken($venue);
        }

        return view('settings.edit', [
            'setting' => Setting::currentFor($user),
            'adminUser' => $user,
            'subscription' => $subscription,
            'activeSection' => $activeSection,
            'telegramVenue' => $venue,
            'telegramLink' => $venue ? $telegramLinking->deepLinkForVenue($venue) : null,
            'telegramMask' => $venue ? $telegramLinking->maskChatId($venue->telegram_chat_id) : 'Ulanmagan',
            'telegramConfigured' => $telegramSettings->enabled(),
            'telegramBotUsername' => $telegramSettings->all()['bot_username'] ?? null,
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $section = in_array($request->query('section'), ['business', 'notifications', 'integrations', 'security'], true)
            ? $request->query('section')
            : 'business';
        $setting = Setting::currentFor($request->user());
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $asset = MediaAsset::replace(
                key: 'brand_logo',
                file: $request->file('logo'),
                directory: 'branding/logo',
                userId: $request->user()?->getKey(),
                ownerUserId: $request->user()?->getKey(),
                label: 'Logo',
                altText: 'Brend logotipi',
            );

            $data['logo_path'] = null;
            $data['logo_media_file_id'] = $asset->media_file_id;
        }

        if ($request->hasFile('favicon')) {
            $asset = MediaAsset::replace(
                key: 'brand_favicon',
                file: $request->file('favicon'),
                directory: 'branding/favicon',
                userId: $request->user()?->getKey(),
                ownerUserId: $request->user()?->getKey(),
                label: 'Favicon',
                altText: 'Favicon',
            );

            $data['favicon_path'] = null;
            $data['favicon_media_file_id'] = $asset->media_file_id;
        }

        unset($data['logo']);
        unset($data['favicon']);

        $setting->update($data);
        Setting::forgetResolved($request->user()?->getKey());
        Log::channel('audit')->info('Settings updated.', [
            'user_id' => $request->user()?->getKey(),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('settings.edit', ['section' => $section])->with('success', 'Sozlamalar muvaffaqiyatli yangilandi.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $section = in_array($request->query('section'), ['business', 'notifications', 'integrations', 'security'], true)
            ? $request->query('section')
            : 'security';
        $data = $request->validated();

        $user = $request->user();
        $user->update([
            'password' => $data['password'],
        ]);

        Log::channel('audit')->warning('User password updated.', [
            'user_id' => $user?->getKey(),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('settings.edit', ['section' => $section])->with('success', 'Admin paroli muvaffaqiyatli yangilandi.');
    }
}
