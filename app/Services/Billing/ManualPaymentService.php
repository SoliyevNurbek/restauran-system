<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Str;

class ManualPaymentService
{
    public function __construct(
        private readonly SubscriptionLifecycleService $subscriptions,
        private readonly TelegramTenantMessageService $tenantMessages,
        private readonly TelegramWorkflowMessageBuilder $messages,
    ) {
    }

    public function createAndDispatch(User $user, SubscriptionPlan $plan): SubscriptionPayment
    {
        $payment = $this->subscriptions->createPendingPayment($user, $plan, 'manual_telegram', 'manual');
        $payment->update([
            'status' => 'pending',
            'transaction_reference' => $payment->transaction_reference ?: 'PAY-'.Str::upper(Str::random(8)),
        ]);

        $this->sendInstructionIfPossible($payment->fresh(['plan', 'venueConnection']));

        return $payment->fresh(['plan', 'venueConnection']);
    }

    public function sendInstructionIfPossible(SubscriptionPayment $payment): void
    {
        $venue = $payment->venueConnection;

        if (! $venue) {
            return;
        }

        $text = $this->messages->paymentInstruction($payment);
        $sent = $this->tenantMessages->sendToBusiness(
            venue: $venue,
            message: $text,
            messageType: 'payment_instruction',
            payment: $payment,
        );

        if ($sent['ok']) {
            $payment->update([
                'status' => 'payment_details_sent',
                'telegram_chat_id' => $venue->telegram_chat_id,
                'telegram_message_id' => (string) data_get($sent, 'payload.result.message_id'),
                'instruction_sent_at' => now(),
            ]);
        }
    }
}
