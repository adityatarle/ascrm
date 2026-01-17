<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\DispatchController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/dealers/register', [DealerController::class, 'register']);

// Protected routes (require Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dealer routes
    Route::get('/dealers/profile', [DealerController::class, 'profile']);
    Route::put('/dealers/profile', [DealerController::class, 'update']);

    // Order routes
    Route::post('/orders', [OrderController::class, 'create']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Dispatch routes
    Route::post('/dispatches', [DispatchController::class, 'create']);
    Route::get('/orders/{orderId}/dispatches', [DispatchController::class, 'index']);
    Route::put('/dispatches/{id}', [DispatchController::class, 'update']);
});

