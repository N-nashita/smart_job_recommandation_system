<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecruiterRegisterController;
use App\Http\Controllers\RecruiterAuthController;

Route::get('/', function () {
    return response()->file(public_path('index.html')); 
});

Route::post('/recruiter/register', [RecruiterRegisterController::class, 'store'])
    ->middleware('throttle:10,1');   // max 10 attempts per minute

Route::post('/recruiter/login', [RecruiterAuthController::class, 'login'])->middleware('throttle:5,1'); // 5 tries/min
Route::post('/recruiter/logout', [RecruiterAuthController::class, 'logout']);