<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::prefix('categories')->group(function () {
    Route::get('trashed', [CategoryController::class, 'trashed']);
    Route::patch('{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('{id}/force-delete', [CategoryController::class, 'forceDelete']);
});
Route::apiResource('categories', CategoryController::class);