<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserCategoryController;
use App\Http\Controllers\Api\UserWalletTypeController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // User Categories
    Route::apiResource('categories', UserCategoryController::class);
    
    // User Wallet Types
    Route::apiResource('wallet-types', UserWalletTypeController::class);
    
    // Wallets
    Route::apiResource('wallets', WalletController::class);
    
    // Transactions
    Route::apiResource('transactions', TransactionController::class);
    Route::get('/transactions-summary', [TransactionController::class, 'summary']);
});
