<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Mail\NotificationEmailConnectedMail;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'setting' => Setting::current(),
            'adminUser' => auth()->user(),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $setting = Setting::current();
        $previousNotificationEmail = trim((string) ($setting->notification_email ?? ''));
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }

            $data['favicon_path'] = $request->file('favicon')->store('branding/favicons', 'public');
        }

        unset($data['logo']);
        unset($data['favicon']);

        $setting->update($data);
        Log::channel('audit')->info('Settings updated.', [
            'user_id' => $request->user()?->getKey(),
            'ip' => $request->ip(),
            'notification_email_changed' => $previousNotificationEmail !== (string) ($setting->notification_email ?? ''),
        ]);

        $currentNotificationEmail = trim((string) ($setting->notification_email ?? ''));

        if ($currentNotificationEmail !== '') {
            if (! $this->canSendRealEmails()) {
                return redirect()
                    ->route('settings.edit')
                    ->with('error', "Sozlamalar saqlandi, lekin email hali haqiqiy manzilga yuborilmaydi. Hozirgi `MAIL_MAILER` qiymati `".config('mail.default')."`.");
            }

            try {
                Mail::to($currentNotificationEmail)->send(new NotificationEmailConnectedMail(
                    restaurantName: $setting->restaurant_name,
                    notificationEmail: $currentNotificationEmail,
                    contactPhone: $setting->contact_phone,
                ));
            } catch (Throwable $exception) {
                Log::channel('auth')->warning('Notification email confirmation failed.', [
                    'user_id' => $request->user()?->getKey(),
                    'ip' => $request->ip(),
                    'email' => $currentNotificationEmail,
                    'exception' => $exception->getMessage(),
                ]);

                return redirect()
                    ->route('settings.edit')
                    ->with('error', 'Email saqlandi, lekin xabar yuborilmadi. MAIL sozlamalarini tekshiring.');
            }
        }

        $message = $currentNotificationEmail !== ''
            ? ($currentNotificationEmail !== $previousNotificationEmail
                ? 'Sozlamalar saqlandi va notification emailga ulanish xabari yuborildi.'
                : 'Sozlamalar saqlandi va notification emailga xabar yuborildi.')
            : 'Sozlamalar muvaffaqiyatli yangilandi.';

        return redirect()->route('settings.edit')->with('success', $message);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = $request->user();
        $user->update([
            'password' => $data['password'],
        ]);

        Log::channel('audit')->warning('User password updated.', [
            'user_id' => $user?->getKey(),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('settings.edit')->with('success', 'Admin paroli muvaffaqiyatli yangilandi.');
    }

    private function canSendRealEmails(): bool
    {
        $mailer = (string) Config::get('mail.default');

        return ! in_array($mailer, ['log', 'array'], true);
    }
}
