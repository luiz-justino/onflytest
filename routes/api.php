<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TravelOrderController;

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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Travel Order routes (User)
    Route::prefix('travel-orders')->group(function () {
        Route::get('/', [TravelOrderController::class, 'index']);
        Route::post('/', [TravelOrderController::class, 'store']);
        Route::get('/{id}', [TravelOrderController::class, 'show']);
        Route::post('/{id}/cancel', [TravelOrderController::class, 'cancel']);
    });
    
    // Admin routes (for status updates)
    Route::prefix('admin/travel-orders')->group(function () {
        Route::get('/', [TravelOrderController::class, 'adminIndex']);
        Route::patch('/{id}/status', [TravelOrderController::class, 'updateStatus']);
    });
});



