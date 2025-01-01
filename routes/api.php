<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Livewire\Admin\ElectionController;
use App\Http\Livewire\Admin\DashboardController;
use App\Http\Livewire\Admin\OrganizationController;
use App\Http\Livewire\Admin\NominationController;

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
    // Route::post('/save-nominee', [NominationController::class, 'saveNominee']);
    // Route::post('/save-candidate', [NominationController::class, 'saveCandidate']);
    // Route::post('/save-candidate-docs', [NominationController::class, 'saveCandidateDocs']);
    
    // Route::post('/candidates', [NominationController::class, 'store']);
    // Route::post('/candidate-additional-info/{id}', [NominationController::class, 'updateAdditionalInfo']);
    
    // // Nomination routes
    // Route::post('/nominations', [NominationController::class, 'store']);
    // // Document routes
    // Route::post('/candidate-documents', [DocumentController::class, 'store']);

    Route::get('/election-candidates/{election_id}', function ($election_id) {
        $candidates = DB::table('candidates')
            ->where('election_id', $election_id)
            ->get();
        
        return response()->json(['candidates' => $candidates]);
    });

    Route::get('/candidate/status/{status?}', [NominationController::class, 'getCandidatesByStatus']);

    // // Step 1-3: Candidate Information (combines into one table)
    // Route::post('/candidate/step1', [NominationController::class, 'saveStep1']); // Election topic
    // Route::post('/candidate/step2', [NominationController::class, 'saveStep2']); // Basic candidate info
    // Route::post('/candidate/step3', [NominationController::class, 'saveStep3']); // Additional candidate info

    // // Step 4: Nominee Information
    // Route::post('/nomination/step4', [NominationController::class, 'saveStep4']); // Nomination details

    // // Step 5: Documentation
    // Route::post('/candidate/step5/documents', [NominationController::class, 'saveStep5']); // Upload documents

    // Optional: Get saved progress
    Route::get('/candidate/progress/{user_id}', [NominationController::class, 'getSavedProgress']);

    Route::post('/save-candidate-info', [NominationController::class, 'saveCandidateInfo']);
});
