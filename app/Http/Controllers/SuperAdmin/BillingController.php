<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdatePaymentMethodRequest;
use App\Http\Requests\SuperAdmin\UpdateSubscriptionPaymentRequest;
use App\Models\PaymentMethod;
use App\Models\SubscriptionPayment;
use App\Services\SuperAdmin\AdminNotificationService;
use App\Services\SuperAdmin\AuditLogService;
use App\Services\SuperAdmin\TelegramNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $method = (string) $request->query('method', '');
        $plan = (string) $request->query('plan', '');
        $business = trim((string) $request->string('business'));
        $provider = (string) $request->query('provider', '');
        $date = (string) $request->query('date', '');
        $search = trim((string) $request->string('q'));

        $payments = Schema::hasTable('subscription_payments')
            ? SubscriptionPayment::query()
                ->with(Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans')
                    ? ['subscription.plan', 'plan', 'venueConnection', 'paymentMethod', 'user', 'reviewer']
                    : ['venueConnection', 'paymentMethod', 'user'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($method !== '', fn ($query) => $query->where('payment_method_id', $method))
                ->when($plan !== '', fn ($query) => $query->where('subscription_plan_id', $plan))
                ->when($provider !== '', fn ($query) => $query->where('provider', $provider))
                ->when($date !== '', fn ($query) => $query->whereDate('created_at', $date))
                ->when($business !== '', function ($query) use ($business) {
                    $query->whereHas('venueConnection', fn ($venue) => $venue->where('venue_name', 'like', "%{$business}%"));
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('transaction_reference', 'like', "%{$search}%")
                            ->orWhere('invoice_number', 'like', "%{$search}%")
                            ->orWhere('notes', 'like', "%{$search}%")
                            ->orWhere('proof_note', 'like', "%{$search}%")
                            ->orWhere('rejection_reason', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString()
            : $this->emptyPaginator();

        return view('superadmin.payments.index', [
            'pageTitle' => "To'lovlar",
            'pageSubtitle' => 'Manual Telegram to‘lovlari, invoice va review jarayoni boshqaruvi.',
            'payments' => $payments,
            'methods' => Schema::hasTable('payment_methods') ? PaymentMethod::query()->orderBy('display_order')->get() : collect(),
            'plans' => Schema::hasTable('subscription_plans') ? \App\Models\SubscriptionPlan::query()->orderBy('display_order')->get() : collect(),
            'filters' => compact('status', 'method', 'search', 'plan', 'business', 'provider', 'date'),
            'totals' => [
                'paid' => Schema::hasTable('subscription_payments') ? (float) SubscriptionPayment::where('status', 'paid')->sum('amount') : 0,
                'pending' => Schema::hasTable('subscription_payments') ? (float) SubscriptionPayment::whereIn('status', ['pending', 'payment_details_sent', 'awaiting_proof', 'under_review'])->sum('amount') : 0,
                'failed_count' => Schema::hasTable('subscription_payments') ? SubscriptionPayment::whereIn('status', ['failed', 'rejected'])->count() : 0,
            ],
        ]);
    }

    public function show(SubscriptionPayment $payment): View
    {
        $payment->load(['subscription.plan', 'plan', 'venueConnection', 'paymentMethod', 'user', 'reviewer', 'telegramMessages']);

        return view('superadmin.payments.show', [
            'pageTitle' => $payment->invoice_number ?: "To'lov tafsiloti",
            'pageSubtitle' => "Manual Telegram billing record, proof va review tafsilotlari.",
            'paymentRecord' => $payment,
        ]);
    }

    public function update(
        UpdateSubscriptionPaymentRequest $request,
        SubscriptionPayment $payment,
        AuditLogService $audit,
        AdminNotificationService $notifications,
        TelegramNotificationService $telegram,
    ): RedirectResponse {
        $before = $payment->only(['payment_method_id', 'status', 'amount', 'currency', 'transaction_reference', 'invoice_number', 'paid_at', 'due_date', 'notes']);
        $payment->update($request->validated());
        $payment->loadMissing(['venueConnection', 'paymentMethod']);

        $audit->record('payment.updated', $payment, $before, $payment->only(array_keys($before)), $payment->status === 'failed' ? 'warning' : 'info', $request, $payment->invoice_number);

        $notificationType = match ($payment->status) {
            'paid' => 'payment_received',
            'failed' => 'payment_failed',
            default => 'manual_payment_submitted',
        };

        $notifications->create(
            type: $notificationType,
            title: $payment->status === 'paid' ? "To'lov qabul qilindi" : "To'lov holati yangilandi",
            description: ($payment->venueConnection?->venue_name ?? 'Biznes').' / '.$payment->status,
            status: $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning'),
            icon: 'credit-card',
            actionUrl: route('superadmin.payments.show', $payment),
            relatedType: $payment::class,
            relatedId: $payment->getKey(),
            sendTelegram: in_array($payment->status, ['paid', 'failed'], true),
            telegramMessage: $telegram->format(
                heading: 'MyRestaurant_SN',
                eventType: 'Payment',
                subject: $payment->venueConnection?->venue_name ?? 'Biznes',
                lines: [
                    'Holat' => $payment->status,
                    'Miqdor' => number_format((float) $payment->amount, 0, '.', ' ').' '.$payment->currency,
                    'Method' => $payment->paymentMethod?->label,
                    'Reference' => $payment->transaction_reference,
                ],
            ),
        );

        return back()->with('success', "To'lov yozuvi yangilandi.");
    }

    public function updateMethod(UpdatePaymentMethodRequest $request, PaymentMethod $method, AuditLogService $audit): RedirectResponse
    {
        $before = $method->only(['label', 'type', 'is_enabled', 'proof_required', 'display_order', 'notes']);
        $method->update([
            'label' => $request->validated('label'),
            'type' => $request->validated('type'),
            'is_enabled' => $request->boolean('is_enabled'),
            'proof_required' => $request->boolean('proof_required'),
            'display_order' => $request->validated('display_order'),
            'config' => ['placeholder' => $request->validated('config_placeholder')],
            'notes' => $request->validated('notes'),
        ]);

        $audit->record('payment_method.updated', $method, $before, $method->only(array_keys($before)), 'info', $request, $method->label);

        return back()->with('success', "To'lov usuli sozlamalari saqlandi.");
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, 12, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
