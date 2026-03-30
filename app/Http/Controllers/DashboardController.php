<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $stats = [
            'todayPurchases' => (float) Purchase::whereDate('purchase_date', $today)->sum('total_amount'),
            'monthPurchases' => (float) Purchase::whereBetween('purchase_date', [$monthStart->toDateString(), now()->toDateString()])->sum('total_amount'),
            'monthExpenses' => (float) Expense::whereBetween('expense_date', [$monthStart->toDateString(), now()->toDateString()])->sum('amount'),
            'lowStockCount' => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'productCount' => Product::count(),
            'supplierCount' => Supplier::count(),
        ];

        $purchaseRows = Purchase::query()
            ->selectRaw('DATE(purchase_date) as date_value, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', $today->copy()->subDays(6))
            ->groupBy(DB::raw('DATE(purchase_date)'))
            ->orderBy('date_value')
            ->get()
            ->keyBy('date_value');

        $expenseRows = Expense::query()
            ->selectRaw('DATE(expense_date) as date_value, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', $today->copy()->subDays(6))
            ->groupBy(DB::raw('DATE(expense_date)'))
            ->orderBy('date_value')
            ->get()
            ->keyBy('date_value');

        $labels = [];
        $purchaseValues = [];
        $expenseValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $date = $day->toDateString();
            $labels[] = $day->format('d M');
            $purchaseValues[] = isset($purchaseRows[$date]) ? (float) $purchaseRows[$date]->amount : 0;
            $expenseValues[] = isset($expenseRows[$date]) ? (float) $expenseRows[$date]->amount : 0;
        }

        $supplierDebt = Supplier::query()
            ->withSum('purchases', 'total_amount')
            ->withSum('payments', 'amount')
            ->get()
            ->sum->balance;

        return view('dashboard.index', [
            'stats' => $stats + ['supplierDebt' => $supplierDebt],
            'chartLabels' => $labels,
            'purchaseValues' => $purchaseValues,
            'expenseValues' => $expenseValues,
            'latestPurchases' => Purchase::with(['supplier', 'items.product'])->latest('purchase_date')->take(6)->get(),
            'latestExpenses' => Expense::with('category')->latest('expense_date')->take(6)->get(),
            'lowStockProducts' => Product::whereColumn('current_stock', '<=', 'minimum_stock')->orderBy('current_stock')->take(6)->get(),
            'topSuppliers' => Supplier::query()
                ->withSum('purchases', 'total_amount')
                ->withSum('payments', 'amount')
                ->orderByDesc('purchases_sum_total_amount')
                ->take(6)
                ->get(),
        ]);
    }
}
