<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboard): View
    {
        return view('superadmin.dashboard', [
            'pageTitle' => 'Boshqaruv markazi',
            'pageSubtitle' => 'Platforma KPI, alertlar va operatsion nazorat bir joyda.',
            'dashboard' => $dashboard->overview(),
        ]);
    }
}
