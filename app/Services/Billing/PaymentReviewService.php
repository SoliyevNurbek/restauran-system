<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Services\SuperAdmin\AdminNotificationService;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentReviewService
{
    public function __construct(
        private readonly SubscriptionLifecycleService $subscriptions,
        private readonly TelegramTenantMessageService $tenantMessages,
        private readonly TelegramWorkflowMessageBuilder $messages,
        private readonly AuditLogService $audit,
        private readonly AdminNotificationService $notifications,
    ) {
    }

    public function approve(SubscriptionPayment $payment, User $reviewer, ?string $internalNote, Request $request): SubscriptionPayment
    {
        return DB::transaction(function () use ($payment, $reviewer, $internalNote, $request) {
            $locked = SubscriptionPayment::query()->whereKey($payment->getKey())->lockForUpdate()->firstOrFail();

            if ($locked->status === 'paid') {
                return $locked->load(['plan', 'venueConnection', 'subscription']);
            }

            $locked->update([
                'status' => 'under_review',
                'internal_note' => $internalNote,
            ]);

            $this->subscriptions->markPaymentPaid(
                $locked,
                ['manual_review' => ['reviewed_by' => $reviewer->getKey(), 'internal_note' => $internalNote]],
                $locked->provider_payment_id ?: 'manual-'.$locked->getKey(),
                $locked->external_transaction_id ?: 'manual-'.$locked->getKey(),
            );

            $locked->update([
                'verified_by' => $reviewer->getKey(),
                'verified_at' => now(),
                'internal_note' => $internalNote,
            ]);

            $this->audit->record('manual_payment.approved', $locked, null, [
                'status' => 'paid',
                'verified_by' => $reviewer->getKey(),
            ], 'info', $request, $locked->invoice_number);

            $this->notifications->create(
                type: 'payment_received',
                title: 'Manual to\'lov tasdiqlandi',
                description: ($locked->venueConnection?->venue_name ?? 'Biznes').' / '.$locked->invoice_number,
                status: 'success',
                icon: 'credit-card',
                actionUrl: route('superadmin.payments.show', $locked),
                relatedType: $locked::class,
                relatedId: $locked->getKey(),
            );

            if ($locked->venueConnection) {
                $this->tenantMessages->sendToBusiness(
                    $locked->venueConnection,
                    $this->messages->approval($locked->fresh(['plan', 'subscription'])),
                    'alert',
                    $locked,
                );
            }

            return $locked->fresh(['plan', 'venueConnection', 'subscription', 'reviewer']);
        });
    }

    public function reject(SubscriptionPayment $payment, User $reviewer, string $reason, ?string $internalNote, Request $request): SubscriptionPayment
    {
        return DB::transaction(function () use ($payment, $reviewer, $reason, $internalNote, $request) {
            $locked = SubscriptionPayment::query()->whereKey($payment->getKey())->lockForUpdate()->firstOrFail();

            if ($locked->status === 'paid') {
                return $locked->load(['plan', 'venueConnection']);
            }

            $locked->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_by' => $reviewer->getKey(),
                'verified_at' => now(),
                'internal_note' => $internalNote,
            ]);

            $this->audit->record('manual_payment.rejected', $locked, null, [
                'status' => 'rejected',
                'reason' => $reason,
            ], 'warning', $request, $locked->invoice_number);

            $this->notifications->create(
                type: 'payment_failed',
                title: 'Manual to\'lov rad etildi',
                description: ($locked->venueConnection?->venue_name ?? 'Biznes').' / '.$reason,
                status: 'danger',
                icon: 'shield-alert',
                actionUrl: route('superadmin.payments.show', $locked),
                relatedType: $locked::class,
                relatedId: $locked->getKey(),
            );

            if ($locked->venueConnection) {
                $this->tenantMessages->sendToBusiness(
                    $locked->venueConnection,
                    $this->messages->rejection($locked->fresh('plan'), $reason),
                    'alert',
                    $locked,
                );
            }

            return $locked->fresh(['plan', 'venueConnection', 'reviewer']);
        });
    }
}
