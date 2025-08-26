<?php

use Illuminate\Support\Facades\Route;

Route::post('/courier/login', [\App\Http\Controllers\Api\v2\Courier\Auth\LoginController::class, 'login']); // Giriş için API Route
Route::post('/courier/register', [\App\Http\Controllers\Api\v2\Courier\Auth\LoginController::class, 'register']); // Giriş için API Route
Route::get('/courier/get-domain/{code}', [\App\Http\Controllers\Api\v2\Courier\Auth\LoginController::class, 'domain']); // Giriş için API Route

// Auth middleware ile korunan route'lar
Route::middleware('jwt.courier')->group(function () {
    Route::prefix('courier')->group(function () {
        Route::post('auth/logout', [\App\Http\Controllers\Api\v2\Courier\Auth\LoginController::class, 'logout']);

        Route::get('/profile', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'index']);
        Route::put('/profile', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'update']);
        Route::post('/profile/update-password', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'updatePassword']);
        Route::post('/profile/update-status', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'updateStatus']);
        Route::post('/location', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'updateLocation']);
        Route::get('/orders', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'index']);
        Route::get('/orders/report', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'reports']);
        Route::post('/order/{orderId}/status', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'status']);
        Route::post('/order/{orderId}/verify', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'verifyOrderCode']);
    });
});
