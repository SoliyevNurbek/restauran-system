<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VenueConnection;
use App\Services\SuperAdmin\VenueAccessSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VenueConnectionController extends Controller
{
    public function index(): View
    {
        return view('superadmin.venues.index', [
            'pageTitle' => "Ulangan to'yxonalar",
            'venues' => VenueConnection::with(['approver', 'adminUser'])->latest()->paginate(12),
        ]);
    }

    public function update(Request $request, VenueConnection $venueConnection, VenueAccessSyncService $accessSync): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,under_review,approved,rejected,suspended'],
            'approval_notes' => ['nullable', 'string', 'max:1000'],
            'review_reason' => ['nullable', 'string', 'max:500'],
            'health_status' => ['nullable', 'string', 'max:50'],
            'halls_count' => ['nullable', 'integer', 'min:0'],
            'bookings_count' => ['nullable', 'integer', 'min:0'],
            'revenue_total' => ['nullable', 'numeric', 'min:0'],
        ]);

        $plainPassword = null;

        DB::transaction(function () use ($venueConnection, $data, &$plainPassword, $request, $accessSync) {
            $venueConnection->fill([
                'status' => $data['status'],
                'approval_notes' => $data['approval_notes'] ?? $venueConnection->approval_notes,
                'review_reason' => $data['review_reason'] ?? $venueConnection->review_reason,
                'health_status' => $data['health_status'] ?? $venueConnection->health_status,
                'halls_count' => $data['halls_count'] ?? $venueConnection->halls_count,
                'bookings_count' => $data['bookings_count'] ?? $venueConnection->bookings_count,
                'revenue_total' => $data['revenue_total'] ?? $venueConnection->revenue_total,
                'reviewed_at' => now(),
            ]);

            if ($data['status'] === 'approved') {
                $venueConnection->approved_by = $request->user()->id;
                $venueConnection->approved_at = now();
                $venueConnection->last_seen_at ??= now();
            }

            $venueConnection->save();
            $user = $accessSync->sync($venueConnection, $data['status']);

            if ($data['status'] === 'approved' && ! $user) {
                $plainPassword = Str::password(10, true, true, false, false);

                $user = new User([
                    'username' => $venueConnection->username,
                ]);

                $user->fill([
                    'name' => $venueConnection->owner_name,
                    'username' => $venueConnection->username,
                    'password' => Hash::make($plainPassword),
                    'role' => 'admin',
                    'status' => 'active',
                    'venue_connection_id' => $venueConnection->id,
                ]);
                $user->save();

                $venueConnection->admin_user_id = $user->id;
                $venueConnection->save();
            }
        });

        return back()->with('success', "To'yxona holati yangilandi.")
            ->with('generated_password', $plainPassword);
    }

    public function resetCredentials(VenueConnection $venueConnection): RedirectResponse
    {
        abort_unless($venueConnection->pendingUser, 404);

        $plainPassword = Str::password(10, true, true, false, false);
        $venueConnection->pendingUser->update([
            'password' => Hash::make($plainPassword),
            'status' => 'active',
        ]);

        return back()->with('success', 'Yangi kirish ma\'lumotlari yaratildi.')
            ->with('generated_password', $plainPassword);
    }
}
