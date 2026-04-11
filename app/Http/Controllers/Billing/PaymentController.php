<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $highlight = (string) $request->query('highlight', '');

        return view('billing.payments.index', [
            'payments' => SubscriptionPayment::query()
                ->with(['plan', 'paymentMethod', 'subscription'])
                ->where('venue_connection_id', $request->user()?->venue_connection_id)
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'filters' => compact('status', 'highlight'),
        ]);
    }
}
