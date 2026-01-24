<?php

use App\Http\Controllers\Auth\SuperAdmin;
use App\Http\Controllers\SuperAdmin\Ajax\AjaxController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\Report\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'superadmin'], function () {
    Route::view('login', "superadmin.login")->name('superadmin.login');
    Route::post('login', [SuperAdmin::class, 'login'])->name('superadmin.auth');
    Route::post('logout', [SuperAdmin::class, 'logout'])->name('superadmin.logout');

    Route::middleware(['superadmin.auth'])->group(function () {
        Route::get('/printed/{orderId}', [App\Http\Controllers\OrderController::class, 'printed']);
        Route::get('/orders/ajax', [DashboardController::class, 'ajax'])->name('superadmin.orders.ajax');
        Route::get('/get-couriers', [DashboardController::class, 'getCourier']);

        Route::get('/profile', [DashboardController::class, 'profile'])->name('superadmin.profile');
        Route::post('/profile', [DashboardController::class, 'profileUpdate'])->name('superadmin.profile.update');

        Route::get('/get-districts/{cityId}', [DashboardController::class, 'getDistricts'])->name('superadmin.get_districts');
        Route::get('/dashboard', [DashboardController::class, 'home'])->name('superadmin.dashboards');

        Route::get('/payment/entegrations', [App\Http\Controllers\MyController::class, 'paymentEntegrations'])->name('admin.payment.entegrations');
        Route::post('/payment/paytr', [App\Http\Controllers\MyController::class, 'paymentUpdateEntegrations'])->name('admin.payment.paytr.update');

        Route::prefix('dealer')->group(function () {
            Route::get('/', [DashboardController::class, 'dealer'])->name('superadmin.dealer');
            Route::get('/add', [DashboardController::class, 'createDealer'])->name('superadmin.dealer_create');
            Route::post('/add', [DashboardController::class, 'createDealerRequest'])->name('superadmin.dealer_create_request');
            Route::get('/edit/{id}', [DashboardController::class, 'editDealer'])->name('superadmin.dealer_edit');
            Route::put('/edit/{id}', [DashboardController::class, 'updateDealer'])->name('superadmin.dealer_update');
            Route::get('/delete/{id}', [DashboardController::class, 'deleteDealer'])->name('superadmin.dealer_delete');
            Route::get('/status/{id}', [DashboardController::class, 'statusDealer'])->name('superadmin.dealer_status');
        });

        Route::post('/telefonsiparis/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::get('/trendyol/get-orders', [App\Http\Controllers\TrendyolYemekController::class, 'index']);
        Route::post('/trendyol/updateOrderStatus', [App\Http\Controllers\TrendyolYemekController::class, 'orderStatus']);
        Route::post('/yemeksepeti/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::post('/getir/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::post('/adisyo/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);
        Route::post('/orders/message', [App\Http\Controllers\OrderController::class, 'message']);


        Route::prefix('admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'admin'])->name('superadmin.admin');
            Route::get('/add', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'createAdmin'])->name('superadmin.admin_create');

            Route::get('/topup/list', [App\Http\Controllers\SuperAdmin\AdminController::class, 'list'])
                ->name('superadmin.admin_topup_list');

            Route::get('/topup/{id}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'topup'])->name('superadmin.admin_topup');
            Route::post('/topup/add', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'topUpCreate'])->name('superadmin.admin_topup_create');
            Route::get('/topup/approve/{topupId}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'approve'])->name('superadmin.approve');
            Route::get('/topup/paid/{topupId}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'paid'])->name('superadmin.paid');
            Route::get('/topup/unpaid/{topupId}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'unPaid'])->name('superadmin.unpaid');

            Route::post('/add', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'createAdminRequest'])->name('superadmin.admin_create_request');
            Route::get('/edit/{id}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'editAdmin'])->name('superadmin.admin_edit');
            Route::put('/edit/{id}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'updateAdmin'])->name('superadmin.admin_update');
            Route::get('/delete/{id}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'deleteAdmin'])->name('superadmin.admin_delete');
            Route::get('/status/{id}', [\App\Http\Controllers\SuperAdmin\AdminController::class, 'statusAdmin'])->name('superadmin.admin_status');
        });

        Route::get('/orders', [DashboardController::class, 'orders'])->name('superadmin.orders');

        Route::get('/reports', [ReportController::class, 'index'])->name('superadmin.reports');
        Route::get('/reports/download', [ReportController::class, 'downloadReport'])->name('superadmin.reports.download');

        Route::get('dashboard/filter-by-date', [DashboardController::class, 'filterByDate'])->name('superadmin.filterByDate');
        Route::get('dashboard/orders/filter', [DashboardController::class, 'filterOrders'])->name('superadmin.filter');
    });
});
