@php
    $c = auth()->user();
    $nav = [
        ['candidate.dashboard', 'Dashboard',   'i-home'],
        ['candidate.jobs',      'Jobs',        'i-brief'],
        ['candidate.applications', 'Applications', 'i-users'],
        ['candidate.saved',     'Saved Jobs',  'i-heart'],
        ['candidate.interviews','Interviews',  'i-cal'],
        ['candidate.profile.edit', 'Profile',   'i-pin'],
    ];
    $pendingApp = \App\Models\Application::where('candidate_id', $c->id)
        ->whereIn('status', ['new', 'reviewed', 'shortlisted'])->count();
    $pendingInterview = \App\Models\Interview::whereHas('application', function ($q) use ($c) {
        $q->where('candidate_id', $c->id);
    })->whereNotIn('status', ['cancelled', 'done'])->count();
@endphp
<aside class="side glass" aria-label="Main menu">
    <a href="{{ url('/') }}" class="logo"><i></i> JobSphere</a>

    @foreach ($nav as [$route, $label, $icon])
        <a class="nv {{ request()->routeIs($route) ? 'on' : '' }}" href="{{ route($route) }}">
            <svg class="ic"><use href="#{{ $icon }}"/></svg>
            <span>{{ $label }}</span>
            @if ($route === 'candidate.applications' && $pendingApp > 0)
                <em>{{ $pendingApp }}</em>
            @endif
            @if ($route === 'candidate.interviews' && $pendingInterview > 0)
                <em>{{ $pendingInterview }}</em>
            @endif
        </a>
    @endforeach

    <a class="nv" href="#" id="cand-logout">
        <svg class="ic"><use href="#i-logout"/></svg>
        <span>Log out</span>
    </a>
    <div class="grow"></div>

    <div class="co">
        <div class="row">
            <div class="avatar">{{ \App\Support\CandidateInfo::initials($c) }}</div>
            <div>
                <strong>{{ $c->name }}</strong>
                <small>{{ $c->email }}</small>
            </div>
        </div>
    </div>
</aside>