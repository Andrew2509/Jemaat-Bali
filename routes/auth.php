<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/admin/login', [AuthController::class, 'login']);
    });

    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');
});
