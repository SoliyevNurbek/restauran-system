<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateBillingGatewayRequest;
use App\Http\Requests\SuperAdmin\UpdateTelegramIntegrationRequest;
use App\Models\IntegrationSetting;
use App\Services\SuperAdmin\AdminNotificationService;
use App\Services\SuperAdmin\AuditLogService;
use App\Services\Billing\BillingSettingsService;
use App\Services\SuperAdmin\TelegramNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    private const TELEGRAM_ALERT_TYPES = [
        'new_business_registration',
        'new_approval_request',
        'business_approved',
        'business_rejected',
        'subscription_created',
        'subscription_expired',
        'payment_received',
        'payment_failed',
        'manual_payment_submitted',
        'suspicious_login',
        'important_settings_change',
        'critical_superadmin_action',
    ];

    public function edit(): View
    {
        $alerts = json_decode(IntegrationSetting::valueFor('telegram.alerts') ?: '[]', true);
        $billing = app(BillingSettingsService::class);

        return view('superadmin.integrations.edit', [
            'pageTitle' => 'Integratsiyalar',
            'pageSubtitle' => 'Telegram bot, alert routing va secure config boshqaruvi.',
            'telegram' => [
                'configured' => app(TelegramNotificationService::class)->isConfigured(),
                'chat_id' => IntegrationSetting::valueFor('telegram.chat_id'),
                'alerts' => is_array($alerts) ? $alerts : [],
                'alert_types' => self::TELEGRAM_ALERT_TYPES,
            ],
            'billing' => [
                'click' => $billing->click(),
                'payme' => $billing->payme(),
            ],
        ]);
    }

    public function update(
        UpdateTelegramIntegrationRequest $request,
        AuditLogService $audit,
        AdminNotificationService $notifications,
    ): RedirectResponse {
        if (filled($request->validated('bot_token'))) {
            IntegrationSetting::putValue('telegram.bot_token', $request->validated('bot_token'), true);
        }

        IntegrationSetting::putValue('telegram.chat_id', $request->validated('chat_id'));
        IntegrationSetting::putValue('telegram.alerts', json_encode($request->validated('alerts', [])));

        $audit->record('integration.telegram.updated', null, null, [
            'chat_id' => $request->validated('chat_id'),
            'alerts_count' => count($request->validated('alerts', [])),
        ], 'warning', $request, 'Telegram');

        $notifications->create(
            type: 'important_settings_change',
            title: 'Telegram integratsiyasi yangilandi',
            description: 'Alert routing va recipient sozlamalari saqlandi.',
            status: 'info',
            icon: 'send',
        );

        return back()->with('success', 'Telegram integratsiyasi saqlandi.');
    }

    public function test(TelegramNotificationService $telegram): RedirectResponse
    {
        $result = $telegram->send(
            $telegram->format(
                heading: 'MyRestaurant_SN',
                eventType: 'Integration test',
                subject: 'Superadmin telegram',
                lines: ['Holat' => 'Test yuborildi'],
            ),
        );

        return back()->with($result['ok'] ? 'success' : 'error', $result['ok'] ? 'Telegram test xabari yuborildi.' : 'Telegram test bajarilmadi: '.$result['message']);
    }

    public function updateBilling(
        UpdateBillingGatewayRequest $request,
        AuditLogService $audit,
        AdminNotificationService $notifications,
    ): RedirectResponse {
        $map = [
            'billing.click.service_id' => [$request->validated('click_service_id'), false],
            'billing.click.merchant_id' => [$request->validated('click_merchant_id'), false],
            'billing.click.secret_key' => [$request->validated('click_secret_key'), true],
            'billing.click.merchant_user_id' => [$request->validated('click_merchant_user_id'), false],
            'billing.click.checkout_url' => [$request->validated('click_checkout_url'), false],
            'billing.payme.merchant_id' => [$request->validated('payme_merchant_id'), false],
            'billing.payme.secret_key' => [$request->validated('payme_secret_key'), true],
            'billing.payme.checkout_url' => [$request->validated('payme_checkout_url'), false],
            'billing.payme.account_key' => [$request->validated('payme_account_key'), false],
        ];

        foreach ($map as $key => [$value, $encrypted]) {
            if ($value !== null) {
                IntegrationSetting::putValue($key, $value, $encrypted);
            }
        }

        $audit->record('integration.billing.updated', null, null, [
            'providers' => ['click', 'payme'],
        ], 'warning', $request, 'Billing gateways');

        $notifications->create(
            type: 'important_settings_change',
            title: 'Billing gateway sozlamalari yangilandi',
            description: 'Click va Payme konfiguratsiyasi saqlandi.',
            status: 'info',
            icon: 'credit-card',
        );

        return back()->with('success', 'Billing gateway sozlamalari saqlandi.');
    }
}
