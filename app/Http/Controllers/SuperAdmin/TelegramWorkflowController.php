<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\ReviewManualPaymentRequest;
use App\Http\Requests\SuperAdmin\UpdateTelegramWorkflowRequest;
use App\Models\SubscriptionPayment;
use App\Services\Billing\PaymentReviewService;
use App\Services\Billing\TelegramBotService;
use App\Services\Billing\TelegramSettingsService;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TelegramWorkflowController extends Controller
{
    public function edit(TelegramSettingsService $settings): View
    {
        return view('superadmin.telegram.edit', [
            'pageTitle' => 'Telegram workflow',
            'pageSubtitle' => 'Manual to\'lovlar, alertlar va bot orqali tenant aloqasini boshqarish.',
            'telegramWorkflow' => $settings->all(),
            'notificationTypes' => TelegramSettingsService::NOTIFICATION_TYPES,
        ]);
    }

    public function update(
        UpdateTelegramWorkflowRequest $request,
        TelegramSettingsService $settings,
        AuditLogService $audit,
    ): RedirectResponse {
        $settings->put($request->validated());

        $audit->record('telegram.workflow.updated', null, null, [
            'is_enabled' => $request->boolean('is_enabled'),
            'notifications' => $request->validated('notification_settings', []),
        ], 'warning', $request, 'Telegram workflow');

        return back()->with('success', 'Telegram workflow sozlamalari saqlandi.');
    }

    public function test(TelegramBotService $bot): RedirectResponse
    {
        $adminChatId = $bot->adminChatId();

        if (! $adminChatId) {
            return back()->with('error', 'Admin chat ID ko\'rsatilmagan.');
        }

        $result = $bot->sendMessage($adminChatId, implode("\n", [
            '🤖 <b>MyRestaurant_SN Telegram test</b>',
            '',
            'Manual payment workflow va alertlar uchun bot aloqasi tayyor.',
            'Vaqt: <b>'.now()->format('d.m.Y H:i').'</b>',
        ]));

        return back()->with(
            $result['ok'] ? 'success' : 'error',
            $result['ok'] ? 'Telegram test xabari yuborildi.' : 'Telegram test bajarilmadi: '.$result['message']
        );
    }

    public function review(
        ReviewManualPaymentRequest $request,
        SubscriptionPayment $payment,
        PaymentReviewService $reviewer,
    ): RedirectResponse {
        abort_unless($payment->provider === 'manual_telegram', 404);

        if ($request->validated('action') === 'approve') {
            $reviewer->approve($payment, $request->user(), $request->validated('internal_note'), $request);

            return back()->with('success', 'Manual to\'lov tasdiqlandi va obuna faollashtirildi.');
        }

        $reviewer->reject(
            $payment,
            $request->user(),
            $request->validated('rejection_reason'),
            $request->validated('internal_note'),
            $request,
        );

        return back()->with('success', 'Manual to\'lov rad etildi va foydalanuvchiga xabar yuborildi.');
    }
}
