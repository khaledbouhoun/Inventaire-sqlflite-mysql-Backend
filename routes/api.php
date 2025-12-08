<?php

use App\Http\Controllers\PointageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LemplacementController;
use App\Http\Controllers\GestQrController;
use App\Http\Controllers\InvontaieController;

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
    Route::get('/', [UserController::class, 'index']);
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/update', [UserController::class, 'updateuser']);
});

// -------------------------------
// Product Routes
// -------------------------------
Route::prefix('products')->group(function () {
    Route::post('/import', [ProductController::class, 'import']);
    Route::get('/limit', [ProductController::class, 'limit']);
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/search/{searchQuery}', [ProductController::class, 'search']);
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
    Route::delete('/{lemp}/{usr}/{no}', [GestQrController::class, 'destroy']);
    Route::get('/{lemp}/{usr}', [GestQrController::class, 'show']);
});

// -------------------------------
// Pointage Routes
// -------------------------------
Route::prefix('pointages')->group(function () {
    Route::get('/', [PointageController::class, 'index']);
    Route::post('/', [PointageController::class, 'store']);
    Route::get('/{id}', [PointageController::class, 'show']);
    Route::post('/{id}', [PointageController::class, 'update']);
    Route::delete('/{id}', [PointageController::class, 'destroy']);
});

// -------------------------------
// Invontaie Routes
// -------------------------------
Route::prefix('invontaies')->group(function () {
    Route::get('/', [InvontaieController::class, 'index']);
    Route::post('/', [InvontaieController::class, 'store']);
    Route::get('/{id}', [InvontaieController::class, 'show']);
    Route::post('/{id}', [InvontaieController::class, 'update']);
    Route::delete('/{id}', [InvontaieController::class, 'destroy']);
});

