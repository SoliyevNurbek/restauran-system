<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\DashboardService;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __invoke(DashboardService $dashboard): View
    {
        return view('superadmin.analytics.index', [
            'pageTitle' => 'Analitika',
            'pageSubtitle' => "Platforma revenue, approval conversion va tenant faolligi kesimidagi ko'rsatkichlar.",
            'analytics' => $dashboard->analytics(),
        ]);
    }
}
