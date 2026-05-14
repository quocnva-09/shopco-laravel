<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Middleware\CheckAdminMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->middleware('throttle:10,1');
    Route::post('logout/all', [AuthController::class, 'logoutAll'])->middleware('throttle:5,1');
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
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus']);
    });
});

// Public routes
// Authentication Routes
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:3,1');

// Category routes
// Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

// Product routes
// Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
