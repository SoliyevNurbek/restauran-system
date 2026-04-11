<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function __invoke(): View
    {
        return view('superadmin.security.index', [
            'pageTitle' => 'Xavfsizlik',
            'pageSubtitle' => "So'nggi loginlar, muvaffaqiyatsiz urinishlar va sezgir hodisalar.",
            'recentLogins' => Schema::hasColumn('users', 'last_login_at')
                ? User::query()->whereNotNull('last_login_at')->latest('last_login_at')->take(10)->get()
                : collect(),
            'events' => Schema::hasTable('security_events')
                ? SecurityEvent::query()->with(['user', 'venueConnection'])->latest('occurred_at')->paginate(15)
                : new LengthAwarePaginator(collect(), 0, 15, 1, ['path' => request()->url(), 'query' => request()->query()]),
        ]);
    }
}
