<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CostCategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EventCostController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FixedCostController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\KitchenCostController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WeddingPackageController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/suppliers/{supplier}/payments', [SupplierController::class, 'storePayment'])->name('suppliers.payments.store');

    Route::resources([
        'suppliers' => SupplierController::class,
        'products' => ProductController::class,
        'purchases' => PurchaseController::class,
        'inventory-expenses' => ExpenseController::class,
        'inventory-expense-categories' => ExpenseCategoryController::class,
        'bookings' => BookingController::class,
        'event-types' => EventTypeController::class,
        'halls' => HallController::class,
        'wedding-packages' => WeddingPackageController::class,
        'clients' => ClientController::class,
        'payments' => PaymentController::class,
        'employees' => EmployeeController::class,
    ]);

    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::resource('kitchen', KitchenCostController::class);
        Route::resource('event', EventCostController::class);
        Route::resource('fixed', FixedCostController::class);
        Route::resource('categories', CostCategoryController::class);
    });

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
