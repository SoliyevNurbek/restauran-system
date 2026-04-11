<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Models\MediaAsset;
use App\Models\Setting;
use App\Services\SuperAdmin\AdminNotificationService;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('superadmin.settings.edit', [
            'pageTitle' => 'Sozlamalar',
            'setting' => Setting::global(),
            'mediaCards' => [
                [
                    'key' => 'brand_logo',
                    'label' => 'Logo',
                    'description' => 'Landing, login va panellarda chiqadigan asosiy ikonka.',
                    'accept' => 'image/png,image/jpeg,image/webp,image/svg+xml',
                ],
                [
                    'key' => 'brand_favicon',
                    'label' => 'Favicon',
                    'description' => 'Brauzer tabidagi kichik ikonka.',
                    'accept' => 'image/png,image/jpeg,image/webp,image/x-icon,image/vnd.microsoft.icon',
                ],
                [
                    'key' => 'landing_preview_dashboard',
                    'label' => 'Landing dashboard rasmi',
                    'description' => 'Landing preview blokidagi 1-rasm.',
                    'accept' => 'image/png,image/jpeg,image/webp',
                ],
                [
                    'key' => 'landing_preview_admin',
                    'label' => 'Landing admin rasmi',
                    'description' => 'Landing preview blokidagi 2-rasm.',
                    'accept' => 'image/png,image/jpeg,image/webp',
                ],
                [
                    'key' => 'landing_preview_analytics',
                    'label' => 'Landing analytics rasmi',
                    'description' => 'Landing preview blokidagi 3-rasm.',
                    'accept' => 'image/png,image/jpeg,image/webp',
                ],
            ],
        ]);
    }

    public function update(
        UpdateSettingsRequest $request,
        AuditLogService $audit,
        AdminNotificationService $notifications,
    ): RedirectResponse
    {
        $setting = Setting::global();
        $before = $setting->only(['restaurant_name', 'contact_phone']);
        $data = $request->safe()->only([
            'restaurant_name',
            'contact_phone',
        ]);

        $setting->update($data);

        $uploads = [
            'brand_logo' => ['directory' => 'branding/logo', 'label' => 'Logo', 'alt' => 'Brend logotipi'],
            'brand_favicon' => ['directory' => 'branding/favicon', 'label' => 'Favicon', 'alt' => 'Favicon'],
            'landing_preview_dashboard' => ['directory' => 'landing', 'label' => 'Landing dashboard rasmi', 'alt' => 'Landing dashboard rasmi'],
            'landing_preview_admin' => ['directory' => 'landing', 'label' => 'Landing admin rasmi', 'alt' => 'Landing admin rasmi'],
            'landing_preview_analytics' => ['directory' => 'landing', 'label' => 'Landing analytics rasmi', 'alt' => 'Landing analytics rasmi'],
        ];

        foreach ($uploads as $field => $config) {
            if ($request->hasFile($field)) {
                $asset = MediaAsset::replace(
                    key: $field,
                    file: $request->file($field),
                    directory: $config['directory'],
                    userId: $request->user()?->getKey(),
                    ownerUserId: null,
                    label: $config['label'],
                    altText: $config['alt'],
                );

                if ($field === 'brand_logo') {
                    $setting->logo_path = null;
                    $setting->logo_media_file_id = $asset->media_file_id;
                }

                if ($field === 'brand_favicon') {
                    $setting->favicon_path = null;
                    $setting->favicon_media_file_id = $asset->media_file_id;
                }
            }
        }

        $setting->save();
        Setting::forgetResolved();
        MediaAsset::forgetKeyed();

        Log::channel('audit')->info('Superadmin settings updated.', [
            'user_id' => $request->user()?->getKey(),
            'ip' => $request->ip(),
        ]);

        $audit->record('superadmin.settings.updated', $setting, $before, $setting->only(['restaurant_name', 'contact_phone']), 'warning', $request, 'Global settings');
        $notifications->create(
            type: 'important_settings_change',
            title: 'Global sozlamalar yangilandi',
            description: "Brending yoki kontakt ma'lumotlari o'zgartirildi.",
            status: 'info',
            icon: 'settings-2',
        );

        return redirect()->route('superadmin.settings.edit')->with('success', 'Sozlamalar saqlandi.');
    }

    public function updatePassword(
        UpdatePasswordRequest $request,
        AuditLogService $audit,
        AdminNotificationService $notifications,
    ): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        Log::channel('audit')->warning('Superadmin password updated.', [
            'user_id' => $request->user()?->getKey(),
            'ip' => $request->ip(),
        ]);

        $audit->record('superadmin.password.updated', $request->user(), null, ['password' => 'updated'], 'warning', $request, $request->user()?->name);
        $notifications->create(
            type: 'important_settings_change',
            title: 'Superadmin paroli yangilandi',
            description: 'Hisob xavfsizlik parametrlari yangilandi.',
            status: 'warning',
            icon: 'shield-check',
        );

        return redirect()->route('superadmin.settings.edit')->with('success', 'Superadmin paroli yangilandi.');
    }
}
