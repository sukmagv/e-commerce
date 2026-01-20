<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\v1\OtpController;
use App\Http\Controllers\Customer\v1\AuthController;
use App\Http\Controllers\Customer\v1\OrderController;
use App\Http\Controllers\Customer\v1\ProductController;
use App\Http\Controllers\Customer\v1\ProfileController;

Route::post('otp/send', [OtpController::class, 'sendOtp']);
Route::post('otp/verify', [OtpController::class, 'verifyOtp']);

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'getProfile');
            Route::patch('/profile', 'updateProfile');
            Route::post('/profile', 'changePassword');
        });
    });

    Route::resource('products', ProductController::class)->only(['index', 'show']);

    Route::resource('orders', OrderController::class);
    
    Route::post('orders/{order}/upload-proof', [OrderController::class, 'uploadProof']);
    Route::post('orders/{order}/pdf', [OrderController::class, 'getPdf']);

    Route::get('/bank-accounts', [OrderController::class, 'getBanks']);
});
