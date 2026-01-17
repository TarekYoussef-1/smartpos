<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ReportController;

Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/dashboard/shifts/{id}/close', [DashboardController::class, 'closeShift'])
    ->middleware(['checkUserSession', 'checkRole:admin,manager'])
    ->name('shift.close');

Route::middleware(['checkUserSession'])->group(function () {

    // =====================
    // ADMIN
    // =====================
    Route::middleware(['checkRole:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::post('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/delete', [UserController::class, 'destroy'])->name('users.destroy');

        Route::put('/orders/{order}/cancel', [DashboardController::class, 'cancelOrder'])->name('orders.cancel');

        Route::get('/dashboard/sales-chart-data/{period}', [DashboardController::class, 'getSalesChartDataByPeriod'])->name('dashboard.sales.chart.data');
        Route::get('/dashboard/order-type-chart-data/{period}', [DashboardController::class, 'getOrderTypeChartDataByPeriod'])->name('dashboard.order-type.chart.data');

        // الشيفتات (Admin) - فتح، غلق، طباعة، مراجعة كاش
        Route::get('/shift/report/{id}', [DashboardController::class, 'printShift'])->name('shift.print');
        Route::get('/shift/cash-review/{id}', fn($id) => view('dashboard.cash_review', compact('id')))->name('shift.cash.review');
        Route::get('/dashboard/shift-cash/{id}', [DashboardController::class, 'shiftCashForm'])->name('shift.cash.form');
        Route::post('/dashboard/shift-cash-save/{id}', [DashboardController::class, 'shiftCashSave'])->name('shift.cash.save');
        Route::get('/dashboard/shift-cash-print/{id}', [DashboardController::class, 'shiftCashPrint'])->name('shift.cash.print');

        // Reprint
        Route::get('/pos/order/{order}/print', [POSController::class, 'reprint'])->name('pos.order.print');

        // Printers
        Route::resource('printers', PrinterController::class);
        Route::get('/test-printer-connection/{ip}', [PrinterController::class, 'testConnection']);

        // Reports (Admin)
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/sales/data', [ReportController::class, 'salesData'])->name('reports.sales.data');
        Route::get('/reports/sales-by-products', [ReportController::class, 'salesByProducts'])->name('reports.sales.products');
        Route::get('/reports/sales/print', [ReportController::class, 'salesPrint'])->name('reports.sales.print');
        Route::get('/reports/sales/export', [ReportController::class, 'exportSales'])->name('sales.export');
    });

    // =====================
    // POS (Cashier + Admin + Manager)
    // =====================
    Route::middleware(['checkRole:cashier,admin,manager'])->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/items/{department}', [POSController::class, 'getItems'])->name('pos.items');
        Route::get('/pos/edit/{order}', [POSController::class, 'edit'])->name('pos.edit');
        Route::post('/pos/delivery/store', [POSController::class, 'storeDelivery'])->name('pos.delivery.store');
        Route::post('/pos/customer/store', [POSController::class, 'storeCustomer'])->name('pos.customer.store');
        Route::post('/pos/order/store', [POSController::class, 'printInvoice'])->name('pos.order.store');
        Route::get('/pos/customer/search/{phone}', [POSController::class, 'searchCustomer'])
            ->where('phone', '[0-9]+')
            ->name('pos.customer.search');
    });

    // =====================
    // MANAGER Dashboard + Reports
    // =====================
    Route::middleware(['checkRole:manager'])->group(function () {
        Route::get('/dashboard-manager', [DashboardController::class, 'managerIndex'])->name('dashboard.manager');

        Route::get('/reports/sales-manager', [ReportController::class, 'sales'])->name('reports.sales.manager');
        Route::get('/reports/sales-manager/export', [ReportController::class, 'exportSales'])->name('reports.sales.export.manager');
    });

});

// Public / no-session
Route::get('/pos/kitchen-print/{order}', [POSController::class, 'printKitchenHTML'])->name('pos.kitchen.print');
