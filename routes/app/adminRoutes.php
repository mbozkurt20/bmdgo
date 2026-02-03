<?php

use App\Helpers\NotificationHelper;
use App\Http\Controllers\TamiPaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => ['guest.admin']], function () {
        Route::view('login', 'auth.login')->name('admin.login');
        Route::post('login', [App\Http\Controllers\AdminController::class, 'login'])->name('admin.auth');
    });

    Route::group(['middleware' => ['admin.auth']], function () {
        Route::get('/get-districts/{cityId}', [App\Http\Controllers\AdminController::class, 'getDistricts'])->name('admin.get_districts');

        //paytr
        Route::post('/payment/paytr', [\App\Http\Controllers\PayTrPaymentController::class, 'payTrPayment'])->name('admin.payment.paytr.form');

        //tami
        Route::get('/payment/form', [TamiPaymentController::class, 'showForm'])->name('admin.payment.tami.form');
        Route::post('/payment/start', [TamiPaymentController::class, 'start'])->name('payment.start');

        Route::get('/printed/{orderId}', [App\Http\Controllers\OrderController::class, 'printed']);
        Route::get('/statistics', [App\Http\Controllers\AdminController::class, 'statistics'])->name('admin.statistics');
        Route::get('/orders/ajax', [App\Http\Controllers\AdminController::class, 'ajax'])->name('admin.orders.ajax');

        Route::get('notifications/clear-all', [App\Http\Controllers\AdminController::class, 'notifications'])->name('admin.notifications');
        Route::get('notifications/{id}', [App\Http\Controllers\AdminController::class, 'notificationDelete']);
        Route::get('profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('admin.profile');
        Route::post('/profile', [App\Http\Controllers\AdminController::class, 'profileUpdate'])->name('admin.profile.update');

        Route::get('/features', [App\Http\Controllers\AdminController::class, 'features'])->name('admin.features');
        Route::get('/features/update/{id}', [App\Http\Controllers\AdminController::class, 'featuresUpdate'])->name('admin.features.update');

        Route::get('/sms/entegrations', [App\Http\Controllers\MyController::class, 'smsEntegrations'])->name('admin.sms.entegrations');
        Route::post('/sms/entegrations/update', [App\Http\Controllers\MyController::class, 'smsEntegrastionUpdate'])->name('admin.sms.entegrations.update');
        Route::post('/sms/entegrations/test', [App\Http\Controllers\MyController::class, 'smsEntegrastionTest'])->name('admin.sms.entegrations.test');
        Route::get('/update/entegrations/status', [App\Http\Controllers\MyController::class, 'smsEntegrastionStatus'])->name('admin.sms.entegrations.status');

        Route::get('/', [App\Http\Controllers\AdminController::class, 'home'])->name('admin.index');

        Route::get('/top-up-balance', [App\Http\Controllers\AdminController::class, 'balance'])->name('admin.balance');
        Route::get('topup/talep', [App\Http\Controllers\AdminController::class, 'topupTalep'])->name('admin.topupTalep');

        Route::get('/tt', [App\Http\Controllers\AdminController::class, 'tt']);
        Route::post('logout', [App\Http\Controllers\AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/filter-by-date', [App\Http\Controllers\AdminController::class, 'filterByDate'])->name('admin.filterByDate');
        Route::get('/orders/filter', [App\Http\Controllers\AdminController::class, 'filterOrders'])->name('admin.filter');

        /* Siparisler */
        Route::get('/deletedOrders', [App\Http\Controllers\Admin\SiparislerController::class, 'deletedOrders'])->name('admin.deletedOrders');
        Route::get('/deliveredOrders', [App\Http\Controllers\Admin\SiparislerController::class, 'deliveredOrders'])->name('admin.deliveredOrders');

        Route::post('/telefonsiparis/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::get('/trendyol/get-orders', [App\Http\Controllers\TrendyolYemekController::class, 'index']);
        Route::post('/trendyol/updateOrderStatus', [App\Http\Controllers\TrendyolYemekController::class, 'orderStatus']);
        Route::post('/yemeksepeti/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::post('/getir/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::post('/adisyo/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);
        Route::post('/gpsyemek/updateOrderStatus', [App\Http\Controllers\GpsYemekController::class, 'updateOrder']);

        Route::post('/orders/message', [App\Http\Controllers\OrderController::class, 'message']);

        /* Restaurants */
        Route::get('/restaurants', [\App\Http\Controllers\Admin\RestaurantsController::class, 'index'])->name('admin.restaurants');
        Route::get('/restaurants/new', [\App\Http\Controllers\Admin\RestaurantsController::class, 'new'])->name('admin.restaurants.new');
        Route::get('/restaurants/edit/{id}', [App\Http\Controllers\Admin\RestaurantsController::class, 'edit'])->name('admin.restaurants.edit');
        Route::get('/restaurants/delete/{id}', [App\Http\Controllers\Admin\RestaurantsController::class, 'delete'])->name('admin.restaurants.delete');
        Route::post('/restaurants/create', [\App\Http\Controllers\Admin\RestaurantsController::class, 'create'])->name('admin.restaurants.create');
        Route::post('/restaurants/update', [App\Http\Controllers\Admin\RestaurantsController::class, 'update'])->name('admin.restaurants.update');

        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports');

        //Giderler - hakedişler
        Route::get('/progress-payment/restaurant', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'restaurant'])->name('admin.progress_payment.restaurant');
        Route::get('/progress-payment/courier', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'courier'])->name('admin.progress_payment.courier');
        Route::post('/progress-payment/records', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'storeRecords'])->name('admin.progress.payments.store');
        Route::post('/progress-payment/restaurant', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'restaurantFilter']);
        Route::post('/progress-payment/courier', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'courierFilter']);
        Route::get('/progress-payment/record/delete/{recordId}', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'deleteRecords']);

        /* GİDERLER */
        Route::prefix('expenses')->group(function () {
            Route::get('/',[\App\Http\Controllers\Admin\ExpensesController::class, 'index'])->name('admin.expenses.index');
            Route::get('/new',[\App\Http\Controllers\Admin\ExpensesController::class, 'create'])->name('admin.expenses.new');
            Route::get('/edit/{id}',[\App\Http\Controllers\Admin\ExpensesController::class, 'edit'])->name('admin.expenses.edit');
            Route::post('/update/{id}',[\App\Http\Controllers\Admin\ExpensesController::class, 'update'])->name('admin.expenses.update');
            Route::post('/',[\App\Http\Controllers\Admin\ExpensesController::class, 'store'])->name('admin.expenses.store');
            Route::get('/delete/{id}',[\App\Http\Controllers\Admin\ExpensesController::class, 'destroy'])->name('admin.expenses.destroy');
        });

        /* KURYELER */
        Route::get('/courier-performance', [\App\Http\Controllers\Admin\CourierController::class, 'performance'])->name('admin.courier.performance');
        Route::get('/get-couriers', [\App\Http\Controllers\Admin\CourierController::class, 'getCourier']);
        Route::get('/couriers', [\App\Http\Controllers\Admin\CourierController::class, 'index'])->name('admin.couriers');
        Route::get('/couriers/maps', [\App\Http\Controllers\Admin\CourierController::class, 'maps'])->name('admin.couriers.maps');
        Route::get('/couriers/new', [App\Http\Controllers\Admin\CourierController::class, 'new'])->name('admin.couriers.new');
        Route::get('/couriers/edit/{id}', [App\Http\Controllers\Admin\CourierController::class, 'edit'])->name('admin.couriers.edit');
        Route::get('/couriers/delete/{id}', [App\Http\Controllers\Admin\CourierController::class, 'delete'])->name('admin.couriers.delete');
        Route::get('/couriers/report/{id}', [App\Http\Controllers\Admin\CourierController::class, 'report'])->name('admin.couriers.report');
        Route::post('/couriers/create', [App\Http\Controllers\Admin\CourierController::class, 'create'])->name('admin.couriers.create');
        Route::post('/couriers/update', [App\Http\Controllers\Admin\CourierController::class, 'update'])->name('admin.couriers.update');

        Route::get('/order/auto_order/{status}', [App\Http\Controllers\AdminController::class, 'auto_order'])->name('admin.couriers.auto_order');
        Route::get('/orders/sendCourier/{orderId}/{courierId}', [\App\Http\Controllers\Admin\CourierController::class, 'sendCourier']);
        Route::post('/reports/globalFilter', [App\Http\Controllers\Admin\ReportController::class, 'globalFilter']);
        Route::get('orders/delete/{id}', [App\Http\Controllers\OrderController::class, 'deleteOrder']);
    });
});
