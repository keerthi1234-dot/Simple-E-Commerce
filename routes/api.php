<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
// For login & register
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    // ----------------------
    // PRODUCT ROUTES
    // ----------------------
    Route::get('/products', [ProductController::class, 'index']);

    // ----------------------
    // CART ROUTES
    // ----------------------
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);

    // ----------------------
    // CHECKOUT / ORDER ROUTES
    // ----------------------
    Route::post('/checkout', [OrderController::class, 'checkout']);

    // ----------------------
    // ADMIN — LIST ALL ORDERS
    // ----------------------
    Route::get('/admin/orders', [OrderController::class, 'allOrders']);
});

