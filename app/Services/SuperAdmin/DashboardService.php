<?php

namespace App\Services\SuperAdmin;

use App\Models\AdminNotification;
use App\Models\BusinessSubscription;
use App\Models\PaymentMethod;
use App\Models\SecurityEvent;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\VenueConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    private function businesses()
    {
        return VenueConnection::query()->visibleToSuperadmin();
    }

    public function overview(): array
    {
        $payments = $this->hasTable('subscription_payments') ? SubscriptionPayment::query() : null;
        $successfulPayments = $payments ? (clone $payments)->where('status', 'paid')->count() : 0;
        $failedPayments = $payments ? (clone $payments)->where('status', 'failed')->count() : 0;
        $allPaymentAttempts = max($successfulPayments + $failedPayments, 1);

        $monthlyRevenue = $payments
            ? (clone $payments)
                ->where('status', 'paid')
                ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount')
            : 0;

        $subscriptionDistribution = $this->hasTable('subscription_plans') && $this->hasTable('business_subscriptions')
            ? SubscriptionPlan::query()
                ->withCount('subscriptions')
                ->orderBy('display_order')
                ->get()
            : collect();

        $recentRegistrations = $this->businesses()->latest()->take(6)->get();
        $recentPayments = $this->hasTable('subscription_payments')
            ? SubscriptionPayment::query()
                ->with($this->hasTable('business_subscriptions') && $this->hasTable('subscription_plans')
                    ? ['venueConnection', 'subscription.plan', 'paymentMethod']
                    : ['venueConnection', 'paymentMethod'])
                ->latest()
                ->take(6)
                ->get()
            : collect();

        $recentUsers = User::query()
            ->whereIn('role', ['admin', 'superadmin'])
            ->latest()
            ->take(6)
            ->get();

        return [
            'kpis' => [
                'total_businesses' => $this->businesses()->count(),
                'pending_approvals' => $this->businesses()->where('status', 'pending')->count(),
                'approved_businesses' => $this->businesses()->where('status', 'approved')->count(),
                'rejected_or_suspended' => $this->businesses()->whereIn('status', ['rejected', 'suspended'])->count(),
                'active_subscriptions' => $this->hasTable('business_subscriptions') ? BusinessSubscription::whereIn('status', ['active', 'trial'])->count() : 0,
                'expired_subscriptions' => $this->hasTable('business_subscriptions') ? BusinessSubscription::where('status', 'expired')->count() : 0,
                'monthly_revenue' => (float) $monthlyRevenue,
                'payment_success_rate' => round(($successfulPayments / $allPaymentAttempts) * 100, 1),
                'failed_payments' => $failedPayments,
            ],
            'trends' => [
                'businesses' => $this->monthSeries($this->businesses(), 'created_at'),
                'revenue' => $payments ? $this->monthSeries(SubscriptionPayment::query()->where('status', 'paid'), 'paid_at', 'amount') : $this->emptySeries(),
                'subscriptions' => $this->hasTable('business_subscriptions') ? $this->monthSeries(BusinessSubscription::query(), 'created_at') : $this->emptySeries(),
                'payment_failures' => $payments ? $this->monthSeries(SubscriptionPayment::query()->where('status', 'failed'), 'updated_at') : $this->emptySeries(),
                'approvals' => $this->approvalConversionSeries(),
            ],
            'subscription_distribution' => $subscriptionDistribution,
            'recent_registrations' => $recentRegistrations,
            'recent_payments' => $recentPayments,
            'recent_users' => $recentUsers,
            'recent_activity' => $this->recentActivity(),
            'alerts' => $this->hasTable('admin_notifications') ? AdminNotification::query()->latest('occurred_at')->take(5)->get() : collect(),
            'system_status' => [
                'payment_methods_enabled' => $this->hasTable('payment_methods') ? PaymentMethod::where('is_enabled', true)->count() : 0,
                'telegram_configured' => app(TelegramNotificationService::class)->isConfigured(),
                'security_events' => $this->hasTable('security_events') ? SecurityEvent::query()->where('occurred_at', '>=', now()->subDays(7))->count() : 0,
                'unread_notifications' => $this->hasTable('admin_notifications') ? AdminNotification::query()->where('is_read', false)->count() : 0,
            ],
        ];
    }

    public function analytics(): array
    {
        $topBusinesses = $this->businesses()
            ->with($this->hasTable('business_subscriptions') && $this->hasTable('subscription_plans') ? ['latestSubscription.plan'] : [])
            ->orderByDesc('revenue_total')
            ->take(5)
            ->get();

        $inactiveBusinesses = $this->businesses()
            ->where(function ($query) {
                $query->whereNull('last_seen_at')
                    ->orWhere('last_seen_at', '<', now()->subDays(14));
            })
            ->orderBy('last_seen_at')
            ->take(5)
            ->get();

        return [
            'revenue_trend' => $this->hasTable('subscription_payments') ? $this->monthSeries(SubscriptionPayment::query()->where('status', 'paid'), 'paid_at', 'amount') : $this->emptySeries(),
            'business_trend' => $this->monthSeries($this->businesses(), 'created_at'),
            'subscription_trend' => $this->hasTable('business_subscriptions') ? $this->monthSeries(BusinessSubscription::query()->whereIn('status', ['active', 'trial']), 'created_at') : $this->emptySeries(),
            'failure_trend' => $this->hasTable('subscription_payments') ? $this->monthSeries(SubscriptionPayment::query()->where('status', 'failed'), 'updated_at') : $this->emptySeries(),
            'approval_conversion' => $this->approvalConversionSeries(),
            'top_businesses' => $topBusinesses,
            'inactive_businesses' => $inactiveBusinesses,
        ];
    }

    private function recentActivity(): Collection
    {
        return collect([
            ...($this->hasTable('subscription_payments')
                ? SubscriptionPayment::query()->latest()->take(4)->get()->map(fn ($payment) => [
                    'title' => "To'lov yangilandi",
                    'description' => ($payment->invoice_number ?: 'Invoice').' / '.$payment->status,
                    'time' => $payment->updated_at,
                    'icon' => 'wallet',
                    'status' => $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning'),
                ])->all()
                : []),
            ...$this->businesses()->latest()->take(4)->get()->map(fn ($venue) => [
                'title' => 'Biznes hodisasi',
                'description' => $venue->venue_name.' / '.$venue->status,
                'time' => $venue->updated_at,
                'icon' => 'building-2',
                'status' => $venue->status === 'approved' ? 'success' : ($venue->status === 'rejected' ? 'danger' : 'warning'),
            ])->all(),
        ])->sortByDesc('time')->take(8)->values();
    }

    private function monthSeries($query, string $column, ?string $sumColumn = null): array
    {
        $months = collect(range(5, 0))->reverse()->map(fn ($offset) => now()->subMonths($offset)->startOfMonth());

        return $months->map(function ($month) use ($query, $column, $sumColumn) {
            $builder = clone $query;
            $builder->whereBetween($column, [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()]);

            return [
                'label' => $month->format('M'),
                'value' => $sumColumn ? (float) $builder->sum($sumColumn) : $builder->count(),
            ];
        })->all();
    }

    private function approvalConversionSeries(): array
    {
        return collect(range(5, 0))->reverse()->map(function ($offset) {
            $month = now()->subMonths($offset);
            $pending = $this->businesses()
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();

            $approved = $this->businesses()
                ->where('status', 'approved')
                ->whereMonth('updated_at', $month->month)
                ->whereYear('updated_at', $month->year)
                ->count();

            return [
                'label' => $month->format('M'),
                'value' => $pending > 0 ? round(($approved / $pending) * 100, 1) : 0,
            ];
        })->all();
    }

    private function emptySeries(): array
    {
        return collect(range(5, 0))->reverse()->map(fn ($offset) => [
            'label' => now()->subMonths($offset)->format('M'),
            'value' => 0,
        ])->all();
    }

    private function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }
}
