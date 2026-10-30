@php
    $r = auth('recruiter')->user();
    $status = $r->verification_status;
    $nav = [
        ['recruiter.dashboard',  'Overview',   'i-home'],
        ['recruiter.applicants', 'Applicants', 'i-users'],
        ['recruiter.jobs',       'My jobs',    'i-brief'],
        ['recruiter.interviews', 'Interviews', 'i-cal'],
    ];
@endphp
<aside class="side glass" aria-label="Main menu">
    <a href="{{ url('/') }}" class="logo"><i></i> JobSphere</a>

    @foreach ($nav as [$route, $label, $icon])
        <a class="nv {{ request()->routeIs($route) ? 'on' : '' }}" href="{{ route($route) }}">
            <svg class="ic"><use href="#{{ $icon }}"/></svg><span>{{ $label }}</span>
            @if ($route === 'recruiter.applicants')<em>24</em>@endif
        </a>
    @endforeach

    <a class="nv" href="#" id="logout"><svg class="ic"><use href="#i-logout"/></svg><span>Log out</span></a>
    <div class="grow"></div>

    <div class="co">
        <div class="row">
            <div class="avatar mint">{{ strtoupper(mb_substr($r->company_name, 0, 1)) }}</div>
            <div>
                <strong>{{ $r->company_name }}</strong>
                <small>{{ $r->industry }} · {{ $r->hq_location }}</small>
            </div>
        </div>
        <span class="vb" @if ($status !== 'verified') style="background:var(--blush);color:#a65a40" @endif>
            <svg class="ic"><use href="#i-shield"/></svg>
            <b>{{ ['verified' => 'Verified company', 'pending' => 'Verification pending', 'rejected' => 'Verification failed'][$status] ?? 'Unverified' }}</b>
        </span>
    </div>
</aside>
