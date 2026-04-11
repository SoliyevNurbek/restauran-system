<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\BusinessSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $currentSubscription = Schema::hasTable('business_subscriptions')
            ? BusinessSubscription::query()
                ->with('plan')
                ->where('venue_connection_id', $user?->venue_connection_id)
                ->latest('starts_at')
                ->first()
            : null;

        return view('billing.plans.index', [
            'plans' => Schema::hasTable('subscription_plans')
                ? SubscriptionPlan::query()
                    ->when(
                        Schema::hasColumn('subscription_plans', 'is_active'),
                        fn ($query) => $query->where('is_active', true)
                    )
                    ->orderBy('display_order')
                    ->get()
                : collect(),
            'currentSubscription' => $currentSubscription,
        ]);
    }
}
