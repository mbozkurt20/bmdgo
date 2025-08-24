<?php

use App\Http\Controllers\Auth\Dealer;
use App\Http\Controllers\Dealer\Ajax\AjaxController;
use App\Http\Controllers\Dealer\DashboardController;
use App\Http\Controllers\Dealer\Report\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'dealer'], function(){
    Route::view('login', "dealer.login")->name('dealer.login');
    Route::post('login', [Dealer::class, 'login'])->name('dealer.auth');
    Route::post('logout', [Dealer::class, 'logout'])->name('dealer.logout');

    Route::middleware(['dealer.auth'])->group(function () {
        Route::get('/printed/{orderId}', [App\Http\Controllers\OrderController::class, 'printed']);

        Route::get('/profile', [DashboardController::class, 'profile'])->name('dealer.profile');
        Route::post('/profile', [DashboardController::class, 'profileUpdate'])->name('dealer.profile.update');

        Route::get('/get-districts/{cityId}', [DashboardController::class, 'getDistricts'])->name('dealer.get_districts');
        Route::get('/dashboard', [DashboardController::class, 'home'])->name('dealer.dashboards');
        Route::get('/dashboard-data', [AjaxController::class, 'getDashboardData'])->name('dealer.dashboards.get_data');

        Route::prefix('admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\Dealer\AdminController::class, 'admin'])->name('dealer.admin');
            Route::get('/add', [\App\Http\Controllers\Dealer\AdminController::class, 'createAdmin'])->name('dealer.admin_create');

            Route::get('/topup/list', [App\Http\Controllers\Dealer\AdminController::class, 'list'])
                ->name('dealer.admin_topup_list');

            Route::get('/topup/{id}', [\App\Http\Controllers\Dealer\AdminController::class, 'topup'])->name('dealer.admin_topup');
            Route::post('/topup/add', [\App\Http\Controllers\Dealer\AdminController::class, 'topUpCreate'])->name('dealer.admin_topup_create');
            Route::get('/topup/approve/{topupId}', [\App\Http\Controllers\Dealer\AdminController::class, 'approve'])->name('dealer.approve');
            Route::get('/topup/paid/{topupId}', [\App\Http\Controllers\Dealer\AdminController::class, 'paid'])->name('dealer.paid');
            Route::get('/topup/unpaid/{topupId}', [\App\Http\Controllers\Dealer\AdminController::class, 'unPaid'])->name('dealer.unpaid');

            Route::post('/add', [\App\Http\Controllers\Dealer\AdminController::class, 'createAdminRequest'])->name('dealer.admin_create_request');
            Route::get('/edit/{id}', [\App\Http\Controllers\Dealer\AdminController::class, 'editAdmin'])->name('dealer.admin_edit');
            Route::put('/edit/{id}', [\App\Http\Controllers\Dealer\AdminController::class, 'updateAdmin'])->name('dealer.admin_update');
            Route::get('/delete/{id}', [\App\Http\Controllers\Dealer\AdminController::class, 'deleteAdmin'])->name('dealer.admin_delete');
            Route::get('/status/{id}', [\App\Http\Controllers\Dealer\AdminController::class, 'statusAdmin'])->name('dealer.admin_status');
        });

        Route::get('/orders', [DashboardController::class, 'orders'])->name('dealer.orders');

        Route::get('/reports', [ReportController::class,'index'])->name('dealer.reports');
        Route::get('/reports/download', [ReportController::class, 'downloadReport'])->name('dealer.reports.download');

        Route::get('dashboard/filter-by-date', [DashboardController::class, 'filterByDate'])->name('dealer.filterByDate');
        Route::get('dashboard/orders/filter', [DashboardController::class, 'filterOrders'])->name('dealer.filter');
    });
});
