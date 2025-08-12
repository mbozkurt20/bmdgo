<?php

use App\Http\Controllers\Auth\SuperAdmin;
use App\Http\Controllers\SuperAdmin\Ajax\AjaxController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\Report\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'superadmin'], function(){
    Route::view('login', "superadmin.login")->name('superadmin.login');
    Route::post('login', [SuperAdmin::class, 'login'])->name('superadmin.auth');
    Route::post('logout', [SuperAdmin::class, 'logout'])->name('superadmin.logout');

    Route::middleware(['superadmin.auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboards');
        Route::get('/dashboard-data', [AjaxController::class, 'getDashboardData'])->name('superadmin.dashboards.get_data');

        Route::get('/orders', [DashboardController::class, 'orders'])->name('superadmin.orders');
        Route::get('/dealer', [DashboardController::class, 'dealer'])->name('superadmin.dealer');
        Route::get('/dealer/add', [DashboardController::class, 'createDealer'])->name('superadmin.dealer_create');
        Route::post('/dealer/add', [DashboardController::class, 'createDealerRequest'])->name('superadmin.dealer_create_request');
        Route::get('/dealer/edit/{id}', [DashboardController::class, 'editDealer'])->name('superadmin.dealer_edit');
        Route::put('/dealer/edit/{id}', [DashboardController::class, 'updateDealer'])->name('superadmin.dealer_update');
        Route::delete('/dealer/{id}', [DashboardController::class, 'deleteDealer'])->name('superadmin.dealer_delete');

        Route::view('/reports', 'superadmin.reports.index')->name('superadmin.reports');
        Route::get('/reports/download', [ReportController::class, 'downloadReport'])->name('superadmin.reports.download');

        Route::get('dashboard/filter-by-date', [DashboardController::class, 'filterByDate'])->name('superadmin.filterByDate');
        Route::get('dashboard/orders/filter', [DashboardController::class, 'filterOrders'])->name('superadmin.filter');
    });
});
