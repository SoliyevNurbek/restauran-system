<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpsertSubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('superadmin.plans.index', [
            'pageTitle' => 'Tariflar',
            'pageSubtitle' => 'Basic, Pro, Premium va boshqa rejalarning narx, muddat va feature boshqaruvi.',
            'plans' => SubscriptionPlan::query()->orderBy('display_order')->get(),
        ]);
    }

    public function store(UpsertSubscriptionPlanRequest $request, AuditLogService $audit): RedirectResponse
    {
        $plan = SubscriptionPlan::query()->create($this->payload($request));
        $audit->record('subscription_plan.created', $plan, null, $plan->toArray(), 'info', $request, $plan->name);

        return back()->with('success', 'Tarif yaratildi.');
    }

    public function update(UpsertSubscriptionPlanRequest $request, SubscriptionPlan $plan, AuditLogService $audit): RedirectResponse
    {
        $before = $plan->toArray();
        $plan->update($this->payload($request));
        $audit->record('subscription_plan.updated', $plan, $before, $plan->fresh()->toArray(), 'info', $request, $plan->name);

        return back()->with('success', 'Tarif yangilandi.');
    }

    private function payload(UpsertSubscriptionPlanRequest $request): array
    {
        return [
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug'),
            'description' => $request->validated('description'),
            'amount' => $request->validated('amount'),
            'currency' => $request->validated('currency'),
            'duration_days' => $request->validated('duration_days'),
            'billing_cycle' => $request->validated('billing_cycle'),
            'status' => $request->boolean('is_active') ? 'active' : 'inactive',
            'is_active' => $request->boolean('is_active'),
            'display_order' => $request->validated('display_order'),
            'features' => $request->validated('features', []),
        ];
    }
}
