<?php

namespace App\Services\Billing;

use App\Models\SubscriptionPayment;
use App\Models\VenueConnection;

class TelegramWorkflowMessageBuilder
{
    public function __construct(
        private readonly TelegramSettingsService $settings,
    ) {
    }

    public function paymentInstruction(SubscriptionPayment $payment): string
    {
        $settings = $this->settings->all();
        $reference = $payment->transaction_reference ?: $payment->invoice_number ?: (string) $payment->getKey();

        return implode("\n", [
            '💳 <b>Obuna to\'lovi uchun ma\'lumot</b>',
            '',
            'Tarif: <b>'.e((string) $payment->plan?->name).'</b>',
            'Summa: <b>'.number_format((float) $payment->amount, 0, '.', ' ')." {$payment->currency}</b>",
            'Karta: <b>'.e((string) $settings['payment_card_number']).'</b>',
            'Qabul qiluvchi: <b>'.e((string) $settings['payment_card_holder']).'</b>',
            'Ref kod: <b>'.e((string) $reference).'</b>',
            '',
            'To\'lovni amalga oshirgach, shu botga chek yoki screenshot yuboring.',
            "Caption ichida <b>{$reference}</b> kodini qoldiring.",
            '',
            'To\'lov tasdiqlangandan so\'ng obunangiz faollashadi.',
        ]);
    }

    public function proofReceived(SubscriptionPayment $payment): string
    {
        return implode("\n", [
            '📨 <b>To\'lov cheki qabul qilindi</b>',
            '',
            'Ref: <b>'.e((string) $payment->transaction_reference).'</b>',
            'Chekingiz ko\'rib chiqish uchun yuborildi.',
            'Tasdiqlangach obuna holati avtomatik yangilanadi.',
        ]);
    }

    public function approval(SubscriptionPayment $payment): string
    {
        $expiry = $payment->subscription?->expires_at ?? $payment->subscription?->renews_at;

        return implode("\n", [
            '✅ <b>To\'lov tasdiqlandi</b>',
            '',
            'Tarif: <b>'.e((string) $payment->plan?->name).'</b>',
            'Holat: <b>Active</b>',
            'Tugash sanasi: <b>'.optional($expiry)->format('d M Y').'</b>',
        ]);
    }

    public function rejection(SubscriptionPayment $payment, string $reason): string
    {
        return implode("\n", [
            '❌ <b>To\'lov rad etildi</b>',
            '',
            'Sabab: <b>'.e($reason).'</b>',
            'Iltimos, chekni qayta yuboring.',
            'Ref: <b>'.e((string) $payment->transaction_reference).'</b>',
        ]);
    }

    public function superadminReviewAlert(SubscriptionPayment $payment): string
    {
        return implode("\n", [
            '🔔 <b>Yangi to\'lov cheki keldi</b>',
            '',
            'Biznes: <b>'.e((string) $payment->venueConnection?->venue_name).'</b>',
            'Tarif: <b>'.e((string) $payment->plan?->name).'</b>',
            'Holat: <b>Under review</b>',
            'Summa: <b>'.number_format((float) $payment->amount, 0, '.', ' ')." {$payment->currency}</b>",
            'Ref: <b>'.e((string) $payment->transaction_reference).'</b>',
        ]);
    }

    public function linked(VenueConnection $venue): string
    {
        return implode("\n", [
            '✅ <b>Telegram muvaffaqiyatli ulandi</b>',
            '',
            'Biznes: <b>'.e((string) $venue->venue_name).'</b>',
            'Billing va muhim eslatmalar shu chatga yuboriladi.',
        ]);
    }

    public function linkFailed(): string
    {
        return "Ulash tokeni topilmadi yoki bu chat boshqa biznesga biriktirilgan. Tenant paneldan yangi havola oling.";
    }
}
