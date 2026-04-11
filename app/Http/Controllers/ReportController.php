<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Hall;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $activeTab = in_array(request('tab'), ['analytics', 'trends', 'occupancy', 'services'], true)
            ? request('tab')
            : 'analytics';

        $reportPeriods = [
            'daily' => $this->buildDailyReport(),
            'weekly' => $this->buildWeeklyReport(),
            'monthly' => $this->buildMonthlyReport(),
            'half_year' => $this->buildHalfYearReport(),
            'yearly' => $this->buildYearlyReport(),
        ];
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthlyRevenue = (float) Payment::query()
            ->whereBetween('payment_date', [$monthStart->toDateString(), now()->toDateString()])
            ->sum('amount');
        $monthlyExpenses = (float) Expense::query()
            ->whereBetween('expense_date', [$monthStart->toDateString(), now()->toDateString()])
            ->sum('amount');
        $hallOccupancy = Hall::query()
            ->withCount(['bookings as monthly_bookings_count' => fn ($query) => $query
                ->whereBetween('event_date', [$monthStart->toDateString(), $monthEnd->toDateString()])])
            ->orderByDesc('monthly_bookings_count')
            ->take(6)
            ->get();
        $topServices = BookingService::query()
            ->join('services', 'services.id', '=', 'booking_services.service_id')
            ->select(
                'services.name',
                DB::raw('SUM(booking_services.quantity) as total_quantity'),
                DB::raw('SUM(booking_services.total) as total_amount'),
                DB::raw('COUNT(DISTINCT booking_services.booking_id) as booking_count')
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total_quantity')
            ->take(8)
            ->get();
        $weekdayDensityMap = Booking::query()
            ->selectRaw('WEEKDAY(event_date) as weekday_index, COUNT(*) as total')
            ->whereBetween('event_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('weekday_index')
            ->pluck('total', 'weekday_index');
        $weekdayDensity = collect([
            ['label' => 'Dush', 'index' => 0],
            ['label' => 'Sesh', 'index' => 1],
            ['label' => 'Chor', 'index' => 2],
            ['label' => 'Pay', 'index' => 3],
            ['label' => 'Jum', 'index' => 4],
            ['label' => 'Shan', 'index' => 5],
            ['label' => 'Yak', 'index' => 6],
        ])->map(fn (array $day) => [
            'label' => $day['label'],
            'total' => (int) ($weekdayDensityMap[$day['index']] ?? 0),
        ]);
        $analyticsSummary = [
            'totalRevenue' => (float) Payment::sum('amount'),
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyExpenses' => $monthlyExpenses,
            'netBalance' => $monthlyRevenue - $monthlyExpenses,
            'activeBookings' => Booking::query()->whereIn('status', ['Yangi', 'Tasdiqlangan', 'Tayyorlanmoqda'])->count(),
            'upcomingBookings' => Booking::query()->upcoming()->count(),
            'debtBookings' => Booking::query()->where('remaining_amount', '>', 0)->count(),
            'averageBookingValue' => (float) Booking::query()->avg('total_amount'),
            'averageGuests' => (float) Booking::query()
                ->whereBetween('event_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->avg('guest_count'),
        ];
        $serviceSummary = [
            'serviceCount' => $topServices->count(),
            'serviceBookings' => (int) BookingService::query()->distinct('booking_id')->count('booking_id'),
            'serviceQuantity' => (int) $topServices->sum('total_quantity'),
            'serviceRevenue' => (float) $topServices->sum('total_amount'),
        ];

        return view('reports.index', [
            'activeTab' => $activeTab,
            'reportPeriods' => $reportPeriods,
            'totalPurchases' => (float) Purchase::sum('total_amount'),
            'totalExpenses' => (float) Expense::sum('amount'),
            'totalSuppliers' => Supplier::count(),
            'lowStockCount' => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'analyticsSummary' => $analyticsSummary,
            'expenseByCategory' => ExpenseCategory::query()
                ->leftJoin('expenses', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->select('expense_categories.name', DB::raw('COALESCE(SUM(expenses.amount), 0) as total'))
                ->groupBy('expense_categories.id', 'expense_categories.name')
                ->orderByDesc('total')
                ->take(8)
                ->get(),
            'topProducts' => Product::query()
                ->leftJoin('purchase_items', 'purchase_items.product_id', '=', 'products.id')
                ->select('products.name', 'products.unit', DB::raw('COALESCE(SUM(purchase_items.quantity), 0) as quantity'))
                ->groupBy('products.id', 'products.name', 'products.unit')
                ->orderByDesc('quantity')
                ->take(8)
                ->get(),
            'topSuppliers' => Supplier::query()
                ->withSum('purchases', 'total_amount')
                ->withSum('payments', 'amount')
                ->orderByDesc('purchases_sum_total_amount')
                ->take(8)
                ->get(),
            'hallOccupancy' => $hallOccupancy,
            'weekdayDensity' => $weekdayDensity,
            'currentMonthBookings' => Booking::query()
                ->whereBetween('event_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->count(),
            'avgGuestsPerBooking' => (float) Booking::query()
                ->whereBetween('event_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->avg('guest_count'),
            'busiestHall' => $hallOccupancy->first(),
            'upcomingEvents' => Booking::query()
                ->with(['client', 'hall', 'eventType'])
                ->upcoming()
                ->take(5)
                ->get(),
            'topServices' => $topServices,
            'serviceSummary' => $serviceSummary,
        ]);
    }

    private function buildDailyReport(): array
    {
        $dates = collect(CarbonPeriod::create(now()->copy()->subDays(6)->startOfDay(), '1 day', now()->copy()->startOfDay()))
            ->map(fn (Carbon $date) => $date->copy());

        $purchaseMap = Purchase::query()
            ->selectRaw('DATE(purchase_date) as period_key, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', now()->copy()->subDays(6)->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        $expenseMap = Expense::query()
            ->selectRaw('DATE(expense_date) as period_key, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', now()->copy()->subDays(6)->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        return $this->makePeriodPayload(
            $dates,
            fn (Carbon $date) => $date->toDateString(),
            fn (Carbon $date) => $date->format('d M'),
            $purchaseMap,
            $expenseMap,
            "So'nggi 7 kun"
        );
    }

    private function buildWeeklyReport(): array
    {
        $weeks = collect(range(7, 0, -1))
            ->map(fn (int $offset) => now()->copy()->startOfWeek()->subWeeks($offset))
            ->push(now()->copy()->startOfWeek());

        $purchaseMap = Purchase::query()
            ->selectRaw('YEAR(purchase_date) as year_value, WEEK(purchase_date, 1) as week_value, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', now()->copy()->subWeeks(7)->startOfWeek()->toDateString())
            ->groupBy('year_value', 'week_value')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->year_value.'-'.$row->week_value => (float) $row->amount]);

        $expenseMap = Expense::query()
            ->selectRaw('YEAR(expense_date) as year_value, WEEK(expense_date, 1) as week_value, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', now()->copy()->subWeeks(7)->startOfWeek()->toDateString())
            ->groupBy('year_value', 'week_value')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->year_value.'-'.$row->week_value => (float) $row->amount]);

        return $this->makePeriodPayload(
            $weeks,
            fn (Carbon $date) => $date->format('o-W'),
            fn (Carbon $date) => $date->format('d M'),
            $purchaseMap,
            $expenseMap,
            "So'nggi 8 hafta"
        );
    }

    private function buildMonthlyReport(): array
    {
        $days = collect(CarbonPeriod::create(now()->copy()->subDays(29)->startOfDay(), '1 day', now()->copy()->startOfDay()))
            ->map(fn (Carbon $date) => $date->copy());

        $purchaseMap = Purchase::query()
            ->selectRaw('DATE(purchase_date) as period_key, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', now()->copy()->subDays(29)->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        $expenseMap = Expense::query()
            ->selectRaw('DATE(expense_date) as period_key, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', now()->copy()->subDays(29)->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        return $this->makePeriodPayload(
            $days,
            fn (Carbon $date) => $date->toDateString(),
            fn (Carbon $date) => $date->format('d M'),
            $purchaseMap,
            $expenseMap,
            "So'nggi 30 kun"
        );
    }

    private function buildHalfYearReport(): array
    {
        $months = collect(range(5, 0, -1))
            ->map(fn (int $offset) => now()->copy()->subMonths($offset)->startOfMonth())
            ->push(now()->copy()->startOfMonth());

        $purchaseMap = Purchase::query()
            ->selectRaw('DATE_FORMAT(purchase_date, "%Y-%m") as period_key, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', now()->copy()->subMonths(5)->startOfMonth()->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        $expenseMap = Expense::query()
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as period_key, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', now()->copy()->subMonths(5)->startOfMonth()->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        return $this->makePeriodPayload(
            $months,
            fn (Carbon $date) => $date->format('Y-m'),
            fn (Carbon $date) => $date->translatedFormat('M Y'),
            $purchaseMap,
            $expenseMap,
            "So'nggi 6 oy"
        );
    }

    private function buildYearlyReport(): array
    {
        $months = collect(range(11, 0, -1))
            ->map(fn (int $offset) => now()->copy()->subMonths($offset)->startOfMonth())
            ->push(now()->copy()->startOfMonth());

        $purchaseMap = Purchase::query()
            ->selectRaw('DATE_FORMAT(purchase_date, "%Y-%m") as period_key, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', now()->copy()->subMonths(11)->startOfMonth()->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        $expenseMap = Expense::query()
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as period_key, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', now()->copy()->subMonths(11)->startOfMonth()->toDateString())
            ->groupBy('period_key')
            ->pluck('amount', 'period_key');

        return $this->makePeriodPayload(
            $months,
            fn (Carbon $date) => $date->format('Y-m'),
            fn (Carbon $date) => $date->translatedFormat('M Y'),
            $purchaseMap,
            $expenseMap,
            "So'nggi 12 oy"
        );
    }

    private function makePeriodPayload(
        Collection $periods,
        callable $keyResolver,
        callable $labelResolver,
        Collection $purchaseMap,
        Collection $expenseMap,
        string $title
    ): array {
        $purchaseValues = $periods->map(fn (Carbon $period) => (float) ($purchaseMap[$keyResolver($period)] ?? 0))->values();
        $expenseValues = $periods->map(fn (Carbon $period) => (float) ($expenseMap[$keyResolver($period)] ?? 0))->values();
        $purchaseTotal = $purchaseValues->sum();
        $expenseTotal = $expenseValues->sum();
        $balance = $purchaseTotal - $expenseTotal;

        return [
            'title' => $title,
            'labels' => $periods->map(fn (Carbon $period) => $labelResolver($period))->values(),
            'purchaseValues' => $purchaseValues,
            'expenseValues' => $expenseValues,
            'profit' => max($balance, 0),
            'loss' => max($balance * -1, 0),
            'total' => $purchaseTotal + $expenseTotal,
            'purchaseTotal' => $purchaseTotal,
            'expenseTotal' => $expenseTotal,
        ];
    }
}
