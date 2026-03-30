<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $stats = [
            'totalOrders' => Order::count(),
            'totalRevenue' => (float) Order::where('status', 'paid')->sum('total'),
            'activeTables' => DiningTable::where('status', 'occupied')->count(),
            'dailySales' => (float) Order::whereDate('created_at', $today)->sum('total'),
        ];

        $salesRows = Order::query()
            ->selectRaw('DATE(created_at) as sale_date, SUM(total) as amount')
            ->whereDate('created_at', '>=', $today->copy()->subDays(6))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $date = $day->toDateString();
            $labels[] = $day->format('M d');
            $values[] = isset($salesRows[$date]) ? (float) $salesRows[$date]->amount : 0;
        }

        $recentOrders = Order::with(['customer', 'diningTable'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', [
            'stats' => $stats,
            'chartLabels' => $labels,
            'chartValues' => $values,
            'recentOrders' => $recentOrders,
        ]);
    }
}
