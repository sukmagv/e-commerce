<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('otp/send', [OtpController::class, 'sendOtp']);
Route::post('otp/verify', [OtpController::class, 'verifyOtp']);

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'getProfile');
            Route::patch('/profile', 'updateProfile');
            Route::post('/profile', 'changePassword');
        });
    });
});
