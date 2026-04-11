<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateVenueReviewRequest;
use App\Models\VenueConnection;
use App\Services\SuperAdmin\AdminNotificationService;
use App\Services\SuperAdmin\AuditLogService;
use App\Services\SuperAdmin\TelegramNotificationService;
use App\Services\SuperAdmin\VenueAccessSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = (string) $request->query('status', '');

        $relations = ['approver', 'adminUser'];

        if (Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans')) {
            $relations[] = 'latestSubscription.plan';
        }

        $businesses = VenueConnection::query()
            ->visibleToSuperadmin()
            ->with($relations)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('venue_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if (! (Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans'))) {
            $businesses->getCollection()->transform(function (VenueConnection $business) {
                $business->setRelation('latestSubscription', null);

                return $business;
            });
        }

        return view('superadmin.businesses.index', [
            'pageTitle' => 'Bizneslar',
            'pageSubtitle' => 'Platformadagi barcha venue va tenantlar ustidan nazorat.',
            'businesses' => $businesses,
            'filters' => compact('search', 'status'),
        ]);
    }

    public function show(VenueConnection $business): View
    {
        abort_if($business->is_system_workspace, 404);

        $relations = ['approver', 'adminUser'];

        if (Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans')) {
            $relations[] = 'latestSubscription.plan';
            $relations[] = 'subscriptions.plan';
        }

        if (Schema::hasTable('subscription_payments')) {
            $relations[] = 'subscriptionPayments.paymentMethod';
        }

        if (Schema::hasTable('security_events')) {
            $relations[] = 'securityEvents';
        }

        $business->load($relations);

        if (! (Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans'))) {
            $business->setRelation('latestSubscription', null);
            $business->setRelation('subscriptions', collect());
        }

        if (! Schema::hasTable('subscription_payments')) {
            $business->setRelation('subscriptionPayments', collect());
        }

        if (! Schema::hasTable('security_events')) {
            $business->setRelation('securityEvents', collect());
        }

        return view('superadmin.businesses.show', [
            'pageTitle' => $business->venue_name,
            'pageSubtitle' => 'Biznes overview, billing va audit tafsilotlari.',
            'business' => $business,
        ]);
    }

    public function update(
        UpdateVenueReviewRequest $request,
        VenueConnection $business,
        AuditLogService $audit,
        AdminNotificationService $notifications,
        TelegramNotificationService $telegram,
        VenueAccessSyncService $accessSync,
    ): RedirectResponse {
        abort_if($business->is_system_workspace, 404);

        $before = $business->only(['status', 'approval_notes', 'review_reason', 'health_status', 'halls_count', 'bookings_count', 'revenue_total']);
        $data = $request->validated();

        $business->fill([
            'status' => $data['status'],
            'approval_notes' => $data['approval_notes'] ?? $business->approval_notes,
            'review_reason' => $data['review_reason'] ?? $business->review_reason,
            'health_status' => $data['health_status'] ?? $business->health_status,
            'halls_count' => $data['halls_count'] ?? $business->halls_count,
            'bookings_count' => $data['bookings_count'] ?? $business->bookings_count,
            'revenue_total' => $data['revenue_total'] ?? $business->revenue_total,
            'reviewed_at' => now(),
            'approved_at' => $data['status'] === 'approved' ? now() : $business->approved_at,
            'approved_by' => $data['status'] === 'approved' ? $request->user()?->getKey() : $business->approved_by,
        ])->save();

        $accessSync->sync($business, $data['status']);

        $audit->record('business.status.updated', $business, $before, $business->only(array_keys($before)), $data['status'] === 'approved' ? 'info' : 'warning', $request, $business->venue_name);

        $notificationType = match ($data['status']) {
            'approved' => 'business_approved',
            'rejected' => 'business_rejected',
            'suspended' => 'critical_superadmin_action',
            default => 'new_approval_request',
        };

        $notifications->create(
            type: $notificationType,
            title: 'Biznes holati yangilandi',
            description: $business->venue_name.' / '.$data['status'],
            status: in_array($data['status'], ['approved'], true) ? 'success' : (in_array($data['status'], ['rejected', 'suspended'], true) ? 'danger' : 'warning'),
            icon: 'building-2',
            actionUrl: route('superadmin.businesses.show', $business),
            relatedType: $business::class,
            relatedId: $business->getKey(),
            sendTelegram: $request->boolean('send_telegram'),
            telegramMessage: $telegram->format(
                heading: 'MyRestaurant_SN',
                eventType: 'Business status',
                subject: $business->venue_name,
                lines: [
                    'Holat' => $data['status'],
                    'Ega' => $business->owner_name,
                    'Admin action' => $request->user()?->name,
                    'Izoh' => $data['review_reason'] ?? $data['approval_notes'] ?? null,
                ],
            ),
        );

        return back()->with('success', "Biznes ma'lumotlari yangilandi.");
    }
}
