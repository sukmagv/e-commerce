<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\v1\AuthController;
use App\Http\Controllers\Api\Admin\V1\OrderController;
use App\Http\Controllers\Api\Admin\V1\ProductController;
use App\Http\Controllers\Api\Admin\V1\CustomerController;
use App\Http\Controllers\Api\Admin\V1\ProductCategoryController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('product-categories', ProductCategoryController::class);

    Route::resource('orders', OrderController::class);

    Route::get('/orders/{order}/proof', [OrderController::class, 'getProofDetail']);
    Route::patch('/orders/{order}/confirm', [OrderController::class, 'acceptProof']);
    Route::patch('/orders/{order}/decline', [OrderController::class, 'declineProof']);
    Route::post('/orders/excel-repot', [OrderController::class, 'excelReport']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::patch('/customers/{customer}/block', [CustomerController::class, 'block']);
    Route::patch('/customers/{customer}/unblock', [CustomerController::class, 'unblock']);
});
