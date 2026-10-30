<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecruiterAuthController;
use App\Http\Controllers\RecruiterDashboardController;
use App\Http\Controllers\RecruiterRegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::redirect('/login', '/candidate/login')->name('login');
Route::redirect('/register', '/candidate/register')->name('register');

// Recruiter Registration
Route::get('/recruiter/register', function () {
    return view('recruiter.register');
})->name('recruiter.register');

Route::post('/recruiter/register', [RecruiterRegisterController::class, 'store'])
    ->name('recruiter.register.store');

// Recruiter Login
Route::get('/recruiter/login', function () {
    return view('recruiter.login');
})->name('recruiter.login');

Route::post('/recruiter/login', [RecruiterAuthController::class, 'login'])
    ->name('recruiter.login.store');

// Recruiter Logout
Route::post('/recruiter/logout', [RecruiterAuthController::class, 'logout'])
    ->middleware('auth:recruiter')
    ->name('recruiter.logout');

// Recruiter Dashboard API
Route::get('/recruiter/dashboard/data', [RecruiterDashboardController::class, 'data'])
    ->middleware('auth:recruiter')
    ->name('recruiter.dashboard.data');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';