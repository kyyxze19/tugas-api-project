<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Route untuk autentikasi (register & login) tidak memerlukan token.
| Route product dilindungi middleware auth:sanctum dan ability.
|
*/

// Auth routes (public - tanpa token)
Route::post('/registerUser', [AuthController::class, 'registerUser']);
Route::post('/loginUser', [AuthController::class, 'loginUser']);

// Product routes (protected - butuh token + ability)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('ability:product-list');

    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('ability:product-store');
});
