@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="/style/home-candidate.css">
    <link rel="stylesheet" href="/style/candidate-dashboard.css">
@endpush

@section('bodyclass', 'candidate')

@section('content')
    @include('partials.sprite')
    @php
        $c = auth()->user();
        $unread = \App\Models\Notification::where('user_id', $c->id)->whereNull('read_at')->count();
    @endphp

    <div class="shell">
        @include('partials.candidate-sidebar', ['unread' => $unread])

        <main class="main">
            <header class="topbar reveal">
                <div class="topbar-left">
                    <div class="eyebrow">@yield('page-eyebrow', 'Candidate portal')</div>
                    <h1 style="margin-top:10px">@yield('page-title', 'JobSphere')</h1>
                    <p id="sub">@yield('page-sub')</p>
                </div>
                <div class="tp-actions">
                    <a href="{{ route('candidate.notifications') }}" class="icon-btn"
                       aria-label="Notifications ({{ $unread }} unread)">
                        <svg class="ic"><use href="#i-bell"/></svg>
                        @if ($unread) <span class="badge">{{ $unread }}</span>@endif
                    </a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="av-head-btn" type="button" style="display:flex; align-items:center; gap:10px; padding:6px 12px; border-radius:14px; border:1px solid var(--line); background:#fff; cursor:pointer">
                                <div class="avatar">{{ \App\Support\CandidateInfo::initials($c) }}</div>
                                <div class="topbar-user" style="min-width:0">
                                    <div class="topbar-name" style="font-weight:600; font-size:.86rem; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $c->name }}</div>
                                    <div class="topbar-role" style="font-size:.74rem; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $c->email }}</div>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="av-head">
                                <div class="avatar">{{ \App\Support\CandidateInfo::initials($c) }}</div>
                                <div>
                                    <div class="av-name">{{ $c->name }}</div>
                                    <div class="av-email">{{ $c->email }}</div>
                                </div>
                            </div>
                            <x-dropdown-link :href="route('candidate.profile.edit')">
                                Profile
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('candidate.profile.cv')">
                                Download CV
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            @yield('main')
        </main>
    </div>

    @yield('modals')
    <div class="toast" id="toast" role="status"></div>
@endsection

@prepend('scripts')
    <script src="{{ asset('js/candidate-shell.js') }}"></script>
@endprepend
