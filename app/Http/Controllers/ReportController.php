<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $monthlyPurchases = Purchase::query()
            ->selectRaw('DATE_FORMAT(purchase_date, "%Y-%m") as ym, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', now()->copy()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $monthlyExpenses = Expense::query()
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as ym, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', now()->copy()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $months = collect(range(5, 0, -1))
            ->map(fn ($offset) => now()->copy()->subMonths($offset))
            ->push(now())
            ->map(fn (Carbon $date) => $date->format('Y-m'));

        $purchaseMap = $monthlyPurchases->pluck('amount', 'ym');
        $expenseMap = $monthlyExpenses->pluck('amount', 'ym');

        return view('reports.index', [
            'labels' => $months->map(fn ($month) => Carbon::createFromFormat('Y-m', $month)->translatedFormat('M Y'))->values(),
            'purchaseValues' => $months->map(fn ($month) => (float) ($purchaseMap[$month] ?? 0))->values(),
            'expenseValues' => $months->map(fn ($month) => (float) ($expenseMap[$month] ?? 0))->values(),
            'totalPurchases' => (float) Purchase::sum('total_amount'),
            'totalExpenses' => (float) Expense::sum('amount'),
            'totalSuppliers' => Supplier::count(),
            'lowStockCount' => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'expenseByCategory' => ExpenseCategory::query()
                ->leftJoin('expenses', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->select('expense_categories.name', DB::raw('COALESCE(SUM(expenses.amount), 0) as total'))
                ->groupBy('expense_categories.id', 'expense_categories.name')
                ->orderByDesc('total')
                ->take(8)
                ->get(),
            'topProducts' => Product::query()
                ->leftJoin('purchase_items', 'purchase_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('COALESCE(SUM(purchase_items.quantity), 0) as quantity'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('quantity')
                ->take(8)
                ->get(),
            'topSuppliers' => Supplier::query()
                ->withSum('purchases', 'total_amount')
                ->withSum('payments', 'amount')
                ->orderByDesc('purchases_sum_total_amount')
                ->take(8)
                ->get(),
        ]);
    }
}
