<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\DispatchController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
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
    Route::get('/me', [AuthController::class, 'me']); // Get authenticated user/dealer info

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // User routes (for Users: admin/accountant/sales/dispatch)
    Route::prefix('users')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'update']);
    });

    // Dealer routes
    Route::prefix('dealers')->group(function () {
        Route::get('/profile', [DealerController::class, 'profile']); // Dealers only
        Route::put('/profile', [DealerController::class, 'update']); // Dealers only
        Route::get('/', [DealerController::class, 'index']); // Users: admin/sales/accountant
        Route::get('/{id}', [DealerController::class, 'show']); // Users: admin/sales/accountant
    });

    // Product routes
    Route::prefix('products')->group(function () {
        Route::get('/categories', [ProductController::class, 'categories']); // All authenticated users
        Route::get('/', [ProductController::class, 'index']); // All authenticated users
        Route::get('/{id}', [ProductController::class, 'show']); // All authenticated users
        Route::post('/', [ProductController::class, 'store']); // Users: admin/sales_officer
        Route::put('/{id}', [ProductController::class, 'update']); // Users: admin/sales_officer
        Route::delete('/{id}', [ProductController::class, 'destroy']); // Users: admin only
    });

    // Cart routes (Dealers only)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']); // Get cart items
        Route::post('/', [CartController::class, 'store']); // Add product to cart
        Route::put('/{id}', [CartController::class, 'update']); // Update cart item quantity
        Route::delete('/{id}', [CartController::class, 'destroy']); // Remove product from cart
        Route::delete('/', [CartController::class, 'clear']); // Clear all cart items
    });

    // Order routes
    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'create']); // Dealers only
        Route::get('/', [OrderController::class, 'index']); // All authenticated users (filtered by role)
        Route::get('/{id}', [OrderController::class, 'show']); // All authenticated users (filtered by role)
        Route::put('/{id}', [OrderController::class, 'update']); // Users: admin/sales_officer
        Route::delete('/{id}', [OrderController::class, 'destroy']); // Users: admin only
    });

    // Dispatch routes
    Route::prefix('dispatches')->group(function () {
        Route::post('/', [DispatchController::class, 'create']); // Users: admin/dispatch_officer
        Route::put('/{id}', [DispatchController::class, 'update']); // Users: admin/dispatch_officer
    });
    Route::get('/orders/{orderId}/dispatches', [DispatchController::class, 'index']); // All authenticated users (filtered by role)

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']); // Users: admin/accountant
        Route::get('/{id}', [PaymentController::class, 'show']); // Users: admin/accountant
        Route::post('/', [PaymentController::class, 'store']); // Users: admin/accountant
    });

    // Report routes
    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales']); // Users: admin/accountant
        Route::get('/dealer-performance', [ReportController::class, 'dealerPerformance']); // Users: admin/sales_officer
    });
});

