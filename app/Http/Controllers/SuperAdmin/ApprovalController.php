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

class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'pending');

        $relations = ['adminUser', 'approver'];

        if (Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans')) {
            $relations[] = 'latestSubscription.plan';
        }

        $approvals = VenueConnection::query()
            ->visibleToSuperadmin()
            ->with($relations)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if (! (Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans'))) {
            $approvals->getCollection()->transform(function (VenueConnection $approval) {
                $approval->setRelation('latestSubscription', null);

                return $approval;
            });
        }

        return view('superadmin.approvals.index', [
            'pageTitle' => 'Tasdiqlar',
            'pageSubtitle' => "Ro'yxatdan o'tgan bizneslarni moderation oqimi orqali boshqaring.",
            'approvals' => $approvals,
            'status' => $status,
        ]);
    }

    public function update(
        UpdateVenueReviewRequest $request,
        VenueConnection $approval,
        AuditLogService $audit,
        AdminNotificationService $notifications,
        TelegramNotificationService $telegram,
        VenueAccessSyncService $accessSync,
    ): RedirectResponse {
        abort_if($approval->is_system_workspace, 404);

        $before = $approval->only(['status', 'approval_notes', 'review_reason']);
        $approval->update([
            'status' => $request->validated('status'),
            'approval_notes' => $request->validated('approval_notes'),
            'review_reason' => $request->validated('review_reason'),
            'reviewed_at' => now(),
            'approved_at' => $request->validated('status') === 'approved' ? now() : $approval->approved_at,
            'approved_by' => $request->validated('status') === 'approved' ? $request->user()?->getKey() : $approval->approved_by,
        ]);

        $accessSync->sync($approval, $approval->status);

        $audit->record('approval.reviewed', $approval, $before, $approval->only(array_keys($before)), 'warning', $request, $approval->venue_name);

        $notificationType = match ($approval->status) {
            'approved' => 'business_approved',
            'rejected' => 'business_rejected',
            'suspended' => 'critical_superadmin_action',
            default => 'new_approval_request',
        };

        $notifications->create(
            type: $notificationType,
            title: 'Approval qarori qabul qilindi',
            description: $approval->venue_name.' / '.$approval->status,
            status: $approval->status === 'approved' ? 'success' : ($approval->status === 'rejected' ? 'danger' : 'warning'),
            icon: 'badge-check',
            actionUrl: route('superadmin.businesses.show', $approval),
            relatedType: $approval::class,
            relatedId: $approval->getKey(),
            sendTelegram: $request->boolean('send_telegram'),
            telegramMessage: $telegram->format(
                heading: 'MyRestaurant_SN',
                eventType: 'Approval workflow',
                subject: $approval->venue_name,
                lines: [
                    'Holat' => $approval->status,
                    'Owner' => $approval->owner_name,
                    'Sabab' => $approval->review_reason,
                    'Moderator' => $request->user()?->name,
                ],
            ),
        );

        return back()->with('success', 'Approval jarayoni yangilandi.');
    }
}
