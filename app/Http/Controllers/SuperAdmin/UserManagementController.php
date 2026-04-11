<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateUserManagementRequest;
use App\Models\User;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with($this->subscriptionRelationsAvailable() ? ['venueConnection.latestSubscription.plan'] : ['venueConnection'])
            ->latest()
            ->paginate(15);

        if (! $this->subscriptionRelationsAvailable()) {
            $users->getCollection()->transform(function (User $user) {
                if ($user->relationLoaded('venueConnection') && $user->venueConnection) {
                    $user->venueConnection->setRelation('latestSubscription', null);
                }

                return $user;
            });
        }

        return view('superadmin.users.index', [
            'pageTitle' => 'Foydalanuvchilar',
            'pageSubtitle' => 'Platforma userlari, tenant adminlar va superadmin rollari nazorati.',
            'users' => $users,
        ]);
    }

    public function show(User $user): View
    {
        $relations = $this->subscriptionRelationsAvailable() ? ['venueConnection.latestSubscription.plan'] : ['venueConnection'];

        if (Schema::hasTable('security_events')) {
            $relations[] = 'securityEvents';
        }

        if (Schema::hasTable('audit_logs')) {
            $relations[] = 'auditLogs';
        }

        $user->load($relations);

        if (! $this->subscriptionRelationsAvailable() && $user->relationLoaded('venueConnection') && $user->venueConnection) {
            $user->venueConnection->setRelation('latestSubscription', null);
        }

        if (! Schema::hasTable('security_events')) {
            $user->setRelation('securityEvents', collect());
        }

        if (! Schema::hasTable('audit_logs')) {
            $user->setRelation('auditLogs', collect());
        }

        return view('superadmin.users.show', [
            'pageTitle' => $user->name,
            'pageSubtitle' => 'Profil, audit va kirish xavfsizligi tafsilotlari.',
            'managedUser' => $user,
        ]);
    }

    public function update(UpdateUserManagementRequest $request, User $user, AuditLogService $audit): RedirectResponse
    {
        $before = $user->only(['status', 'role']);
        $user->fill($request->safe()->only(['status', 'role']));

        $temporaryPassword = null;
        if ($request->boolean('reset_password')) {
            $temporaryPassword = Str::password(10, true, true, false, false);
            $user->password = Hash::make($temporaryPassword);
        }

        $user->save();

        $audit->record('user.updated', $user, $before, $user->only(['status', 'role']), 'warning', $request, $user->name);

        return back()
            ->with('success', 'Foydalanuvchi holati yangilandi.')
            ->with('generated_password', $temporaryPassword);
    }

    private function subscriptionRelationsAvailable(): bool
    {
        return Schema::hasTable('business_subscriptions') && Schema::hasTable('subscription_plans');
    }
}
