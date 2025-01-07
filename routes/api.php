<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Livewire\Admin\ElectionController;
use App\Http\Livewire\Admin\DashboardController;
use App\Http\Livewire\Admin\OrganizationController;
use App\Http\Livewire\Admin\NominationController;
use App\Http\Livewire\Admin\ResultController;

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
    Route::get('/all-candidates', [NominationController::class,'getAllCandidates']);
    Route::get('/candidate/{id}', [NominationController::class, 'getCandidateData']);
    Route::post('candidate/{id}/vote', [NominationController::class, 'vote']);
    Route::get('/election-candidates/{election_id}', function ($election_id) {
        $candidates = DB::table('candidates')
            ->where('election_id', $election_id)
            ->get();
        
        return response()->json(['candidates' => $candidates]);
    });

    Route::get('/candidate/status/{status?}', [NominationController::class, 'getCandidatesByStatus']);

    Route::get('/candidate/progress/{user_id}', [NominationController::class, 'getSavedProgress']);

    Route::post('/save-candidate-info', [NominationController::class, 'saveCandidateInfo']);

    Route::post('/save-nominations', [NominationController::class, 'saveNomination']);

    Route::post('/save-candidate-documents', [NominationController::class, 'saveDocuments']);

    Route::get('/get-candidateid', [NominationController::class, 'getCandidateId']);

    Route::get('/election/results', [ResultController::class, 'getAllResults']);

    Route::post('/save-organizations', [OrganizationController::class, 'saveOrganization']);
});
