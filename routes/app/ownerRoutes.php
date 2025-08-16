<?php

use Illuminate\Support\Facades\Route;


Route::get('/owner', [\App\Http\Controllers\Owner\OwnerController::class, 'showLoginForm']);
Route::post('/owner/login', [\App\Http\Controllers\Owner\OwnerController::class, 'login'])->name('owner.auth');

Route::group(['middleware' => ['owner.auth']], function () {
    // Kullanıcı (dealer) kaydı
    Route::get('/owner/dealer/create', [\App\Http\Controllers\Owner\OwnerController::class, 'showDealerForm'])->name('dealer.create');
    Route::post('/owner/dealer/create', [\App\Http\Controllers\Owner\OwnerController::class, 'storeDealer'])->name('dealer.store');
});

Route::group(['middleware' => ['owner.auth','dealer.auth']], function () {
    Route::get('/owner/dashboard', [\App\Http\Controllers\Owner\OwnerController::class, 'dashboard'])->name('dashboard');

    // Kontör ekleme
    Route::get('/owner/admin/topup', [\App\Http\Controllers\Owner\OwnerController::class, 'showTopUpForm'])->name('admin.topup.form');
    Route::post('/owner/admin/topup', [\App\Http\Controllers\Owner\OwnerController::class, 'topUp'])->name('admin.topup');
});
