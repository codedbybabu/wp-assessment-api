<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ProductController;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- */

Route::prefix('v1')->group(function () {

    // Auth Routes
    Route::prefix('auth')->group(
        function () {
            Route::post('/login', [AuthController::class, 'login']);

            Route::middleware('jwt.auth')->group(
                function () {
                    Route::post('/logout', [AuthController::class, 'logout']);
                    Route::post('/refresh', [AuthController::class, 'refresh']);
                    Route::get('/me', [AuthController::class, 'me']);
                }
            );
        }
    );

    // Public Shop & Products (Guest & Auth handled in controller)
    Route::get('/shop', [ShopController::class, 'index']);
    Route::get('/categories', [ShopController::class, 'categories']);
    Route::get('/products/{identifier}', [ProductController::class, 'show']);

    // Protected Shop Actions
    Route::middleware('jwt.auth')->group(
        function () {
            Route::get('/products/{productId}/variations', [ProductController::class, 'variations']);

            // Premium Deals
            Route::middleware('role:gold,silver')->get(
                '/premium/deals',
                function () {
                    return response()->json(['message' => 'Premium deals for silver/gold members']);
                }
            );
        }
    );
});
