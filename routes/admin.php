<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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
    Route::apiResource('products', ProductController::class);

    // User routes
    Route::apiResource('users', UserController::class);
});
