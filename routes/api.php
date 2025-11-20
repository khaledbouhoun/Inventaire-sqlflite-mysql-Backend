<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LemplacementController;
use App\Http\Controllers\GestqrController;

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
    Route::get('/', [UserController::class, 'index']);           // Get all users
    Route::post('/', [UserController::class, 'register']);       // Register
    Route::post('/login', [UserController::class, 'login']);     // Login
    Route::post('/auto-login', [UserController::class, 'autoLogin']);
    Route::delete('/logout', [UserController::class, 'logout']); // Logout

    // User specific actions
    Route::post('/update-pointage/{usr_no}', [UserController::class, 'updatePointage']);
    Route::post('/update-depot/{usr_no}', [UserController::class, 'updateDepot']);
});

// -------------------------------
// Product Routes
// -------------------------------
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::post('/import', [ProductController::class, 'import']); // Excel Import

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
    // 1. List & Filter
    // Use query params: ?usr_no=1&lemp_no=5&date_from=...
    Route::get('/', [GestqrController::class, 'index']);

    // 2. Create
    Route::post('/', [GestqrController::class, 'store']);

    // 3. Show Single Record (Composite Key)
    // Matches: /gestqr/5/10/2 (Location 5, User 10, Sequence 2)
    Route::get('/{lemp}/{usr}/{no}', [GestqrController::class, 'show'])
        ->where(['lemp' => '[0-9]+', 'usr' => '[0-9]+', 'no' => '[0-9]+']);

    // 4. Delete Record (Composite Key)
    Route::delete('/{lemp}/{usr}/{no}', [GestqrController::class, 'destroy'])
        ->where(['lemp' => '[0-9]+', 'usr' => '[0-9]+', 'no' => '[0-9]+']);
});
