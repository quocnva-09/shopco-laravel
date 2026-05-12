<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Category routes
    Route::prefix('categories')->group(function () {
        Route::get('trashed', [CategoryController::class, 'trashed']);
        Route::patch('{id}/restore', [CategoryController::class, 'restore']);
        Route::delete('{id}/force-delete', [CategoryController::class, 'forceDelete']);
    });
    Route::apiResource('categories', CategoryController::class);

    // Product routes
    Route::prefix('products')->group(function () {
        Route::get('trashed', [ProductController::class, 'trashed']);
        Route::patch('{id}/restore', [ProductController::class, 'restore']);
        Route::delete('{id}/force-delete', [ProductController::class, 'forceDelete']);
    });
    Route::apiResource('products', ProductController::class);

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('trashed', [UserController::class, 'trashed']);
        Route::patch('{id}/restore', [UserController::class, 'restore']);
        Route::delete('{id}/force-delete', [UserController::class, 'forceDelete']);
    });
    Route::apiResource('users', UserController::class);

    // Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'adminIndex']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus']);
    });
});
