<?php

use App\Http\Controllers\Admin\v1\ProductCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\v1\AuthController; // masih belum dipisah
use App\Http\Controllers\Admin\v1\ProductController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('product-categories', ProductCategoryController::class);
});
