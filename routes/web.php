<?php

use App\Helpers\NotificationHelper;
use App\Http\Controllers\TamiPaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahalleController;

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

Route::get('/restaurant/orders/new', [MahalleController::class, 'create']);
Route::get('/restaurant/couriers', [App\Http\Controllers\CourierController::class, 'index'])->name('restaurant.couriers.index');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/partner', [App\Http\Controllers\HomeController::class, 'dealer'])->name('dealer');
Route::post('/new-partner', [App\Http\Controllers\HomeController::class, 'createDealerRequest'])->name('createDealerRequest');
Route::get('/get-districts/{cityId}', [App\Http\Controllers\HomeController::class, 'getDistricts']);

Route::post('/payment/callback', [TamiPaymentController::class, 'callback'])
    ->name('payment.callback')
    ->withoutMiddleware(['web']); // web middleware'ını tamamen kaldır

Route::get('/payment/success', [TamiPaymentController::class, 'successPage'])
    ->name('payment.success')
    ->middleware('auth:admin'); // Sadece auth middleware ekle

Route::get('/payment/fail', [TamiPaymentController::class, 'failPage'])
    ->name('payment.fail')
    ->middleware('auth:admin'); // Sadece auth middleware ekle

Auth::routes();

Route::get('/payment/success', [TamiPaymentController::class, 'successPage'])->name('payment.success');
Route::get('/payment/fail', [TamiPaymentController::class, 'failPage'])->name('payment.fail');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.dashboard');

include __DIR__ . '/app/superAdminRoutes.php';
include __DIR__ . '/app/adminRoutes.php';
include __DIR__ . '/app/restaurantRoutes.php';
include __DIR__ . '/app/partnerRoutes.php';

Route::post('/order/add-online-order', [App\Http\Controllers\Api\OrderController::class, 'addOnlineOrder']);
Route::post('/order/cancel-order', [App\Http\Controllers\Api\OrderController::class, 'cancelEntegraOrder']);
