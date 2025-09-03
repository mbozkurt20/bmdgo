<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'restaurant'], function () {
    Route::group(['middleware' => ['guest.restaurant']], function () {
        Route::view('register', 'restaurant.register')->name('restaurant.register');
        Route::view('login', 'restaurant.login')->name('restaurant.login');
        Route::view('payment', 'restaurant.payment')->name('restaurant.payment');
        Route::post('login', [App\Http\Controllers\RestaurantController::class, 'login'])->name('restaurant.auth');
        Route::post('register', [App\Http\Controllers\RestaurantController::class, 'register'])->name('restaurant.create');
    });

    Route::get('/order/{orderId}/print', [App\Http\Controllers\OrderController::class, 'print']);


    Route::group(['middleware' => ['restaurant.auth']], function () {
        Route::get('/printed/{orderId}', [App\Http\Controllers\OrderController::class, 'printed']);
        Route::get('/orders/ajax', [App\Http\Controllers\RestaurantController::class, 'ajax'])->name('restaurant.orders.ajax');

        Route::get('profile', [App\Http\Controllers\MyController::class, 'profile'])->name('restaurant.profile');
        Route::post('/profile', [App\Http\Controllers\MyController::class, 'profileUpdate'])->name('restaurant.profile.update');

        Route::post('/quick-order', [App\Http\Controllers\OrderController::class, 'storeQuick'])->name('quick.order.store');

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

        /* Coupons */
        Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('restaurant.coupons');
        Route::get('/coupons/new', [App\Http\Controllers\CouponController::class, 'create'])->name('restaurant.coupons.new');
        Route::get('/coupons/edit/{id}', [App\Http\Controllers\CouponController::class, 'edit'])->name('restaurant.coupons.edit');
        Route::get('/coupons/delete/{id}', [App\Http\Controllers\CouponController::class, 'delete'])->name('restaurant.coupons.delete');
        Route::post('/coupons/create', [App\Http\Controllers\CouponController::class, 'store'])->name('restaurant.coupons.create');
        Route::post('/coupons/update', [App\Http\Controllers\CouponController::class, 'update'])->name('restaurant.coupons.update');

        /* prints */
        Route::get('/prints', [App\Http\Controllers\PrinterController::class, 'index'])->name('restaurant.prints');
        Route::get('/prints/new', [App\Http\Controllers\PrinterController::class, 'create'])->name('restaurant.prints.new');
        Route::get('/prints/edit/{id}', [App\Http\Controllers\PrinterController::class, 'edit'])->name('restaurant.prints.edit');
        Route::get('/prints/delete/{id}', [App\Http\Controllers\PrinterController::class, 'delete'])->name('restaurant.prints.delete');
        Route::post('/prints/create', [App\Http\Controllers\PrinterController::class, 'store'])->name('restaurant.prints.create');
        Route::post('/prints/update', [App\Http\Controllers\PrinterController::class, 'update'])->name('restaurant.prints.update');

        /* Categories */
        Route::get('/categories', [App\Http\Controllers\CategorieController::class, 'index'])->name('restaurant.categories');
        Route::get('/categories/new', [App\Http\Controllers\CategorieController::class, 'new'])->name('restaurant.categories.new');
        Route::get('/categories/edit/{id}', [App\Http\Controllers\CategorieController::class, 'edit'])->name('restaurant.categories.edit');
        Route::get('/categories/delete/{id}', [App\Http\Controllers\CategorieController::class, 'delete'])->name('restaurant.categories.delete');
        Route::post('/categories/create', [App\Http\Controllers\CategorieController::class, 'create'])->name('restaurant.categories.create');
        Route::post('/categories/update', [App\Http\Controllers\CategorieController::class, 'update'])->name('restaurant.categories.update');

        /* Courier */
        Route::get('/get-couriers', [\App\Http\Controllers\CourierController::class, 'getCourier']);
        Route::get('/couriers', [App\Http\Controllers\CourierController::class, 'index'])->name('restaurant.couriers');
        Route::get('/couriers/new', [App\Http\Controllers\CourierController::class, 'new'])->name('restaurant.couriers.new');
        Route::get('/couriers/edit/{id}', [App\Http\Controllers\CourierController::class, 'edit'])->name('restaurant.couriers.edit');
        Route::get('/couriers/delete/{id}', [App\Http\Controllers\CourierController::class, 'delete'])->name('restaurant.couriers.delete');
        Route::get('/couriers/report/{id}', [App\Http\Controllers\CourierController::class, 'report'])->name('restaurant.couriers.report');
        Route::post('/couriers/create', [App\Http\Controllers\CourierController::class, 'create'])->name('restaurant.couriers.create');
        Route::post('/couriers/update', [App\Http\Controllers\CourierController::class, 'update'])->name('restaurant.couriers.update');

        /* Customers */
        Route::get('/get-customers', [App\Http\Controllers\CustomerController::class, 'getCustomers'])->name('restaurant.getCustomers');
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
        Route::get('/orders/sendCourier/{orderID}/{courierID}', [App\Http\Controllers\OrderController::class, 'sendCourier'])->name('restaurant.orders.sendCourier');
       // Route::post('/orders/sendCourier', [App\Http\Controllers\OrderController::class, 'sendCourier'])->name('restaurant.orders.sendCourier');
        Route::get('/orders/addPOS/{id}', [App\Http\Controllers\OrderController::class, 'addPOS'])->name('restaurant.addPOS');
        Route::get('/get-pos-items', [App\Http\Controllers\OrderController::class, 'getPosItems']);

        Route::get('/restaurant/orders/cartItems', function () {
            return view('restaurant.orders._cart-items');
        });

        Route::get('/orders/updatePlusPOS/{id}', [App\Http\Controllers\OrderController::class, 'updatePlusPOS'])->name('restaurant.updatePlusPOS');
        Route::get('/orders/updateMinusPOS/{id}/{qty}', [App\Http\Controllers\OrderController::class, 'updateMinusPOS'])->name('restaurant.updateMinusPOS');
        Route::get('/orders/customerpos/{id}', [App\Http\Controllers\OrderController::class, 'customerpos'])->name('restaurant.customerpos');
        Route::post('/orders/customeradd', [App\Http\Controllers\OrderController::class, 'customeradd'])->name('restaurant.customers.customeradd');
        Route::post('/orders/addOrder', [App\Http\Controllers\OrderController::class, 'addOrder']);

        Route::get('/deletedOrders', [App\Http\Controllers\SiparislerController::class, 'deletedOrders'])->name('restaurant.deletedOrders');
        Route::get('/deliveredOrders', [App\Http\Controllers\SiparislerController::class, 'deliveredOrders'])->name('restaurant.deliveredOrders');

        Route::get('/menus', [App\Http\Controllers\MenuController::class, 'index'])->name('restaurant.menus');
        Route::get('/menu', [App\Http\Controllers\MenuController::class, 'show'])->name('restaurant.menu');
        Route::post('/menus/select', [App\Http\Controllers\MenuController::class, 'store'])->name('restaurant.menu.template.select');

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
        Route::post('/trendyol/updateOrderStatus', [App\Http\Controllers\TrendyolYemekController::class, 'updateOrderStatus']);
        Route::post('/yemeksepeti/updateOrderStatus', [App\Http\Controllers\YemekSepetiController::class, 'updateOrder']);
        Route::post('/getir/updateOrderStatus', [App\Http\Controllers\GetirYemekController::class, 'updateOrder']);
        Route::post('/adisyo/updateOrderStatus', [App\Http\Controllers\AdisyoController::class, 'updateOrder']);


        Route::get('/printed/{orderId}', [App\Http\Controllers\OrderController::class, 'printed']);
        //Get Adisyo Orders
        //Route::get('/getAdisyoOrders', [App\Http\Controllers\AdisyoController::class, 'GetOrders'])->name('getOrders');
        Route::get('/getAdisyoOrders', [App\Http\Controllers\AdisyoController::class, 'index'])->name('getOrders');
    });
});
