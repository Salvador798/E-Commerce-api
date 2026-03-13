<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
| User registration and login
*/

Route::prefix('auth')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('password/forgot', [PasswordResetController::class, 'sendResetCode']);
    Route::post('password/reset', [PasswordResetController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Visible catalog without authentication
*/

Route::prefix('public')->group(function () {

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
});


/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
| Authenticated customer endpoints
*/

Route::middleware('auth:sanctum')->prefix('customer')->group(function () {

    // Auth
    Route::post('logout', [AuthController::class, 'logout']);

    // Address - manage user addresses
    Route::get('address', [AddressController::class, 'index']);
    Route::post('address', [AddressController::class, 'store']);
    Route::put('address/{address}', [AddressController::class, 'update']);
    Route::delete('address/{address}', [AddressController::class, 'destroy']);

    // Cart - manage shopping cart
    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart', [CartController::class, 'store']);
    Route::put('cart/item/{item}', [CartController::class, 'update']);
    Route::delete('cart/item/{item}', [CartController::class, 'destroy']);
    Route::delete('cart/empty', [CartController::class, 'empty']);

    // Checkout - process checkout
    Route::post('checkout', [CheckoutController::class, 'checkout']);

    // Orders - view and create orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    // Payments - make payments
    Route::post('payments', [PaymentController::class, 'store']);

    // Shipment tracking
    Route::get('shipment/{order}', [ShipmentController::class, 'show']);
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| E-commerce administration
*/

Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('admin')->group(function () {

    // Sales
    Route::get('sales/summary', [DashboardController::class, 'summarySale']);
    Route::get('sales/per-month', [DashboardController::class, 'SalesPerMonth']);
    Route::get('sales/top-products', [DashboardController::class, 'BestsellingProducts']);

    // Orders
    Route::get('orders/summary', [DashboardController::class, 'summaryOrders']);
    Route::get('orders/per-status', [DashboardController::class, 'OrderByStatus']);

    // Stock
    Route::get('stock/summary', [DashboardController::class, 'summaryStock']);
    Route::get('stock/low', [DashboardController::class, 'stockLow']);

    // Users - CRUD operations
    Route::apiResource('users', UserController::class);

    // Categories - CRUD and toggle status
    Route::apiResource('categories', CategoryController::class);
    Route::patch('categories/{category}/status', [CategoryController::class, 'toggleStatus']);

    // Products - CRUD and toggle status
    Route::apiResource('products', ProductController::class);
    Route::patch('products/{product}/status', [ProductController::class, 'toggleStatus']);

    // Inventories - CRUD operations
    Route::apiResource('inventories', InventoryController::class);

    // Orders - view all orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    // Payments - view all payments
    Route::get('payments', [PaymentController::class, 'index']);

    // Shipments - create and update shipments
    Route::get('shipments', [ShipmentController::class, 'index']);
    Route::post('shipments', [ShipmentController::class, 'store']);
    Route::put('shipments/{shipment}/status', [ShipmentController::class, 'update']);

    // Reports
    Route::get('sales/day', [ReportController::class, 'salesPerDay']);
    Route::get('sales/category', [ReportController::class, 'salesByCategory']);
    Route::get('sales/customer', [ReportController::class, 'salesPerCustomer']);
    Route::get('sales/product', [ReportController::class, 'salesPerProduct']);
    Route::get('sales/range', [ReportController::class, 'salesByRank']);
    Route::get('sales/method', [ReportController::class, 'salesByPaymentMethod']);
    Route::get('sales/status', [ReportController::class, 'salesByStatusOrder']);
    Route::get('sales/region', [ReportController::class, 'salesByRegion']);
});
