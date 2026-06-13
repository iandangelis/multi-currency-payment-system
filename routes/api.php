<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentRequestController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth.sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function(Request $request) {
        return $request->user()->load('role');
    });

    Route::post('/payment-requests', [PaymentRequestController::class, 'store']);
});
