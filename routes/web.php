<?php

use Illuminate\Support\Facades\Route;

use App\Http\Livewire\Auth\ForgotPassword;
use App\Http\Livewire\Auth\ResetPassword;
use App\Http\Livewire\Auth\SignUp;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Admin\DashboardController;
use App\Http\Livewire\Admin\ElectionCommitteeController;
use App\Http\Livewire\Admin\ElectionController;
use App\Http\Livewire\Admin\NominationController;
use App\Http\Livewire\Admin\OrganizationController;
use App\Http\Livewire\Admin\VotingEligibilityController;
use App\Http\Livewire\Admin\ResultController;
use App\Http\Livewire\Admin\ReportController;
use App\Http\Livewire\Admin\SettingsController;

use App\Http\Livewire\Billing;
use App\Http\Livewire\Profile;
use App\Http\Livewire\Tables;
use App\Http\Livewire\StaticSignIn;
use App\Http\Livewire\StaticSignUp;
use App\Http\Livewire\Rtl;

use App\Http\Livewire\LaravelExamples\UserProfile;
use App\Http\Livewire\LaravelExamples\UserManagement;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
    return redirect('/login');
});

Route::get('/sign-up', SignUp::class)->name('sign-up');
Route::get('/login', Login::class)->name('login');

Route::get('/login/forgot-password', ForgotPassword::class)->name('forgot-password');

Route::get('/reset-password/{id}',ResetPassword::class)->name('reset-password')->middleware('signed');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/election-committee', ElectionCommitteeController::class)->name('election-committee');
    Route::get('/election', ElectionController::class)->name('election');
    Route::get('/nomination', NominationController::class)->name('nomination');
    Route::get('/organization', OrganizationController::class)->name('organization');
    Route::get('/voting-eligibility', VotingEligibilityController::class)->name('voting-eligibility');

    Route::get('/result', App\Http\Livewire\Admin\ResultController::class)->name('result');
    Route::get('/result/{election}/show', App\Http\Livewire\Admin\ResultShow::class)->name('result.show');

    Route::get('/report', ReportController::class)->name('report');
    Route::get('/settings', SettingsController::class)->name('settings');
    
    Route::get('/billing', Billing::class)->name('billing');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/tables', Tables::class)->name('tables');
    Route::get('/static-sign-in', StaticSignIn::class)->name('sign-in');
    Route::get('/static-sign-up', StaticSignUp::class)->name('static-sign-up');
    Route::get('/rtl', Rtl::class)->name('rtl');
    Route::get('/laravel-user-profile', UserProfile::class)->name('user-profile');
    Route::get('/laravel-user-management', UserManagement::class)->name('user-management');
});