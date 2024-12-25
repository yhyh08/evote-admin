<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Add OPTIONS route for CORS preflight
Route::options('v1/{any}', function() {
    return response()->json([], 200);
})->where('any', '.*');

Route::prefix('v1')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });

    Route::match(['post', 'options'], '/send-otp', [AuthController::class, 'sendOTP']);
    Route::match(['post', 'options'], '/verify-otp', [AuthController::class, 'verifyOTP']);
    Route::get('/check-user/{phone}', [AuthController::class, 'checkUser']);
});