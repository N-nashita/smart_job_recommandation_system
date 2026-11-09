<?php

use App\Http\Controllers\Admin\VerificationReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecruiterAuthController;
use App\Http\Controllers\RecruiterDashboardController;
use App\Http\Controllers\RecruiterRegisterController;
use App\Http\Controllers\RecruiterVerificationController;
use App\Http\Controllers\RecruiterApplicantsController;
use App\Http\Controllers\RecruiterInterviewsController;
use App\Http\Controllers\RecruiterJobsController;
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

    Route::get('/dashboard', [RecruiterDashboardController::class, 'index'])->name('recruiter.dashboard');
    Route::get('/applicants', [RecruiterApplicantsController::class, 'index'])->name('recruiter.applicants');
    Route::get('/jobs',       [RecruiterJobsController::class, 'index'])->name('recruiter.jobs');
    Route::get('/interviews', [RecruiterInterviewsController::class, 'index'])->name('recruiter.interviews');

    Route::view('/verification', 'recruiter.verification')->name('recruiter.verification');
    Route::post('/verification/upload', [RecruiterVerificationController::class, 'upload'])
        ->middleware('throttle:10,1')->name('recruiter.verification.upload');
    Route::post('/verification/recheck', [RecruiterVerificationController::class, 'recheck'])
        ->middleware('throttle:5,1')->name('recruiter.verification.recheck');

    // verified recruiters only
    Route::middleware('recruiter.verified')->group(function () {
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

/* ---------- Admin: company review ---------- */
Route::middleware(['auth', 'can:review-verifications'])->prefix('admin')->group(function () {
    Route::get('/verifications', [VerificationReviewController::class, 'index'])->name('admin.verifications');
    Route::get('/verifications/{recruiter}/document', [VerificationReviewController::class, 'document'])->name('admin.verifications.document');
    Route::post('/verifications/{recruiter}/approve', [VerificationReviewController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('/verifications/{recruiter}/reject', [VerificationReviewController::class, 'reject'])->name('admin.verifications.reject');
});

/* ---------- Candidate (Breeze) ---------- */
Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';