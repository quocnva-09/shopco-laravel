<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'getMyInfo']);

    // Cart routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('add', [CartController::class, 'add']);
        Route::put('items/{itemId}', [CartController::class, 'updateItem']);
        Route::delete('items/{itemId}', [CartController::class, 'removeItem']);
        Route::get('items/count', [CartController::class, 'countCartItems']);
        Route::delete('/', [CartController::class, 'clear']);
    });

    // Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\OrderController::class, 'store']);
        Route::get('/{order}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
        Route::patch('/{order}/status', [\App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
    });
});

// Public routes
// Authentication Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Category routes
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

// Product routes
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
