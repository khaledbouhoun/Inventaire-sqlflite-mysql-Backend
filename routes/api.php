<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LemplacementController;
use App\Http\Controllers\GestQrController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// -------------------------------
// Test Route
// -------------------------------
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// -------------------------------
// User Routes
// -------------------------------
Route::prefix('user')->group(function () {

    // GET all users
    Route::get('/', [UserController::class, 'index']);

    // Register
    Route::post('/register', [UserController::class, 'register']);

    // Login
    Route::post('/login', [UserController::class, 'login']);

    // Logout (no delete)
    Route::post('/logout', [UserController::class, 'logout']);

    // Update pointage
    Route::post('/update', [UserController::class, 'updateuser']);

});

// -------------------------------
// Product Routes
// -------------------------------
Route::prefix('products')->group(function () {
    // Fixed routes (no dynamic parameters) — safe to put first
    Route::post('/import', [ProductController::class, 'import']); 
    Route::get('/limit', [ProductController::class, 'limit']);
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/search/{searchQuery}', [ProductController::class, 'search']);

    // Dynamic routes (must be LAST)
    Route::get('/{prd_no}', [ProductController::class, 'show']);
    Route::post('/{prd_no}', [ProductController::class, 'update']);
    Route::delete('/{prd_no}', [ProductController::class, 'destroy']);
});



// -------------------------------
// Lemplacement Routes
// -------------------------------
Route::prefix('lemplacements')->group(function () {
    Route::get('/', [LemplacementController::class, 'index']);
    Route::post('/', [LemplacementController::class, 'store']);

    Route::get('/{id}', [LemplacementController::class, 'show']);
    Route::post('/{id}', [LemplacementController::class, 'update']);
    Route::delete('/{id}', [LemplacementController::class, 'destroy']);
});

// -------------------------------
// Gestqr Routes (Composite Key)
// -------------------------------
Route::prefix('gestqr')->group(function () {
    
    Route::get('/', [GestQrController::class, 'index']);
    Route::post('/', [GestQrController::class, 'store']);

    // MORE SPECIFIC FIRST
    Route::delete('/{lemp}/{usr}/{no}', [GestQrController::class, 'destroy']);

    // LESS SPECIFIC LAST
    Route::get('/{lemp}/{usr}', [GestQrController::class, 'show']);

});
