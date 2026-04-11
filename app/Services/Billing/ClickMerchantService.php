<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ClickMerchantService
{
    public function __construct(
        private readonly BillingSettingsService $settings,
        private readonly SubscriptionLifecycleService $subscriptions,
    ) {
    }

    public function checkoutData(SubscriptionPayment $payment): array
    {
        $config = $this->settings->click();

        return [
            'url' => $config['checkout_url'],
            'method' => 'GET',
            'fields' => [
                'service_id' => $config['service_id'],
                'merchant_id' => $config['merchant_id'],
                'amount' => (float) $payment->amount,
                'transaction_param' => $payment->getKey(),
                'return_url' => route('billing.payments.index', ['highlight' => $payment->getKey()]),
            ],
        ];
    }

    public function handlePrepare(array $payload): array
    {
        $payment = $this->resolvePayment($payload);
        $config = $this->settings->click();

        if (! $this->validPrepareSignature($payload, (string) $config['service_id'], (string) $config['secret_key'])) {
            return ['error' => -1, 'error_note' => 'SIGN CHECK FAILED'];
        }

        if (! $payment || $payment->status !== 'pending') {
            return ['error' => -5, 'error_note' => 'PAYMENT NOT FOUND'];
        }

        return [
            'click_trans_id' => Arr::get($payload, 'click_trans_id'),
            'merchant_trans_id' => (string) $payment->getKey(),
            'merchant_prepare_id' => $payment->getKey(),
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    public function handleComplete(array $payload): array
    {
        $payment = $this->resolvePayment($payload);
        $config = $this->settings->click();

        if (! $this->validCompleteSignature($payload, (string) $config['service_id'], (string) $config['secret_key'])) {
            return ['error' => -1, 'error_note' => 'SIGN CHECK FAILED'];
        }

        if (! $payment) {
            return ['error' => -5, 'error_note' => 'PAYMENT NOT FOUND'];
        }

        if ((int) Arr::get($payload, 'error', 0) < 0) {
            $this->subscriptions->markPaymentFailed($payment, 'failed', ['click' => $payload]);

            return [
                'click_trans_id' => Arr::get($payload, 'click_trans_id'),
                'merchant_trans_id' => (string) $payment->getKey(),
                'merchant_confirm_id' => $payment->getKey(),
                'error' => -9,
                'error_note' => 'Payment failed',
            ];
        }

        $subscription = $this->subscriptions->markPaymentPaid(
            $payment,
            ['click' => $payload],
            (string) Arr::get($payload, 'click_paydoc_id'),
            (string) Arr::get($payload, 'click_trans_id'),
        );

        return [
            'click_trans_id' => Arr::get($payload, 'click_trans_id'),
            'merchant_trans_id' => (string) $payment->getKey(),
            'merchant_confirm_id' => $subscription->source_payment_id ?: $payment->getKey(),
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    private function resolvePayment(array $payload): ?SubscriptionPayment
    {
        return SubscriptionPayment::query()->find(Arr::get($payload, 'merchant_trans_id'));
    }

    private function validPrepareSignature(array $payload, string $serviceId, string $secretKey): bool
    {
        $expected = md5(
            Arr::get($payload, 'click_trans_id')
            .$serviceId
            .$secretKey
            .Arr::get($payload, 'merchant_trans_id')
            .Arr::get($payload, 'amount')
            .Arr::get($payload, 'action')
            .Arr::get($payload, 'sign_time')
        );

        return Str::lower($expected) === Str::lower((string) Arr::get($payload, 'sign_string'));
    }

    private function validCompleteSignature(array $payload, string $serviceId, string $secretKey): bool
    {
        $expected = md5(
            Arr::get($payload, 'click_trans_id')
            .$serviceId
            .$secretKey
            .Arr::get($payload, 'merchant_trans_id')
            .Arr::get($payload, 'merchant_prepare_id')
            .Arr::get($payload, 'amount')
            .Arr::get($payload, 'action')
            .Arr::get($payload, 'sign_time')
        );

        return Str::lower($expected) === Str::lower((string) Arr::get($payload, 'sign_string'));
    }
}
