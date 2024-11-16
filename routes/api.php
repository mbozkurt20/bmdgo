<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/getPlatformYemekSepeti', [App\Http\Controllers\YemekSepetiController::class, 'index']);
Route::get('/getPlatformGetirYemek', [App\Http\Controllers\GetirYemekController::class, 'index']);
Route::get('/getPlatformTrendyolYemek', [App\Http\Controllers\TrendyolYemekController::class, 'index']);
Route::get('/getPlatformAdisyo', [App\Http\Controllers\AdisyoController::class, 'index']);

Route::post('/getPlatformGetirYemek/create', [App\Http\Controllers\GetirYemekController::class, 'createExe']);


Route::get('/getCourierOrder', [App\Http\Controllers\ApiController::class, 'getCourierOrder']);


//Kurye App

Route::post('/courier/login', [App\Http\Controllers\ApiController::class, 'login']);
Route::get('/courier/orders/{token}/{id}', [App\Http\Controllers\ApiController::class, 'orders']); 
Route::post('/courier/location/', [App\Http\Controllers\ApiController::class, 'location']);


Route::get('/getLocations', [App\Http\Controllers\ApiController::class, 'getLocations']);
Route::get('/courier/status/{token}/{user_id}/{status}', [App\Http\Controllers\ApiController::class, 'status']);
Route::get('/courier/situation/{token}/{user_id}', [App\Http\Controllers\ApiController::class, 'situation']);
Route::post('/courier/order_status', [App\Http\Controllers\ApiController::class, 'order_status']);


//Caller App

Route::post('/caller/login', [App\Http\Controllers\CallerController::class, 'login']);
Route::get('/caller/phoneNumber/{userId}/{phone}', [App\Http\Controllers\CallerController::class, 'getphoneNumber']);







