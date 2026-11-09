<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['recruiter.verified' => \App\Http\Middleware\EnsureRecruiterVerified::class]);
        $middleware->redirectGuestsTo(fn (\Illuminate\Http\Request $request) =>
            $request->is('recruiter/*') ? route('recruiter.login') : route('login'));

        $middleware->redirectUsersTo(fn (\Illuminate\Http\Request $request) =>
            $request->is('recruiter/*') ? route('recruiter.dashboard') : route('dashboard'));
    
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
    