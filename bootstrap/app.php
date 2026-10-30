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
        $middleware->validateCsrfTokens(except: [
            'recruiter/register',
            'recruiter/login',
            'recruiter/logout',
            // 'chat',   // add if you built the chatbot route
        ]);

        // send guests of protected recruiter pages to the login page
        $middleware->redirectGuestsTo('/login-recruiter.html');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();