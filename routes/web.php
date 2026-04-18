<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingUsageItemController;
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
use App\Http\Controllers\Billing\CheckoutController as BillingCheckoutController;
use App\Http\Controllers\Billing\ClickWebhookController;
use App\Http\Controllers\Billing\PaymentController as BillingPaymentController;
use App\Http\Controllers\Billing\PaymeWebhookController;
use App\Http\Controllers\Billing\PlanController as BillingPlanController;
use App\Http\Controllers\Billing\SubscriptionController as BillingSubscriptionController;
use App\Http\Controllers\Billing\TelegramConnectionController as BillingTelegramConnectionController;
use App\Http\Controllers\Billing\TelegramWebhookController as BillingTelegramWebhookController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WeddingPackageController;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\AnalyticsController as SuperAdminAnalyticsController;
use App\Http\Controllers\SuperAdmin\ApprovalController as SuperAdminApprovalController;
use App\Http\Controllers\SuperAdmin\AuditLogController as SuperAdminAuditLogController;
use App\Http\Controllers\SuperAdmin\BillingController as SuperAdminBillingController;
use App\Http\Controllers\SuperAdmin\BusinessController as SuperAdminBusinessController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\IntegrationController as SuperAdminIntegrationController;
use App\Http\Controllers\SuperAdmin\LandingContentController as SuperAdminLandingContentController;
use App\Http\Controllers\SuperAdmin\LanguageController as SuperAdminLanguageController;
use App\Http\Controllers\SuperAdmin\NotificationController as SuperAdminNotificationController;
use App\Http\Controllers\SuperAdmin\PageController as SuperAdminPageController;
use App\Http\Controllers\SuperAdmin\PaymentProofController as SuperAdminPaymentProofController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperAdminPlanController;
use App\Http\Controllers\SuperAdmin\SecurityController as SuperAdminSecurityController;
use App\Http\Controllers\SuperAdmin\SettingController as SuperAdminSettingController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperAdminSubscriptionController;
use App\Http\Controllers\SuperAdmin\TelegramWorkflowController as SuperAdminTelegramWorkflowController;
use App\Http\Controllers\SuperAdmin\UserManagementController as SuperAdminUserManagementController;
use App\Http\Controllers\SuperAdmin\VenueConnectionController as SuperAdminVenueConnectionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

Route::get('/media/{mediaFile}/{filename?}', MediaFileController::class)->name('media.show');

Route::view('/', 'landing.index')->name('landing');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.store');

    Route::get('/superadmin/login', [SuperAdminAuthController::class, 'create'])->name('superadmin.login');
    Route::post('/superadmin/login', [SuperAdminAuthController::class, 'store'])->middleware('throttle:20,1')->name('superadmin.login.store');
});

