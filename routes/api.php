<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentRequestController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });

    Route::get('/payment-requests', [PaymentRequestController::class, 'index']);
    Route::get('/payment-requests/{paymentRequest}', [PaymentRequestController::class, 'show']);
    Route::post('/payment-requests', [PaymentRequestController::class, 'store']);
    Route::patch('/payment-requests/{paymentRequest}/approve', [PaymentRequestController::class, 'approve']);
    Route::patch('/payment-requests/{paymentRequest}/reject', [PaymentRequestController::class, 'reject']);
});
