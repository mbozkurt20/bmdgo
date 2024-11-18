<?php

use App\Http\Controllers\Auth\SuperAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahalleController;
use App\Http\Controllers\SuperAdmin\Ajax\AjaxController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\Report\ReportController;
use App\Models\Order;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Custom Routes
Route::get('/mahalle-create', [MahalleController::class, 'create']);
Route::get('/restaurant/orders/new', [MahalleController::class, 'create']);
Route::get('/restaurant/couriers', [App\Http\Controllers\CourierController::class, 'index'])->name('restaurant.couriers.index');
Route::get('/orders', [OrderController::class, 'index'])->name('getOrders');
Route::get('/restaurant/customers', [CustomerController::class, 'index'])->name('restaurant.customers');
Route::get('/restaurant/categories', [CategoryController::class, 'index'])->name('restaurant.categories');
Route::get('/restaurant/products', [ProductController::class, 'index'])->name('restaurant.products');
Route::get('/restaurant/couriers', [CourierController::class, 'index'])->name('restaurant.couriers');
Route::get('/restaurant/delivered-orders', [OrderController::class, 'deliveredOrders'])->name('restaurant.deliveredOrders');




// Change 'home' route name to avoid conflict
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');  // Change 'home' to 'home.index'

// Authentication Routes
Auth::routes(['register' => false]);  // Disable register if not needed

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.dashboard');  // Keep home route for dashboard

// Super Admin Routes
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
    });
});

// Admin Routes
Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => ['guest.admin']], function () {
        Route::view('login', 'admin.login')->name('admin.login');
        Route::post('login', [App\Http\Controllers\AdminController::class, 'login'])->name('admin.auth');
    });
    Route::group(['middleware' => ['admin.auth']], function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'home'])->name('admin.index');
        Route::post('logout', [App\Http\Controllers\AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/filter-by-date', [App\Http\Controllers\AdminController::class, 'filterByDate'])->name('admin.filterByDate');
        Route::get('/orders/filter', [App\Http\Controllers\AdminController::class, 'filterOrders'])->name('admin.filter');

        /* Siparisler */
        Route::get('/deletedOrders', [App\Http\Controllers\Admin\SiparislerController::class, 'deletedOrders'])->name('admin.deletedOrders');
        Route::get('/deliveredOrders', [App\Http\Controllers\Admin\SiparislerController::class, 'deliveredOrders'])->name('admin.deliveredOrders');

        Route::post('/telefonsiparis/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::post('/trendyol/updateOrderStatus', [App\Http\Controllers\TrendyolYemekController::class, 'orderStatus']);
        Route::post('/yemeksepeti/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::post('/getir/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);
        Route::post('/adisyo/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);
        Route::post('/updateCourierStatus', [App\Http\Controllers\OrderController::class, 'updateCourierStatus'])->name('updateCourierStatus');
        Route::post('/orders/message', [App\Http\Controllers\OrderController::class, 'message']);

        /* Restaurants */
        Route::get('/restaurants', [\App\Http\Controllers\Admin\RestaurantsController::class, 'index'])->name('admin.restaurants');
        Route::get('/restaurants/new', [\App\Http\Controllers\Admin\RestaurantsController::class, 'new'])->name('admin.restaurants.new');
        Route::get('/restaurants/edit/{id}', [App\Http\Controllers\Admin\RestaurantsController::class, 'edit'])->name('admin.restaurants.edit');
        Route::get('/restaurants/delete/{id}', [App\Http\Controllers\Admin\RestaurantsController::class, 'delete'])->name('admin.restaurants.delete');
        Route::post('/restaurants/create', [\App\Http\Controllers\Admin\RestaurantsController::class, 'create'])->name('admin.restaurants.create');
        Route::post('/restaurants/update', [App\Http\Controllers\Admin\RestaurantsController::class, 'update'])->name('admin.restaurants.update');

        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports');

        Route::get('/progress-payment/restaurant', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'restaurant'])->name('admin.progress_payment.restaurant');
        Route::get('/progress-payment/courier', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'courier'])->name('admin.progress_payment.courier');

        /* Couriers */
        Route::get('/couriers', [\App\Http\Controllers\Admin\CourierController::class, 'index'])->name('admin.couriers');
        Route::get('/couriers/maps', [\App\Http\Controllers\Admin\CourierController::class, 'maps'])->name('admin.couriers.maps');
        Route::get('/couriers/new', [App\Http\Controllers\Admin\CourierController::class, 'new'])->name('admin.couriers.new');
        Route::get('/couriers/edit/{id}', [App\Http\Controllers\Admin\CourierController::class, 'edit'])->name('admin.couriers.edit');
        Route::get('/couriers/delete/{id}', [App\Http\Controllers\Admin\CourierController::class, 'delete'])->name('admin.couriers.delete');
        Route::get('/couriers/report/{id}', [App\Http\Controllers\Admin\CourierController::class, 'report'])->name('admin.couriers.report');
        Route::post('/couriers/create', [App\Http\Controllers\Admin\CourierController::class, 'create'])->name('admin.couriers.create');
        Route::post('/couriers/update', [App\Http\Controllers\Admin\CourierController::class, 'update'])->name('admin.couriers.update');
        Route::get('/orders/sendCourier/{orderid}/{courier}', [\App\Http\Controllers\Admin\CourierController::class, 'sendCourier']);
    });
});

// Restaurant Routes
Route::group(['prefix' => 'restaurant'], function () {
    Route::group(['middleware' => ['guest.restaurant']], function () {
        Route::view('register', 'restaurant.register')->name('restaurant.register');
        Route::view('login', 'restaurant.login')->name('restaurant.login');
        Route::view('payment', 'restaurant.payment')->name('restaurant.payment');
        Route::post('login', [App\Http\Controllers\RestaurantController::class, 'login'])->name('restaurant.auth');
        Route::post('register', [App\Http\Controllers\RestaurantController::class, 'register'])->name('restaurant.create');
    });
    Route::group(['middleware' => ['restaurant.auth']], function () {
        Route::get('/', [App\Http\Controllers\RestaurantController::class, 'home'])->name('restaurant.index');
        Route::post('logout', [App\Http\Controllers\RestaurantController::class, 'logout'])->name('restaurant.logout');
        Route::get('/filter-by-date', [App\Http\Controllers\RestaurantController::class, 'filterByDate'])->name('restaurant.filterByDate');
        Route::get('/orders/filter', [App\Http\Controllers\RestaurantController::class, 'filterOrders'])->name('orders.filter');
        
        // Orders
        Route::post('/orders/message', [App\Http\Controllers\OrderController::class, 'message']);
        Route::get('/orders', [App\Http\Controllers\RestaurantController::class, 'orders'])->name('restaurant.orders');
        Route::get('/orders/new', [App\Http\Controllers\RestaurantController::class, 'newOrders'])->name('restaurant.orders.new');
        Route::get('/orders/delivered', [App\Http\Controllers\RestaurantController::class, 'deliveredOrders'])->name('restaurant.orders.delivered');
    });
});
