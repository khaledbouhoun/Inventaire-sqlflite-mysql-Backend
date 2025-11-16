<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

// API Routes
Route::middleware('api')->group(function () {

    // Test route
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working!']);
    });

    // -------------------------------
    // Auth Routes
    // -------------------------------
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'register']);
        Route::post('/login', [UserController::class, 'login']);
        Route::post('/auto-login', [UserController::class, 'autoLogin']);
        Route::delete('/logout', [UserController::class, 'logout']);

        Route::post('/update-pointage/{usr_id}', [UserController::class, 'updatePointage']);
        Route::post('/update-depot/{usr_id}', [UserController::class, 'updateDepot']);
    });

    // -------------------------------
    // Products Routes
    // -------------------------------
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']); // List all products
        Route::get('/{prd_no}', [ProductController::class, 'show']); // Get single product
        Route::post('/', [ProductController::class, 'store']); // Create product
        Route::post('/{prd_no}', [ProductController::class, 'update']); // Update product
        Route::delete('/{prd_no}', [ProductController::class, 'destroy']); // Delete product
        Route::post('/import', [ProductController::class, 'import']); // Import products from Excel
    });
});
