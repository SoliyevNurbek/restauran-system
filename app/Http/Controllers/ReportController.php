<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $monthlyRows = Order::query()
            ->selectRaw('DATE(created_at) as sale_date, SUM(total) as amount')
            ->where('status', 'paid')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('sale_date')
            ->get();

        $dailyRows = Order::query()
            ->selectRaw('DATE(created_at) as sale_date, SUM(total) as amount')
            ->where('status', 'paid')
            ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('sale_date')
            ->get();

        return view('reports.index', [
            'monthlyLabels' => $monthlyRows->pluck('sale_date')->map(fn ($d) => Carbon::parse($d)->format('M d'))->values(),
            'monthlyValues' => $monthlyRows->pluck('amount')->map(fn ($v) => (float) $v)->values(),
            'dailyLabels' => $dailyRows->pluck('sale_date')->map(fn ($d) => Carbon::parse($d)->format('M d'))->values(),
            'dailyValues' => $dailyRows->pluck('amount')->map(fn ($v) => (float) $v)->values(),
            'totalPaidRevenue' => (float) Order::where('status', 'paid')->sum('total'),
            'paidOrderCount' => Order::where('status', 'paid')->count(),
        ]);
    }
}
