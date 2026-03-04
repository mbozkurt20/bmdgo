<?php

use App\Helpers\NotificationHelper;
use App\Http\Controllers\TamiPaymentController;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;

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
Route::get('/restaurant/{restaurantId}/menu', [App\Http\Controllers\MenuController::class, 'show'])->name('restaurant.menu');
Route::get('/pSwAIk2Jo6edRFcHME/gpskurye', [\App\Http\Controllers\GpsYemekController::class,'index'])->name('restaurant.couriers.index');
Route::get('/pSwAIk2Jo6edRFcHME/jobs', [\App\Http\Controllers\JobController::class,'index']);

Auth::routes();

include __DIR__ . '/app/superAdminRoutes.php';
include __DIR__ . '/app/adminRoutes.php';
include __DIR__ . '/app/restaurantRoutes.php';
include __DIR__ . '/app/partnerRoutes.php';

Route::post('/entegra/add-order', [App\Http\Controllers\EntegraWebhookController::class, 'addOrder']);
Route::post('/entegra/cancel-order', [App\Http\Controllers\EntegraWebhookController::class, 'cancelOrder']);

Route::post('/paytr/callback', [\App\Http\Controllers\PayTrPaymentController::class, 'paytrCallback'])->name('paytr.callback');
Route::get('/paytr/success', [\App\Http\Controllers\PayTrPaymentController::class, 'payTrSuccess'])->name('paytr.success');
Route::get('/paytr/fail',    [\App\Http\Controllers\PayTrPaymentController::class, 'payTrFail'])->name('paytr.fail');
Route::get('/payment/success', [TamiPaymentController::class, 'successPage'])->name('payment.success');
Route::get('/payment/fail', [TamiPaymentController::class, 'failPage'])->name('payment.fail');
Route::post('/payment/callback', [TamiPaymentController::class, 'callback'])
    ->name('payment.callback')
    ->withoutMiddleware(['web']); // web middleware'ını tamamen kaldır

Route::get('/payment/success', [TamiPaymentController::class, 'successPage'])
    ->name('payment.success')
    ->middleware('auth:admin'); // Sadece auth middleware ekle

Route::get('/payment/fail', [TamiPaymentController::class, 'failPage'])
    ->name('payment.fail')
    ->middleware('auth:admin'); // Sadece auth middleware ekle

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.dashboard');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/get-districts/{cityId}', [App\Http\Controllers\HomeController::class, 'getDistricts']);
