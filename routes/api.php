<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Livewire\Admin\ElectionController;
use App\Http\Livewire\Admin\DashboardController;
use App\Http\Livewire\Admin\OrganizationController;

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
    Route::get('/user-info/{phone}', [AuthController::class, 'getUserInfo']);
    Route::get('/election-info/{id}', [ElectionController::class, 'getElectionInfo']);
    Route::get('/all-elections', [ElectionController::class,'getAllElections']);
    Route::get('/latest-election', [DashboardController::class, 'getLatestElection']);
    Route::get('/all-organizations', [OrganizationController::class,'getAllOrganizations']);
    Route::get('/organization-info/{id}', [OrganizationController::class, 'getOrganizationInfo']);
});