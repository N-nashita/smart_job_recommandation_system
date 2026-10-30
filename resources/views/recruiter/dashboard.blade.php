@extends('layouts.recruiter')

@section('title', 'Dashboard — JobSphere')

@section('main')
@php
    $r = auth('recruiter')->user();
    $first = explode(' ', trim($r->name))[0];
    $hour = (int) now()->setTimezone('Asia/Dhaka')->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

    // MOCK DATA: replace with real queries later
    $matches = [
        ['n' => 'Rafi Ahmed',  'i' => 'RA', 'av' => 'sage',  'role' => 'Backend Engineer · 3 yrs', 'm' => 94, 'up' => true,  'sk' => ['Laravel', 'MySQL', 'REST APIs']],
        ['n' => 'Sadia Islam', 'i' => 'SI', 'av' => 'blush', 'role' => 'Backend Engineer · 2 yrs', 'm' => 91, 'up' => true,  'sk' => ['PHP', 'AWS', 'Docker']],
        ['n' => 'Imran Hossain', 'i' => 'IH', 'av' => 'mint', 'role' => 'Frontend Developer · 3 yrs', 'm' => 90, 'up' => true, 'sk' => ['React', 'TypeScript']],
    ];
    $today = [
        ['t' => '10:00', 'n' => 'Nabil Hasan',   'd' => 'Technical round · Meet', 'now' => true],
        ['t' => '14:30', 'n' => 'Mim Chowdhury', 'd' => 'HR intro · Phone',      'now' => false],
    ];
@endphp

<section class="welcome">
    <div class="hello reveal">
        <div class="eyebrow">Recruiter workspace</div>
        <h1>{{ $greeting }}, <em>{{ $first }}.</em></h1>
        <p>9 candidates updated their profiles since yesterday. Two of them now match your Backend role above 90%.</p>
        <div class="chips">
            <div class="chip"><b>24</b><span>New applicants</span></div>
            <div class="chip"><b>5</b><span>Interviews</span></div>
            <div class="chip"><b>9</b><span>Profile updates</span></div>
        </div>
        <div class="quick">
            <a href="{{ route('recruiter.applicants') }}" class="btn light sm">Review applicants</a>
            <a href="{{ route('recruiter.jobs') }}" class="btn outline-w sm">Post a job</a>
        </div>
    </div>

    <div class="glass focus reveal">
        <h3>Today's focus <span class="ai">Suggested</span></h3>
        <p class="sub">Picked from candidate activity and your open roles.</p>
        <div class="bar"><i id="bar"></i></div>
        <ul class="tasks" id="tasks">
            <li><label><input type="checkbox"><span>Review 3 strong new matches<small>Backend Engineer · 85%+ compatibility</small></span></label></li>
            <li><label><input type="checkbox"><span>Call Rafi Ahmed for interview<small>Updated his portfolio 2 hours ago</small></span></label></li>
            <li><label><input type="checkbox"><span>Publish the UI Designer draft<small>Posting passed the authenticity check</small></span></label></li>
        </ul>
    </div>
</section>

<section class="cols">
    <div class="glass card reveal">
        <div class="card-head">
            <div><h3>Top matches</h3><p class="sub">Best fits for your open roles</p></div>
            <a href="{{ route('recruiter.applicants') }}" class="btn ghost sm">View all</a>
        </div>
        <div class="cands">
            @foreach ($matches as $m)
                <a class="cand" href="{{ route('recruiter.applicants') }}">
                    <div class="avatar {{ $m['av'] }}">{{ $m['i'] }}</div>
                    <div>
                        <div class="nm">{{ $m['n'] }} @if ($m['up'])<span class="tag">Updated profile</span>@endif</div>
                        <div class="rl">{{ $m['role'] }}</div>
                        <div class="skills">@foreach ($m['sk'] as $s)<span>{{ $s }}</span>@endforeach</div>
                    </div>
                    <div class="right"><div class="ring" style="--p:{{ $m['m'] }}"><b>{{ $m['m'] }}</b></div>match</div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="glass card reveal">
        <div class="card-head" style="margin-bottom:14px">
            <div><h3>Today's interviews</h3><p class="sub">{{ now()->setTimezone('Asia/Dhaka')->format('l j F') }}</p></div>
            <a href="{{ route('recruiter.interviews') }}" class="btn ghost sm">Schedule</a>
        </div>
        <ul class="tl">
            @foreach ($today as $e)
                <li class="{{ $e['now'] ? 'now' : '' }}"><time>{{ $e['t'] }}</time><span class="dot"></span>
                    <div class="ev"><b>{{ $e['n'] }}</b><small>{{ $e['d'] }}</small></div></li>
            @endforeach
        </ul>
    </div>
</section>
@endsection

@push('scripts')
<script>
@verbatim
(() => {
    const boxes = [...document.querySelectorAll('#tasks input')];
    const prog = () => { document.getElementById('bar').style.width = Math.round(boxes.filter(b => b.checked).length / boxes.length * 100) + '%'; };
    boxes.forEach(b => b.addEventListener('change', prog)); prog();
})();
@endverbatim
</script>
@endpush
