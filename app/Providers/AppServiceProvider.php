<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        \Illuminate\Support\Facades\Gate::define('review-verifications',
        fn ($user) => in_array($user->email, config('verification.admin_emails', [])));
    }
}
