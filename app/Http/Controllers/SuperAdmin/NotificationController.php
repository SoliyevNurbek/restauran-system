<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $type = (string) $request->query('type', '');

        $notifications = Schema::hasTable('admin_notifications')
            ? AdminNotification::query()
                ->when($type !== '', fn ($query) => $query->where('type', $type))
                ->latest('occurred_at')
                ->paginate(15)
                ->withQueryString()
            : $this->emptyPaginator();

        return view('superadmin.notifications.index', [
            'pageTitle' => 'Bildirishnomalar',
            'pageSubtitle' => 'In-app alertlar, event stream va tezkor aksiyalar markazi.',
            'notifications' => $notifications,
            'type' => $type,
            'types' => Schema::hasTable('admin_notifications') ? AdminNotification::query()->select('type')->distinct()->pluck('type') : collect(),
        ]);
    }

    public function update(Request $request, AdminNotification $notification): RedirectResponse
    {
        $notification->update(['is_read' => $request->boolean('is_read', true)]);

        return back()->with('success', 'Bildirishnoma holati yangilandi.');
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, 15, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
