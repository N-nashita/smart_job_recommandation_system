@extends('layouts.candidate')

@section('title', 'Dashboard — JobSphere')
@section('page-title', 'Dashboard')
@section('page-sub', 'Your applications, interviews and saved jobs in one place.')

@section('main')
@php
    $c = auth()->user();
    $first = explode(' ', trim($c->name))[0];
    $hour = (int) now()->setTimezone('Asia/Dhaka')->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $colors = ['sand', 'sage', 'blush', 'mint'];
@endphp

<!-- Welcome banner -->
<section class="welcome reveal">
    <div class="hello reveal">
        <div class="eyebrow">Candidate portal</div>
        <h1>{{ $greeting }}, <em>{{ $first }}.</em></h1>
        <p>Your applications, interviews and saved jobs in one place.</p>
        <div class="chips">
        <div class="chip"><b id="applications-count">{{ $stats['applications'] }}</b><span>Applications</span></div>
            <div class="chip"><b>{{ $stats['interviews'] }}</b><span>Upcoming interviews</span></div>
            <div class="chip"><b>{{ $stats['saved'] }}</b><span>Saved jobs</span></div>
            
        </div>
        <div class="quick">
            <a href="{{ route('candidate.jobs') }}" class="btn primary sm"><svg class="ic"><use href="#i-brief"/></svg> Browse jobs</a>
            <a href="{{ route('candidate.profile.edit') }}" class="btn ghost sm"><svg class="ic"><use href="#i-pin"/></svg> Edit profile</a>
        </div>
    </div>

    <div class="focus glass reveal">
    <h3>Profile completeness</h3>
    <p class="sub">Fill in your details to improve job matches and CV quality.</p>

    <div class="focus-pct">{{ $completeness ?? 0 }}<small>%</small></div>

    <div class="bar"><i style="width:{{ $completeness ?? 0 }}%"></i></div>

    <div class="focus-foot">
        <span>{{ ($completeness ?? 0) === 100 ? 'Great! Your profile is complete.' : 'Keep going, almost there.' }}</span>
    </div>

    @if (($completeness ?? 0) < 100)
        <a href="{{ route('candidate.profile.edit') }}" class="btn primary sm">Complete profile</a>
    @endif
</div>
</section>

<!-- Two column layout -->
<section class="cols">
    <!-- Recent applications -->
    <div class="glass card reveal">
        <div class="card-head">
            <div>
                <h3>Recent applications</h3>
                <p class="sub">Your latest submissions.</p>
            </div>
            <a href="{{ route('candidate.applications') }}" class="btn ghost sm">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Applied</th>
                    </tr>
                </thead>
                <tbody id="recent-applications-body">
                @forelse ($applications->take(5) as $a)
                    @php
                        $statusLabel = match ($a->status) {
                            'new' => 'Applied',
                            default => ucfirst($a->status),
                        };
                        $statusClass = 'stage '.str_replace(' ', '-', $a->status);
                    @endphp
                    <tr>
                        <td><b>{{ $a->job->title }}</b></td>
                        <td>{{ optional($a->job->recruiter)->company_name ?? '—' }}</td>
                        <td><span class="stage {{ $statusClass }}">{{ $statusLabel }}</span></td>
                        <td>{{ $a->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-note">No applications yet. Browse jobs and apply.</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>

    <!-- Recommended jobs -->
    <div class="glass card reveal">
        <div class="card-head">
            <div>
                <h3>Recommended for you</h3>
                <p class="sub">Open jobs posted by recruiters, ranked for your profile.</p>
            </div>
            <a href="{{ route('candidate.jobs') }}" class="btn ghost sm">View all jobs</a>
        </div>
        <div class="jobs-grid reveal">
        @forelse ($recommended as $rec)
            @php
                $job   = $rec['job'];
                $score = (int) $rec['score'];
                $applied = $applications->pluck('job_post_id')->contains($job->id);
                $verified = optional($job->recruiter)->verification_status === 'verified';
                $ini = collect(explode(' ', trim($job->title)))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
            @endphp
            <article class="cand reveal">
                <div class="avatar {{ $colors[$job->id % 4] }}">{{ $ini }}</div>
                <div>
                    <div class="nm">{{ $job->title }}</div>
                    <div class="rl">{{ optional($job->recruiter)->company_name ?? '—' }} · {{ $job->location ?? '—' }}</div>
                </div>
                <div class="right">
                    <div class="match-score"><b>{{ $score }}</b>% match</div>
                    <div class="score-fill"><span style="width:{{ $score }}%"></span></div>
                    @if ($applied)
                        <button class="btn applied sm" disabled>Applied</button>
                    @else
                        <button class="btn primary sm apply-btn"
                                data-url="{{ route('candidate.applications.store', $job->id) }}"
                                data-job-id="{{ $job->id }}"
                                data-job-title="{{ $job->title }}"
                                type="button">Apply</button>
                    @endif
                    @if ($verified)
                        <div style="font-size:0.7rem;color:var(--accent-deep);margin-top:4px">
                            <svg class="ic" style="width:10px;height:10px"><use href="#i-shield"/></svg> verified
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <p class="empty-note">No open jobs right now. Check back later.</p>
        @endforelse
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(() => {
    document.addEventListener('application:submitted', (event) => {
        const body = document.getElementById('recent-applications-body');
        const count = document.getElementById('applications-count');
        if (!body) return;

        const empty = body.querySelector('.empty-note');
        if (empty) empty.closest('tr').remove();

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><b></b></td>
            <td>—</td>
            <td><span class="stage">Applied</span></td>
            <td>Just now</td>
        `;
        row.querySelector('td b').textContent = event.detail.jobTitle;
        body.prepend(row);

        while (body.children.length > 5) body.lastElementChild.remove();
        if (count) count.textContent = String(Number(count.textContent || 0) + 1);
    });
})();
</script>
@endpush
