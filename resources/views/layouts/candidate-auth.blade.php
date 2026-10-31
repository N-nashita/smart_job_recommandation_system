<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — JobSphere</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style/home-candidate.css') }}">
    <link rel="stylesheet" href="{{ asset('style/register-candidate.css') }}">
    @stack('styles')
</head>

<body>
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <defs>
            <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" /></symbol>
            <symbol id="i-eye" viewBox="0 0 24 24"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" /><circle cx="12" cy="12" r="3" /></symbol>
            <symbol id="i-eye-off" viewBox="0 0 24 24"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.5 6.6A17 17 0 0 0 2 12s3.6 7 10 7a10 10 0 0 0 4.4-1M9.9 9.9a3 3 0 0 0 4.2 4.2" /></symbol>
            <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M7 17 17 7M8 7h9v9" /></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 4.5-3.4 7.7-8 9-4.6-1.3-8-4.5-8-9V6z" /><path d="M9 12l2 2 4-4" /></symbol>
        </defs>
    </svg>

    <nav>
        <a href="{{ url('/') }}" class="logo"><i></i> JobSphere</a>
        <div class="links">
            <a href="{{ url('/') }}#jobs">Jobs</a>
            <a href="{{ url('/') }}#how">How it works</a>
            <a href="{{ url('/') }}#trust">Trust &amp; safety</a>
        </div>
        <div class="nav-cta">
            @yield('nav_cta')
        </div>
    </nav>

    <main class="wrap">
        @yield('content')
    </main>

    <footer class="slim">
        <div class="wrap foot">
            <span>{{ date('Y') }}</span> JobSphere. All rights reserved.</span>
            <span>Privacy · Terms · Contact</span>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>