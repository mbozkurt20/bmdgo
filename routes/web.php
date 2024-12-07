<?php

use App\Events\OrderNotification;
use App\Helpers\OrdersHelper;
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

Route::get('/mahalle-create', [MahalleController::class, 'create']);

Route::get('/restaurant/orders/new', [MahalleController::class, 'create']);

Route::get('/restaurant/couriers', [App\Http\Controllers\CourierController::class, 'index'])->name('restaurant.couriers.index');


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.dashboard');


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
        Route::post('/updateCourierStatus', [App\Http\Controllers\OrderController::class, 'updateCourierStatus'])->name('updateCourierStatus2');
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

        Route::get('/setting/auto_order/{id}', [App\Http\Controllers\Admin\CourierController::class, 'auto_order'])->name('admin.couriers.auto_order');




        Route::get('/orders/sendCourier/{orderid}/{courier}', [\App\Http\Controllers\Admin\CourierController::class, 'sendCourier']);
        Route::post('/reports/globalFilter', [App\Http\Controllers\Admin\ReportController::class, 'globalFilter']);

        Route::post('/progress-payment/restaurant', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'restaurantFilter']);
        Route::post('/progress-payment/courier', [\App\Http\Controllers\Admin\ProgressPaymentController::class, 'courierFilter']);

        Route::get('orders/delete/{id}', [App\Http\Controllers\OrderController::class, 'deleteOrder']);
    });
});



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


        Route::get('/reports/orders', [App\Http\Controllers\ReportController::class, 'orders'])->name('restaurant.reports.orders');
        Route::get('/reports/couriers', [App\Http\Controllers\ReportController::class, 'couriers'])->name('restaurant.reports.couriers');


        /* Products */
        Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('restaurant.products');
        Route::get('/products/new', [App\Http\Controllers\ProductController::class, 'new'])->name('restaurant.products.new');
        Route::get('/products/edit/{id}', [App\Http\Controllers\ProductController::class, 'edit'])->name('restaurant.products.edit');
        Route::get('/products/delete/{id}', [App\Http\Controllers\ProductController::class, 'delete'])->name('restaurant.products.delete');
        Route::post('/products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('restaurant.products.create');
        Route::post('/products/update', [App\Http\Controllers\ProductController::class, 'update'])->name('restaurant.products.update');

        /* Categories */

        Route::get('/categories', [App\Http\Controllers\CategorieController::class, 'index'])->name('restaurant.categories');
        Route::get('/categories/new', [App\Http\Controllers\CategorieController::class, 'new'])->name('restaurant.categories.new');
        Route::get('/categories/edit/{id}', [App\Http\Controllers\CategorieController::class, 'edit'])->name('restaurant.categories.edit');
        Route::get('/categories/delete/{id}', [App\Http\Controllers\CategorieController::class, 'delete'])->name('restaurant.categories.delete');
        Route::post('/categories/create', [App\Http\Controllers\CategorieController::class, 'create'])->name('restaurant.categories.create');
        Route::post('/categories/update', [App\Http\Controllers\CategorieController::class, 'update'])->name('restaurant.categories.update');


        /* Courier */

        Route::get('/couriers', [App\Http\Controllers\CourierController::class, 'index'])->name('restaurant.couriers');
        Route::get('/couriers/new', [App\Http\Controllers\CourierController::class, 'new'])->name('restaurant.couriers.new');
        Route::get('/couriers/edit/{id}', [App\Http\Controllers\CourierController::class, 'edit'])->name('restaurant.couriers.edit');
        Route::get('/couriers/delete/{id}', [App\Http\Controllers\CourierController::class, 'delete'])->name('restaurant.couriers.delete');
        Route::get('/couriers/report/{id}', [App\Http\Controllers\CourierController::class, 'report'])->name('restaurant.couriers.report');
        Route::post('/couriers/create', [App\Http\Controllers\CourierController::class, 'create'])->name('restaurant.couriers.create');
        Route::post('/couriers/update', [App\Http\Controllers\CourierController::class, 'update'])->name('restaurant.couriers.update');


        /* Customers */

        Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('restaurant.customers');
        Route::get('/customers/new', [App\Http\Controllers\CustomerController::class, 'new'])->name('restaurant.customers.new');
        Route::get('/customers/edit/{id}', [App\Http\Controllers\CustomerController::class, 'edit'])->name('restaurant.customers.edit');
        Route::get('/customers/delete/{id}', [App\Http\Controllers\CustomerController::class, 'delete'])->name('restaurant.customers.delete');
        Route::post('/customers/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('restaurant.customers.create');
        Route::post('/customers/update', [App\Http\Controllers\CustomerController::class, 'update'])->name('restaurant.customers.update');


        /* Orders */
        Route::post('/orders/message', [App\Http\Controllers\OrderController::class, 'message']);
        Route::post('/orders/message2', [App\Http\Controllers\OrderController::class, 'message2']);
        Route::get('/orders/new', [App\Http\Controllers\OrderController::class, 'new'])->name('restaurant.orders.new');
        Route::get('/orders/removePOS', [App\Http\Controllers\OrderController::class, 'removePOS'])->name('restaurant.removePOS');
        Route::post('/updateCourierStatus', [App\Http\Controllers\OrderController::class, 'updateCourierStatus'])->name('updateCourierStatus');

        Route::get('/orders/{link}', [App\Http\Controllers\OrderController::class, 'index'])->name('restaurant.orders');
        //Route::get('/orders/sendCourier/{orderid}/{courier}', [App\Http\Controllers\OrderController::class, 'sendCourier'])->name('restaurant.orders.sendCourier');
        Route::post('/orders/sendCourier', [App\Http\Controllers\OrderController::class, 'sendCourier'])->name('restaurant.orders.sendCourier');
        Route::get('/orders/addPOS/{id}', [App\Http\Controllers\OrderController::class, 'addPOS'])->name('restaurant.addPOS');
        Route::get('/orders/updatePlusPOS/{id}', [App\Http\Controllers\OrderController::class, 'updatePlusPOS'])->name('restaurant.updatePlusPOS');
        Route::get('/orders/updateMinusPOS/{id}/{qty}', [App\Http\Controllers\OrderController::class, 'updateMinusPOS'])->name('restaurant.updateMinusPOS');
        Route::get('/orders/customerpos/{id}', [App\Http\Controllers\OrderController::class, 'customerpos'])->name('restaurant.customerpos');
        Route::post('/orders/customeradd', [App\Http\Controllers\OrderController::class, 'customeradd'])->name('restaurant.customers.customeradd');
        Route::post('/orders/addOrder', [App\Http\Controllers\OrderController::class, 'addOrder']);



        Route::get('/deletedOrders', [App\Http\Controllers\SiparislerController::class, 'deletedOrders'])->name('restaurant.deletedOrders');
        Route::get('/deliveredOrders', [App\Http\Controllers\SiparislerController::class, 'deliveredOrders'])->name('restaurant.deliveredOrders');

        /* Menus */

        Route::get('/menus', [App\Http\Controllers\MenuController::class, 'index'])->name('restaurant.menus');
        Route::get('/menus/new', [App\Http\Controllers\MenuController::class, 'new'])->name('restaurant.menus.new');
        Route::get('/menus/edit/{id}', [App\Http\Controllers\MenuController::class, 'edit'])->name('restaurant.menus.edit');
        Route::get('/menus/delete/{id}', [App\Http\Controllers\MenuController::class, 'delete'])->name('restaurant.menus.delete');
        Route::post('/menus/create', [App\Http\Controllers\MenuController::class, 'create'])->name('restaurant.menus.create');
        Route::post('/menus/update', [App\Http\Controllers\MenuController::class, 'update'])->name('restaurant.menus.update');


        /* Settings */

        Route::get('/entegrations', [App\Http\Controllers\MyController::class, 'entegrations'])->name('restaurant.entegrations');
        Route::post('/entegrations/update', [App\Http\Controllers\MyController::class, 'entegrastion_update'])->name('restaurant.entegrations.entegrastion_update');



        //Raporlar


        Route::post('/reports/globalFilter', [App\Http\Controllers\ReportController::class, 'globalFilter']);
        Route::post('/reports/globalFilterOrder', [App\Http\Controllers\ReportController::class, 'globalFilterOrder']);

        Route::get('/check-orders', [App\Http\Controllers\OrderController::class, 'checkOrders']);

        Route::post('/telefonsiparis/updateOrderStatus', [App\Http\Controllers\OrderController::class, 'updateOrderStatus']);

        Route::post('/trendyol/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);
        Route::post('/yemeksepeti/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);
        Route::post('/getir/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);
        Route::post('/adisyo/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);
        Route::post('/migros/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);

        Route::get('/orders/printed/{id}', [App\Http\Controllers\OrderController::class, 'printed']);
        //Get Adisyo Orders
        //Route::get('/getAdisyoOrders', [App\Http\Controllers\AdisyoController::class, 'GetOrders'])->name('getOrders');
        Route::get('/getAdisyoOrders', [App\Http\Controllers\AdisyoController::class, 'index'])->name('getOrders');
    });
});


Route::post('/order/add-online-order', [App\Http\Controllers\Api\OrderController::class, 'addOnlineOrder']);
Route::post('/order/cancel-order', [App\Http\Controllers\Api\OrderController::class, 'cancelEntegraOrder']);
/*
Route::get('/abcabc', function(){
    $order = Order::first();
    return $order->toJson();
});*/

Route::get('tt',function (){
    $order = [
        'id' => 1,
        'restaurant_id' => 1

    ];

    event(new OrderNotification($order));
});
