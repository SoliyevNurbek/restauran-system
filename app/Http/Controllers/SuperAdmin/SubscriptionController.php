<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateSubscriptionRequest;
use App\Models\BusinessSubscription;
use App\Models\SubscriptionPlan;
use App\Services\SuperAdmin\AdminNotificationService;
use App\Services\SuperAdmin\AuditLogService;
use App\Services\SuperAdmin\TelegramNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $plan = (string) $request->query('plan', '');

        $subscriptions = Schema::hasTable('business_subscriptions')
            ? BusinessSubscription::query()
                ->with(Schema::hasTable('subscription_plans') ? ['venueConnection', 'plan', 'user'] : ['venueConnection', 'user'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($plan !== '', fn ($query) => $query->where('subscription_plan_id', $plan))
                ->latest()
                ->paginate(12)
                ->withQueryString()
            : $this->emptyPaginator();

        return view('superadmin.subscriptions.index', [
            'pageTitle' => 'Obunalar',
            'pageSubtitle' => 'Tariflar, renewal va manual override nazorati.',
            'subscriptions' => $subscriptions,
            'plans' => Schema::hasTable('subscription_plans') ? SubscriptionPlan::query()->orderBy('display_order')->get() : collect(),
            'filters' => compact('status', 'plan'),
        ]);
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, 12, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    public function update(
        UpdateSubscriptionRequest $request,
        BusinessSubscription $subscription,
        AuditLogService $audit,
        AdminNotificationService $notifications,
        TelegramNotificationService $telegram,
    ): RedirectResponse {
        $before = $subscription->only(['subscription_plan_id', 'status', 'activity_state', 'billing_cycle', 'amount', 'currency', 'manual_override', 'renews_at', 'expires_at', 'trial_ends_at', 'notes']);
        $subscription->update($request->validated());
        $subscription->loadMissing(['plan', 'venueConnection']);

        $audit->record('subscription.updated', $subscription, $before, $subscription->only(array_keys($before)), 'info', $request, $subscription->venueConnection?->venue_name);

        $notifications->create(
            type: $subscription->status === 'expired' ? 'subscription_expired' : 'subscription_created',
            title: 'Obuna holati yangilandi',
            description: optional($subscription->venueConnection)->venue_name.' / '.optional($subscription->plan)->name,
            status: in_array($subscription->status, ['active', 'trial'], true) ? 'success' : ($subscription->status === 'expired' ? 'danger' : 'warning'),
            icon: 'repeat',
            actionUrl: route('superadmin.subscriptions.index'),
            relatedType: $subscription::class,
            relatedId: $subscription->getKey(),
            sendTelegram: in_array($subscription->status, ['expired', 'active'], true),
            telegramMessage: $telegram->format(
                heading: 'MyRestaurant_SN',
                eventType: 'Subscription',
                subject: optional($subscription->venueConnection)->venue_name ?? 'Biznes',
                lines: [
                    'Tarif' => optional($subscription->plan)->name,
                    'Holat' => $subscription->status,
                    'Miqdor' => number_format((float) $subscription->amount, 0, '.', ' ').' '.$subscription->currency,
                    'Renewal' => optional($subscription->renews_at)?->format('d.m.Y'),
                ],
            ),
        );

        return back()->with('success', 'Obuna muvaffaqiyatli yangilandi.');
    }
}
