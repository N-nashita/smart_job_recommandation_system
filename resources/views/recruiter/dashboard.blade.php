@extends('layouts.recruiter')

@section('title', 'Dashboard — JobSphere')

@section('main')
@php
    $r = auth('recruiter')->user();
    $first = explode(' ', trim($r->name))[0];
    $hour = (int) now()->setTimezone('Asia/Dhaka')->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $colors = ['sage', 'blush', 'mint', 'sand'];
    $hasFocus = $auto->count() + $tasks->count() > 0;
    $doneN = $tasks->where('done', true)->count();
@endphp

<section class="welcome">
    <div class="hello reveal">
        <div class="eyebrow">Recruiter workspace</div>
        <h1>{{ $greeting }}, <em>{{ $first }}.</em></h1>
        @if (! $verified)
            <p>Your workspace fills up once your company is verified.</p>
        @elseif ($stats['new'] + $stats['interviews'] + $stats['open'] === 0)
            <p>Nothing yet. Post your first job and candidates will start showing up here.</p>
        @else
            <p>{{ $stats['new'] }} new {{ $stats['new'] === 1 ? 'applicant is' : 'applicants are' }} waiting and {{ $stats['interviews'] }} {{ $stats['interviews'] === 1 ? 'interview is' : 'interviews are' }} scheduled today.</p>
        @endif
        <div class="chips">
            <div class="chip"><b>{{ $stats['new'] }}</b><span>New applicants</span></div>
            <div class="chip"><b>{{ $stats['interviews'] }}</b><span>Interviews today</span></div>
            <div class="chip"><b>{{ $stats['open'] }}</b><span>Open jobs</span></div>
        </div>
        <div class="quick">
            <a href="{{ route('recruiter.applicants') }}" class="btn light sm">Review applicants</a>
            <a href="{{ route('recruiter.jobs') }}" class="btn outline-w sm">Post a job</a>
        </div>
    </div>

    <div class="glass focus reveal">
        <h3>Today's focus @if ($auto->count())<span class="ai">Suggested</span>@endif</h3>
        <p class="sub">Suggestions come from your live data. Add your own tasks below.</p>
        <div class="bar"><i id="bar"></i></div>

        @if (! $hasFocus)
            <p class="sub" style="margin-top:14px">{{ $verified ? 'No tasks for today.' : 'Available after verification.' }}</p>
        @endif

        <ul class="tasks">
            @foreach ($auto as $a)
                <li><a href="{{ $a['url'] }}" class="auto-item"><span>{{ $a['title'] }}<small>{{ $a['note'] }}</small></span><b>→</b></a></li>
            @endforeach
        </ul>
        <ul class="tasks" id="tasks">
            @foreach ($tasks as $t)
                <li><label><input type="checkbox" data-id="{{ $t->id }}" @if ($t->done) checked @endif>
                    <span>{{ $t->title }}@if ($t->note)<small>{{ $t->note }}</small>@endif</span></label>
                    <form method="POST" action="{{ route('recruiter.tasks.destroy', $t->id) }}" class="x">@csrf @method('DELETE')<button title="Remove">×</button></form></li>
            @endforeach
        </ul>

        @if ($verified)
            <form method="POST" action="{{ route('recruiter.tasks.store') }}" class="addtask">
                @csrf
                <input name="title" placeholder="Add a task for today" maxlength="140" required>
                <button class="btn light sm" type="submit">Add</button>
            </form>
        @endif
    </div>
</section>

<section class="cols">
    <div class="glass card reveal">
        <div class="card-head">
            <div><h3>Top matches</h3><p class="sub">Best fits for your open roles</p></div>
            <a href="{{ route('recruiter.applicants') }}" class="btn ghost sm">View all</a>
        </div>
        <div class="cands">
            @forelse ($matches as $m)
                @php($c = $m->candidate)
                @php($ini = collect(explode(' ', trim($c->name)))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode(''))
                <a class="cand" href="{{ route('recruiter.applicants') }}">
                    <div class="avatar {{ $colors[$c->id % 4] }}">{{ $ini }}</div>
                    <div>
                        <div class="nm">{{ $c->name }}</div>
                        <div class="rl">Applied for {{ $m->job->title }}</div>
                    </div>
                    <div class="right"><div class="ring" style="--p:{{ $m->match_score }}"><b>{{ $m->match_score }}</b></div>match</div>
                </a>
            @empty
                <p class="sub empty-note">{{ $verified ? 'No applicants yet.' : 'Nothing to show until your company is verified.' }}</p>
            @endforelse
        </div>
    </div>

    <div class="glass card reveal">
        <div class="card-head" style="margin-bottom:14px">
            <div><h3>Today's interviews</h3><p class="sub">{{ now()->setTimezone('Asia/Dhaka')->format('l j F') }}</p></div>
            <a href="{{ route('recruiter.interviews') }}" class="btn ghost sm">Schedule</a>
        </div>
        <ul class="tl">
            @forelse ($interviews as $e)
                <li><time>{{ $e->scheduled_at->setTimezone('Asia/Dhaka')->format('H:i') }}</time><span class="dot"></span>
                    <div class="ev"><b>{{ $e->application->candidate->name }}</b><small>{{ trim(($e->round ?? '').' · '.($e->mode ?? ''), ' ·') }}</small></div></li>
            @empty
                <li class="empty-note sub">No interviews today.</li>
            @endforelse
        </ul>
    </div>
</section>

<section class="glass card reveal">
    <div class="card-head">
        <div><h3>Your jobs</h3><p class="sub">Latest postings and how many people applied</p></div>
        <a href="{{ route('recruiter.jobs') }}" class="btn ghost sm">Manage jobs</a>
    </div>
    <ul class="feed">
        @forelse ($jobs as $j)
            <li><span class="pip {{ $j->status === 'open' ? '' : 'c' }}"><svg class="ic"><use href="#i-brief"/></svg></span>
                <div><b>{{ $j->title }}</b><small>{{ $j->location }} · {{ $j->type }} · {{ ['open' => 'Live', 'draft' => 'Draft', 'closed' => 'Closed'][$j->status] ?? $j->status }} · {{ $j->applications_count }} {{ $j->applications_count === 1 ? 'applicant' : 'applicants' }}</small></div></li>
        @empty
            <li class="sub">{{ $verified ? 'No jobs yet. Click “Post a job” to create your first one.' : 'Jobs appear here once your company is verified.' }}</li>
        @endforelse
    </ul>
</section>
@endsection

@push('scripts')
<script>
@verbatim
(() => {
    const boxes = [...document.querySelectorAll('#tasks input[type=checkbox]')];
    const bar = document.getElementById('bar');
    const prog = () => { bar.style.width = boxes.length ? Math.round(boxes.filter(b => b.checked).length / boxes.length * 100) + '%' : '0%'; };
    const token = document.querySelector('meta[name="csrf-token"]').content;
    boxes.forEach(b => b.addEventListener('change', () => {
        prog();
        fetch('/recruiter/tasks/' + b.dataset.id + '/toggle', { method: 'POST', headers: { 'X-CSRF-TOKEN': token } })
            .catch(() => { b.checked = !b.checked; prog(); });
    }));
    prog();
})();
@endverbatim
</script>
@endpush