Route::post('/billing/click/prepare', [ClickWebhookController::class, 'prepare'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('billing.click.prepare');
Route::post('/billing/click/complete', [ClickWebhookController::class, 'complete'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('billing.click.complete');
Route::post('/billing/payme/merchant', PaymeWebhookController::class)
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('billing.payme.merchant');
Route::post('/telegram/webhook', BillingTelegramWebhookController::class)
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->middleware('telegram.webhook')
    ->name('telegram.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/low-stock-word', [DashboardController::class, 'exportLowStockWord'])->middleware('throttle:6,1')->name('dashboard.low-stock-word');

    Route::post('/suppliers/{supplier}/payments', [SupplierController::class, 'storePayment'])->name('suppliers.payments.store');

    Route::resources([
        'suppliers' => SupplierController::class,
        'products' => ProductController::class,
        'booking-usage-items' => BookingUsageItemController::class,
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
    Route::get('/plans', [BillingPlanController::class, 'index'])->name('plans.index');
    Route::get('/subscriptions', [BillingSubscriptionController::class, 'index'])->name('subscriptions.index');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->middleware('throttle:20,1')->name('settings.update');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->middleware('throttle:10,1')->name('settings.password.update');

    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/plans', [BillingPlanController::class, 'index'])->name('plans.index');
        Route::post('/plans/{plan}/checkout', [BillingCheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/subscriptions', [BillingSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/payments', [BillingPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}/checkout', [BillingCheckoutController::class, 'show'])->name('checkout.show');
    });

    Route::put('/settings/telegram', [BillingTelegramConnectionController::class, 'update'])->name('settings.telegram.update');
    Route::post('/settings/telegram/regenerate', [BillingTelegramConnectionController::class, 'regenerate'])->name('settings.telegram.regenerate');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', \App\Http\Middleware\EnsureSuperAdmin::class])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/', SuperAdminDashboardController::class)->name('dashboard');
        Route::get('/businesses', [SuperAdminBusinessController::class, 'index'])->name('businesses.index');
        Route::get('/businesses/{business}', [SuperAdminBusinessController::class, 'show'])->name('businesses.show');
        Route::put('/businesses/{business}', [SuperAdminBusinessController::class, 'update'])->name('businesses.update');
        Route::get('/venues', [SuperAdminBusinessController::class, 'index'])->name('venues.index');
        Route::put('/venues/{venueConnection}', [SuperAdminVenueConnectionController::class, 'update'])->name('venues.update');
        Route::post('/venues/{venueConnection}/reset-credentials', [SuperAdminVenueConnectionController::class, 'resetCredentials'])->name('venues.reset-credentials');
        Route::get('/approvals', [SuperAdminApprovalController::class, 'index'])->name('approvals.index');
        Route::put('/approvals/{approval}', [SuperAdminApprovalController::class, 'update'])->name('approvals.update');
        Route::get('/users', [SuperAdminUserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [SuperAdminUserManagementController::class, 'show'])->name('users.show');
        Route::put('/users/{user}', [SuperAdminUserManagementController::class, 'update'])->name('users.update');
        Route::get('/subscriptions', [SuperAdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::put('/subscriptions/{subscription}', [SuperAdminSubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::get('/plans', [SuperAdminPlanController::class, 'index'])->name('plans.index');
        Route::post('/plans', [SuperAdminPlanController::class, 'store'])->name('plans.store');
        Route::put('/plans/{plan}', [SuperAdminPlanController::class, 'update'])->name('plans.update');
        Route::get('/payments-center', [SuperAdminBillingController::class, 'index'])->name('payments.index');
        Route::get('/payments-center/{payment}', [SuperAdminBillingController::class, 'show'])->name('payments.show');
        Route::put('/payments-center/{payment}', [SuperAdminBillingController::class, 'update'])->name('payments.update');
        Route::post('/payments-center/{payment}/review', [SuperAdminTelegramWorkflowController::class, 'review'])->name('payments.review');
        Route::get('/payments-center/{payment}/proof', SuperAdminPaymentProofController::class)->name('payments.proof');
        Route::put('/payment-methods/{method}', [SuperAdminBillingController::class, 'updateMethod'])->name('payment-methods.update');
        Route::get('/analytics', SuperAdminAnalyticsController::class)->name('analytics.index');
        Route::get('/notifications', [SuperAdminNotificationController::class, 'index'])->name('notifications.index');
        Route::put('/notifications/{notification}', [SuperAdminNotificationController::class, 'update'])->name('notifications.update');
        Route::get('/landing', [SuperAdminLandingContentController::class, 'edit'])->name('landing.edit');
        Route::put('/landing', [SuperAdminLandingContentController::class, 'update'])->name('landing.update');
        Route::get('/pages', [SuperAdminPageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages', [SuperAdminPageController::class, 'update'])->name('pages.update');
        Route::get('/languages', [SuperAdminLanguageController::class, 'edit'])->name('languages.edit');
        Route::put('/languages', [SuperAdminLanguageController::class, 'update'])->name('languages.update');
        Route::get('/settings', [SuperAdminSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SuperAdminSettingController::class, 'update'])->name('settings.update');
        Route::put('/settings/password', [SuperAdminSettingController::class, 'updatePassword'])->name('settings.password.update');
        Route::get('/audit-logs', SuperAdminAuditLogController::class)->name('audit.index');
        Route::get('/integrations', [SuperAdminIntegrationController::class, 'edit'])->name('integrations.edit');
        Route::put('/integrations/telegram', [SuperAdminIntegrationController::class, 'update'])->name('integrations.telegram.update');
        Route::post('/integrations/telegram/test', [SuperAdminIntegrationController::class, 'test'])->name('integrations.telegram.test');
        Route::put('/integrations/billing', [SuperAdminIntegrationController::class, 'updateBilling'])->name('integrations.billing.update');
        Route::get('/telegram-workflow', [SuperAdminTelegramWorkflowController::class, 'edit'])->name('telegram.edit');
        Route::put('/telegram-workflow', [SuperAdminTelegramWorkflowController::class, 'update'])->name('telegram.update');
        Route::post('/telegram-workflow/test', [SuperAdminTelegramWorkflowController::class, 'test'])->name('telegram.test');
        Route::get('/security', SuperAdminSecurityController::class)->name('security.index');
    });
