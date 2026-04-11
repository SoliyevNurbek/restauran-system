<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\StartCheckoutRequest;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\Billing\ClickMerchantService;
use App\Services\Billing\ManualPaymentService;
use App\Services\Billing\PaymeMerchantService;
use App\Services\Billing\SubscriptionLifecycleService;
use App\Services\Billing\TelegramBotService;
use App\Services\Billing\TelegramLinkingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function store(
        StartCheckoutRequest $request,
        SubscriptionPlan $plan,
        SubscriptionLifecycleService $subscriptions,
        ManualPaymentService $manualPayments,
    ): RedirectResponse {
        $provider = $request->validated('provider');

        if ($provider === 'manual') {
            $payment = $manualPayments->createAndDispatch($request->user(), $plan);
            $venue = $request->user()?->venueConnection;
            $flashType = $venue?->telegram_chat_id ? 'success' : 'warning';
            $flashMessage = $venue?->telegram_chat_id
                ? 'Telegram orqali to\'lov yo\'riqnomasi tayyorlandi.'
                : 'Telegram ulanmagan. Botni ulab, keyin shu checkout oynasi orqali yo\'riqnomani oling.';

            return redirect()->route('billing.checkout.show', $payment)->with($flashType, $flashMessage);
        }

        $method = $provider === 'manual' ? 'bank_transfer' : $provider;
        $payment = $subscriptions->createPendingPayment($request->user(), $plan, $provider === 'test' ? 'manual' : $provider, $method);

        if ($provider === 'test' && config('billing.testing.enabled')) {
            $subscriptions->markPaymentPaid($payment, ['testing' => true], 'test-'.$payment->getKey(), 'test-'.$payment->getKey());

            return redirect()->route('billing.payments.index', ['highlight' => $payment->getKey()])
                ->with('success', 'Test to\'lovi muvaffaqiyatli yakunlandi.');
        }

        return redirect()->route('billing.checkout.show', $payment);
    }

    public function show(
        SubscriptionPayment $payment,
        ClickMerchantService $click,
        PaymeMerchantService $payme,
        TelegramBotService $bot,
        TelegramLinkingService $linking,
    ): View {
        abort_unless($payment->venue_connection_id === auth()->user()?->venue_connection_id, 403);

        $venue = auth()->user()?->venueConnection;

        $checkout = match ($payment->provider) {
            'click' => ['type' => 'form'] + $click->checkoutData($payment),
            'payme' => ['type' => 'redirect', 'url' => $payme->checkoutUrl($payment)],
            'manual_telegram' => [
                'type' => 'manual_telegram',
                'url' => null,
                'deep_link' => $bot->deepLink($payment->transaction_reference ?: (string) $payment->getKey()),
                'link_deep_link' => $venue ? $linking->deepLinkForVenue($venue) : null,
                'telegram_connected' => filled($venue?->telegram_chat_id),
            ],
            default => ['type' => 'manual', 'url' => null],
        };

        return view('billing.checkout.show', [
            'payment' => $payment->load(['plan', 'paymentMethod']),
            'checkout' => $checkout,
            'telegramVenue' => $venue,
        ]);
    }
}
