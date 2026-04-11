<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\BusinessSubscription;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $subscriptions = BusinessSubscription::query()
            ->with('plan', 'sourcePayment')
            ->where('venue_connection_id', $user?->venue_connection_id)
            ->latest('starts_at')
            ->paginate(10);

        return view('billing.subscriptions.index', [
            'currentSubscription' => $subscriptions->first(),
            'subscriptions' => $subscriptions,
        ]);
    }
}
