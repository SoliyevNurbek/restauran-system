<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class PaymeMerchantService
{
    private const STATE_CREATED = 1;
    private const STATE_COMPLETED = 2;
    private const STATE_CANCELED = -1;
    private const ERROR_INVALID_ACCOUNT = -31050;
    private const ERROR_CANNOT_DO_OPERATION = -31008;
    private const ERROR_TRANSACTION_NOT_FOUND = -31003;
    private const ERROR_METHOD_NOT_FOUND = -32601;
    private const ERROR_AUTH = -32504;

    public function __construct(
        private readonly BillingSettingsService $settings,
        private readonly SubscriptionLifecycleService $subscriptions,
    ) {
    }

    public function handle(Request $request): array
    {
        $this->authorize($request);

        $method = (string) $request->input('method');
        $params = (array) $request->input('params', []);
        $id = $request->input('id');

        return match ($method) {
            'CheckPerformTransaction' => $this->success($id, $this->checkPerformTransaction($params)),
            'CreateTransaction' => $this->success($id, $this->createTransaction($params)),
            'PerformTransaction' => $this->success($id, $this->performTransaction($params)),
            'CancelTransaction' => $this->success($id, $this->cancelTransaction($params)),
            'CheckTransaction' => $this->success($id, $this->checkTransaction($params)),
            'GetStatement' => $this->success($id, $this->getStatement($params)),
            default => $this->error($id, self::ERROR_METHOD_NOT_FOUND, 'Method not found'),
        };
    }

    public function checkoutUrl(SubscriptionPayment $payment): string
    {
        $config = $this->settings->payme();
        $payload = base64_encode(json_encode([
            'm' => $config['merchant_id'],
            'ac.'.$config['account_key'] => $payment->getKey(),
            'a' => (int) round(((float) $payment->amount) * 100),
            'l' => 'uz',
            'c' => route('billing.payments.index', ['highlight' => $payment->getKey()]),
        ], JSON_UNESCAPED_SLASHES));

        return rtrim((string) $config['checkout_url'], '/').'/'.$payload;
    }

    private function authorize(Request $request): void
    {
        $expected = 'Basic '.base64_encode('Paycom:'.$this->settings->payme()['secret_key']);

        if ($request->header('Authorization') !== $expected) {
            throw ValidationException::withMessages([
                'authorization' => 'Unauthorized',
            ]);
        }
    }

    private function checkPerformTransaction(array $params): array
    {
        $payment = $this->findPaymentFromAccount($params);

        if (! $payment || (int) round(((float) $payment->amount) * 100) !== (int) Arr::get($params, 'amount')) {
            return [
                'allow' => false,
                'reason' => self::ERROR_INVALID_ACCOUNT,
            ];
        }

        return ['allow' => true];
    }

    private function createTransaction(array $params): array
    {
        $payment = $this->findPaymentFromAccount($params);

        if (! $payment || $payment->status !== 'pending') {
            throw $this->merchantException(self::ERROR_INVALID_ACCOUNT, 'Payment not found');
        }

        $createdAt = Carbon::createFromTimestampMs((int) Arr::get($params, 'time'));

        $payment->update([
            'provider_payment_id' => (string) Arr::get($params, 'id'),
            'external_transaction_id' => (string) Arr::get($params, 'id'),
            'transaction_reference' => (string) Arr::get($params, 'id'),
            'meta' => array_merge($payment->meta ?? [], ['payme_create' => $params, 'created_at_ms' => Arr::get($params, 'time')]),
            'created_at' => $payment->created_at ?? $createdAt,
        ]);

        return [
            'create_time' => $createdAt->valueOf(),
            'transaction' => (string) $payment->getKey(),
            'state' => self::STATE_CREATED,
        ];
    }

    private function performTransaction(array $params): array
    {
        $payment = SubscriptionPayment::query()
            ->where('provider_payment_id', (string) Arr::get($params, 'id'))
            ->orWhere('external_transaction_id', (string) Arr::get($params, 'id'))
            ->first();

        if (! $payment) {
            throw $this->merchantException(self::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found');
        }

        $subscription = $this->subscriptions->markPaymentPaid(
            $payment,
            ['payme_perform' => $params],
            (string) Arr::get($params, 'id'),
            (string) Arr::get($params, 'id'),
        );

        return [
            'transaction' => (string) $payment->getKey(),
            'perform_time' => now()->valueOf(),
            'state' => self::STATE_COMPLETED,
            'subscription_id' => $subscription->getKey(),
        ];
    }

    private function cancelTransaction(array $params): array
    {
        $payment = SubscriptionPayment::query()
            ->where('provider_payment_id', (string) Arr::get($params, 'id'))
            ->orWhere('external_transaction_id', (string) Arr::get($params, 'id'))
            ->first();

        if (! $payment) {
            throw $this->merchantException(self::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found');
        }

        $status = $payment->status === 'paid' ? 'refunded' : 'canceled';
        $this->subscriptions->markPaymentFailed($payment, $status, ['payme_cancel' => $params]);

        return [
            'transaction' => (string) $payment->getKey(),
            'cancel_time' => now()->valueOf(),
            'state' => self::STATE_CANCELED,
        ];
    }

    private function checkTransaction(array $params): array
    {
        $payment = SubscriptionPayment::query()
            ->where('provider_payment_id', (string) Arr::get($params, 'id'))
            ->orWhere('external_transaction_id', (string) Arr::get($params, 'id'))
            ->first();

        if (! $payment) {
            throw $this->merchantException(self::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found');
        }

        $state = match ($payment->status) {
            'paid' => self::STATE_COMPLETED,
            'canceled', 'failed', 'refunded' => self::STATE_CANCELED,
            default => self::STATE_CREATED,
        };

        return [
            'create_time' => optional($payment->created_at)->valueOf(),
            'perform_time' => optional($payment->paid_at)->valueOf(),
            'cancel_time' => in_array($payment->status, ['canceled', 'failed', 'refunded'], true) ? optional($payment->updated_at)->valueOf() : 0,
            'transaction' => (string) $payment->getKey(),
            'state' => $state,
            'reason' => null,
        ];
    }

    private function getStatement(array $params): array
    {
        $from = Carbon::createFromTimestampMs((int) Arr::get($params, 'from'));
        $to = Carbon::createFromTimestampMs((int) Arr::get($params, 'to'));

        $transactions = SubscriptionPayment::query()
            ->where('provider', 'payme')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(fn (SubscriptionPayment $payment) => [
                'id' => $payment->provider_payment_id ?: $payment->external_transaction_id,
                'time' => optional($payment->created_at)->valueOf(),
                'amount' => (int) round(((float) $payment->amount) * 100),
                'account' => [
                    $this->settings->payme()['account_key'] => (string) $payment->getKey(),
                ],
                'create_time' => optional($payment->created_at)->valueOf(),
                'perform_time' => optional($payment->paid_at)->valueOf(),
                'cancel_time' => in_array($payment->status, ['canceled', 'failed', 'refunded'], true) ? optional($payment->updated_at)->valueOf() : 0,
                'transaction' => (string) $payment->getKey(),
                'state' => match ($payment->status) {
                    'paid' => self::STATE_COMPLETED,
                    'canceled', 'failed', 'refunded' => self::STATE_CANCELED,
                    default => self::STATE_CREATED,
                },
                'reason' => null,
            ])
            ->values()
            ->all();

        return ['transactions' => $transactions];
    }

    private function findPaymentFromAccount(array $params): ?SubscriptionPayment
    {
        $accountKey = $this->settings->payme()['account_key'];
        $paymentId = Arr::get($params, 'account.'.$accountKey);

        return SubscriptionPayment::query()->find($paymentId);
    }

    private function success($id, array $result): array
    {
        return ['result' => $result, 'id' => $id];
    }

    private function error($id, int $code, string $message): array
    {
        return [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'id' => $id,
        ];
    }

    private function merchantException(int $code, string $message): ValidationException
    {
        return ValidationException::withMessages([
            'merchant' => json_encode(['code' => $code, 'message' => $message]),
        ]);
    }
}
