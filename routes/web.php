<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecruiterAuthController;
use App\Http\Controllers\RecruiterDashboardController;
use App\Http\Controllers\RecruiterRegisterController;
use App\Http\Controllers\RecruiterVerificationController;
use App\Http\Controllers\RecruiterApplicantsController;
use App\Http\Controllers\RecruiterInterviewsController;
use App\Http\Controllers\RecruiterJobsController;
use App\Http\Controllers\RecruiterCompanyController;
use App\Http\Controllers\CandidateDashboardController;
use App\Http\Controllers\CandidateJobsController;
use App\Http\Controllers\CandidateApplicationsController;
use App\Http\Controllers\CandidateSavedJobsController;
use App\Http\Controllers\CandidateInterviewsController;
use App\Http\Controllers\CandidateNotificationsController;
use App\Http\Controllers\CandidateProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::redirect('/login', '/candidate/login')->name('login');
Route::redirect('/register', '/candidate/register')->name('register');

/* ---------- Recruiter: guests only ---------- */
Route::middleware('guest:recruiter')->prefix('recruiter')->group(function () {
    Route::view('/register', 'recruiter.register')->name('recruiter.register');
    Route::post('/register', [RecruiterRegisterController::class, 'store'])->name('recruiter.register.store');

    Route::view('/login', 'recruiter.login')->name('recruiter.login');
    Route::post('/login', [RecruiterAuthController::class, 'login'])->name('recruiter.login.store');
});

/* ---------- Recruiter: logged in ---------- */
Route::middleware('auth:recruiter')->prefix('recruiter')->group(function () {
    Route::post('/logout', [RecruiterAuthController::class, 'logout'])->name('recruiter.logout');
    Route::get('/company', [RecruiterCompanyController::class, 'show'])->name('recruiter.company');
    Route::put('/company', [RecruiterCompanyController::class, 'update'])->name('recruiter.company.update');
    Route::get('/dashboard', [RecruiterDashboardController::class, 'index'])->name('recruiter.dashboard');
    Route::get('/applicants', [RecruiterApplicantsController::class, 'index'])->name('recruiter.applicants');
    Route::get('/jobs',       [RecruiterJobsController::class, 'index'])->name('recruiter.jobs');
    Route::get('/interviews', [RecruiterInterviewsController::class, 'index'])->name('recruiter.interviews');

    Route::view('/verification', 'recruiter.verification')->name('recruiter.verification');
    Route::post('/verification/update',  [RecruiterVerificationController::class, 'update'])->middleware('throttle:10,1')->name('recruiter.verification.update');
    Route::post('/verification/recheck', [RecruiterVerificationController::class, 'recheck'])
        ->middleware('throttle:5,1')->name('recruiter.verification.recheck');

    // verified recruiters only
    Route::middleware('recruiter.verified')->group(function () {
        Route::put('/jobs/{job}',    [RecruiterJobsController::class, 'update']);
        Route::delete('/jobs/{job}', [RecruiterJobsController::class, 'destroy']);
        Route::post('/tasks', [RecruiterDashboardController::class, 'storeTask'])->name('recruiter.tasks.store');
        Route::post('/tasks/{task}/toggle', [RecruiterDashboardController::class, 'toggleTask'])->name('recruiter.tasks.toggle');
        Route::delete('/tasks/{task}', [RecruiterDashboardController::class, 'destroyTask'])->name('recruiter.tasks.destroy');
        Route::patch('/applicants/{application}/stage',      [RecruiterApplicantsController::class, 'stage']);
        Route::post('/applicants/{application}/interview',   [RecruiterApplicantsController::class, 'interview']);
        Route::post('/jobs',                                 [RecruiterJobsController::class, 'store']);
        Route::post('/jobs/{job}/status',                    [RecruiterJobsController::class, 'status']);
        Route::patch('/interviews/{interview}',              [RecruiterInterviewsController::class, 'update']);
        Route::delete('/interviews/{interview}',             [RecruiterInterviewsController::class, 'destroy']);
    });
});

/* ---------- Candidate: logged in ---------- */
Route::middleware(['auth', 'candidate'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/dashboard', [CandidateDashboardController::class, 'index'])->name('dashboard');

    Route::get('/jobs', [CandidateJobsController::class, 'index'])->name('jobs');
    Route::get('/jobs/{job}', [CandidateJobsController::class, 'show'])->name('jobs.show');

    Route::post('/jobs/{job}/apply', [CandidateApplicationsController::class, 'store'])->name('applications.store');
    Route::post('/jobs/{job}/save',  [CandidateSavedJobsController::class, 'toggle'])->name('saved.toggle');

    Route::get('/saved',       [CandidateSavedJobsController::class, 'index'])->name('saved');
    Route::get('/applications',[CandidateApplicationsController::class, 'index'])->name('applications');
    Route::delete('/applications/{application}', [CandidateApplicationsController::class, 'destroy'])->name('applications.destroy');

    Route::get('/interviews',  [CandidateInterviewsController::class, 'index'])->name('interviews');

    Route::get('/notifications', [CandidateNotificationsController::class, 'index'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [CandidateNotificationsController::class, 'read'])->name('notifications.read');

    Route::get('/profile', [CandidateProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [CandidateProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/cv', [CandidateProfileController::class, 'cv'])->name('profile.cv');
});

/* ---------- Candidate (Breeze) ---------- */
Route::get('/dashboard', fn () => redirect()->route('candidate.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';