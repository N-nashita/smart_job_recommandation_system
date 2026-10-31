@extends('layouts.candidate-auth')

@section('title', 'Candidate login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('style/login-candidate.css') }}">
@endpush

@section('nav_cta')
    <a href="{{ route('recruiter.login') }}" class="btn ghost">Recruiter login</a>
    <a href="{{ route('candidate.register') }}" class="btn primary">Create account</a>
@endsection

@section('content')
<div class="reg login">
    <!-- left panel -->
    <aside class="aside">
        <div class="eyebrow">For job seekers</div>
        <h1>Welcome <em>back.</em></h1>
        <p class="lead">Pick up where you left off: check your matched jobs, track applications and see who viewed your profile.</p>

        <ul class="perks">
            <li><svg class="ic"><use href="#i-check" /></svg> Jobs ranked by your compatibility score</li>
            <li><svg class="ic"><use href="#i-check" /></svg> Alerts for new jobs in your category</li>
            <li><svg class="ic"><use href="#i-check" /></svg> Know when a recruiter views your profile</li>
        </ul>

        <div class="vcard" aria-label="Example candidate dashboard">
            <span class="vlabel">Example dashboard</span>
            <div class="vhead">
                <div class="avatar mint">A</div>
                <div class="vname">
                    <strong><span>Amina Rahman</span>
                        <span class="vbadge"><svg class="ic"><use href="#i-shield" /></svg> Candidate</span></strong>
                    <small>IT/Telecommunication · Fresher</small>
                </div>
            </div>
            <div class="vstats">
                <div><b>24</b><span>Matched jobs</span></div>
                <div><b>3</b><span>Applications</span></div>
                <div><b>9</b><span>Profile views</span></div>
            </div>
        </div>
    </aside>

    <!-- form card -->
    <div class="glass form-card" id="card">
        <h2>Log in to your account</h2>
        <p class="sub">New here? <a href="{{ route('candidate.register') }}">Create an account</a></p>

        <form id="login" class="login-form" method="POST"
              action="{{ route('candidate.login.store') }}"
              data-action="{{ route('candidate.login.store') }}" novalidate>
            @csrf

            <div class="f"><label for="email">Email</label>
                <input id="email" name="email" type="email" autocomplete="username" placeholder="you@email.com">
                <span class="msg-err"></span>
            </div>

            <div class="f"><label for="password">Password</label>
                <div class="pw">
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Your password">
                    <button type="button" class="eye" id="eye" aria-label="Show password"><svg class="ic"><use href="#i-eye" /></svg></button>
                </div>
                <span class="msg-err"></span>
            </div>

            <div class="row-between">
                <label class="cb"><input type="checkbox" name="remember"> <span>Keep me logged in</span></label>
                <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
            </div>

            <div class="banner" id="banner" role="alert" hidden></div>

            <button type="submit" class="btn primary block" id="submit">Log in <svg class="ic"><use href="#i-arrow" /></svg></button>
        </form>

        <p class="switch-note">Hiring instead? <a href="{{ route('recruiter.login') }}">Recruiter login</a></p>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/candidate-login.js') }}"></script>
@endpush