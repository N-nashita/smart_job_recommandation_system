<?php
use App\Http\Controllers\RecruiterAuthController;
use App\Http\Controllers\RecruiterRegisterController;
use Illuminate\Support\Facades\Route;

// public pages
Route::view('/', 'home')->name('home');

// recruiter: guests only
Route::middleware('guest:recruiter')->group(function () {
    Route::view('/recruiter/register', 'recruiter.register')->name('recruiter.register');
    Route::view('/recruiter/login', 'recruiter.login')->name('recruiter.login');

    Route::post('/recruiter/register', [RecruiterRegisterController::class, 'store'])
        ->middleware('throttle:10,1');
    Route::post('/recruiter/login', [RecruiterAuthController::class, 'login'])
        ->middleware('throttle:10,1');
});

// recruiter: logged in only
Route::middleware('auth:recruiter')->group(function () {
    Route::view('/recruiter/dashboard',  'recruiter.dashboard')->name('recruiter.dashboard');
    Route::view('/recruiter/applicants', 'recruiter.applicants')->name('recruiter.applicants');
    Route::view('/recruiter/jobs',       'recruiter.jobs')->name('recruiter.jobs');
    Route::view('/recruiter/interviews', 'recruiter.interviews')->name('recruiter.interviews');
    Route::post('/recruiter/logout', [RecruiterAuthController::class, 'logout']);
});