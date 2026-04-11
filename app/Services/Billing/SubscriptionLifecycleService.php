<?php

namespace App\Services\Billing;

use App\Models\BusinessSubscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\VenueConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionLifecycleService
{
    public function createPendingPayment(
        User $user,
        SubscriptionPlan $plan,
        string $provider,
        string $method,
    ): SubscriptionPayment {
        $venue = $this->resolveVenue($user);
        $currentSubscription = $this->currentSubscription($venue);
        $paymentFor = $this->detectPaymentPurpose($currentSubscription, $plan);

        return DB::transaction(function () use ($user, $venue, $currentSubscription, $plan, $provider, $method, $paymentFor) {
            return SubscriptionPayment::query()->create([
                'business_subscription_id' => $currentSubscription?->getKey(),
                'subscription_plan_id' => $plan->getKey(),
                'venue_connection_id' => $venue->getKey(),
                'user_id' => $user->getKey(),
                'payment_method_id' => $this->paymentMethodId($provider, $method),
                'provider' => $provider,
                'method' => $method,
                'status' => 'pending',
                'amount' => $plan->amount,
                'currency' => $plan->currency ?: config('billing.currency', 'UZS'),
                'invoice_number' => $this->nextInvoiceNumber(),
                'payment_for' => $paymentFor,
                'description' => $plan->name.' tarifi uchun '.$paymentFor.' to‘lovi',
                'due_date' => now()->addDay(),
                'meta' => [
                    'plan_name' => $plan->name,
                    'duration_days' => $plan->duration_days ?: 30,
                ],
            ]);
        });
    }

    public function markPaymentPaid(
        SubscriptionPayment $payment,
        array $providerMeta = [],
        ?string $providerPaymentId = null,
        ?string $externalTransactionId = null,
    ): BusinessSubscription {
        return DB::transaction(function () use ($payment, $providerMeta, $providerPaymentId, $externalTransactionId) {
            $payment->refresh();

            if ($payment->status === 'paid' && $payment->subscription) {
                return $payment->subscription;
            }

            $payment->update([
                'status' => 'paid',
                'provider_payment_id' => $providerPaymentId ?: $payment->provider_payment_id,
                'external_transaction_id' => $externalTransactionId ?: $payment->external_transaction_id,
                'transaction_reference' => $externalTransactionId ?: $providerPaymentId ?: $payment->transaction_reference,
                'paid_at' => now(),
                'meta' => array_merge($payment->meta ?? [], $providerMeta),
            ]);

            $plan = $payment->plan ?: SubscriptionPlan::query()->findOrFail($payment->subscription_plan_id);
            $currentSubscription = BusinessSubscription::query()
                ->where('venue_connection_id', $payment->venue_connection_id)
                ->latest('starts_at')
                ->first();

            $subscription = $this->activateSubscription($payment, $plan, $currentSubscription);

            $payment->update([
                'business_subscription_id' => $subscription->getKey(),
            ]);

            return $subscription;
        });
    }

    public function markPaymentFailed(SubscriptionPayment $payment, string $status = 'failed', array $meta = []): void
    {
        $payment->update([
            'status' => $status,
            'meta' => array_merge($payment->meta ?? [], $meta),
        ]);
    }

    public function currentSubscription(VenueConnection $venue): ?BusinessSubscription
    {
        return BusinessSubscription::query()
            ->with('plan')
            ->where('venue_connection_id', $venue->getKey())
            ->latest('starts_at')
            ->first();
    }

    private function activateSubscription(
        SubscriptionPayment $payment,
        SubscriptionPlan $plan,
        ?BusinessSubscription $currentSubscription,
    ): BusinessSubscription {
        $startsAt = now();
        $endsAt = $startsAt->copy()->addDays(max((int) ($plan->duration_days ?: 30), 1));

        if ($currentSubscription && in_array($currentSubscription->status, ['active', 'trial'], true)) {
            if ((int) $currentSubscription->subscription_plan_id === (int) $plan->getKey() && $payment->payment_for !== 'upgrade') {
                $base = $currentSubscription->current_period_end && $currentSubscription->current_period_end->isFuture()
                    ? $currentSubscription->current_period_end->copy()
                    : now();

                $endsAt = $base->copy()->addDays(max((int) ($plan->duration_days ?: 30), 1));

                $currentSubscription->update([
                    'status' => 'active',
                    'activity_state' => 'healthy',
                    'amount' => $plan->amount,
                    'currency' => $plan->currency,
                    'renews_at' => $endsAt,
                    'expires_at' => $endsAt,
                    'trial_ends_at' => null,
                    'source_payment_id' => $payment->getKey(),
                ]);

                return $currentSubscription->fresh(['plan']);
            }

            $currentSubscription->update([
                'status' => 'canceled',
                'activity_state' => 'replaced',
                'canceled_at' => now(),
            ]);
        }

        return BusinessSubscription::query()->create([
            'venue_connection_id' => $payment->venue_connection_id,
            'user_id' => $payment->user_id,
            'source_payment_id' => $payment->getKey(),
            'subscription_plan_id' => $plan->getKey(),
            'status' => 'active',
            'activity_state' => 'healthy',
            'billing_cycle' => $plan->billing_cycle,
            'amount' => $plan->amount,
            'currency' => $plan->currency,
            'manual_override' => false,
            'auto_renew' => false,
            'starts_at' => $startsAt,
            'trial_ends_at' => null,
            'renews_at' => $endsAt,
            'expires_at' => $endsAt,
            'notes' => 'Activated from '.$payment->provider.' payment '.$payment->invoice_number,
        ])->load('plan');
    }

    private function detectPaymentPurpose(?BusinessSubscription $currentSubscription, SubscriptionPlan $plan): string
    {
        if (! $currentSubscription) {
            return 'subscription';
        }

        if ((int) $currentSubscription->subscription_plan_id !== (int) $plan->getKey()) {
            return 'upgrade';
        }

        return in_array($currentSubscription->status, ['active', 'trial'], true) ? 'renewal' : 'subscription';
    }

    private function resolveVenue(User $user): VenueConnection
    {
        return $user->venueConnection()->firstOrFail();
    }

    private function nextInvoiceNumber(): string
    {
        return 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    private function paymentMethodId(string $provider, string $method): ?int
    {
        return DB::table('payment_methods')
            ->where('code', $method ?: $provider)
            ->value('id');
    }
}
