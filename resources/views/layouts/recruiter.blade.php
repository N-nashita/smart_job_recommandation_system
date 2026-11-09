@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ asset('style/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('style/pages.css') }}">
@endpush

@section('bodyclass', 'app')

@section('content')
    @include('partials.sprite')
    <script>window.RECRUITER_VERIFIED = {{ auth('recruiter')->user()->verification_status === 'verified' ? 'true' : 'false' }};</script>
    <div class="shell">
        @include('partials.recruiter-sidebar')
        <main class="main">
            @include('partials.verification-banner')
            @yield('main')
        </main>
    </div>
    @yield('modals')
    <div class="toast" id="toast" role="status"></div>
@endsection

@prepend('scripts')
    <script src="{{ asset('js/recruiter-shell.js') }}"></script>
@endprepend
