@extends('layouts.candidate')

@section('title', $job->title . ' — JobSphere')

@section('page-eyebrow', 'Job details')
@section('page-title', $job->title)
@section('page-sub')
    {{ optional($job->recruiter)->company_name ?? '—' }}
    @if (optional($job->recruiter)->verification_status === 'verified')
        <span style="color:var(--accent-deep); font-weight:600; margin-left:6px;">
            <svg class="ic" style="width:12px;height:12px;vertical-align:-1px"><use href="#i-shield"/></svg>
            verified company
        </span>
    @endif
@endsection

@section('main')
@php
    $companyName = optional($job->recruiter)->company_name ?: 'Company';
    $companyInitials = collect(preg_split('/\s+/', trim($companyName)))
        ->filter()
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<div class="job-detail-actions">
    <a href="{{ route('candidate.jobs') }}" class="btn ghost sm">
        <svg class="ic"><use href="#i-arrow-r"/></svg> Back to jobs
    </a>
    <div class="tp-actions">
        @if (!$applied)
            <button class="btn primary apply-btn"
                    data-url="{{ route('candidate.applications.store', $job->id) }}"
                    data-job-id="{{ $job->id }}"
                    data-job-title="{{ $job->title }}"
                    type="button">Apply now</button>
        @else
            <button class="btn applied sm" disabled>Applied</button>
        @endif
        <button class="btn ghost sm save-btn"
                data-url="{{ route('candidate.saved.toggle', $job->id) }}"
                data-saved="{{ $saved ? '1' : '0' }}"
                type="button">
            <svg class="ic"><use href="#i-heart"/></svg>
        </button>
    </div>
</div>

<section class="split reveal">
    <div class="stack">
        <div class="glass card job-description-card">
            <div class="detail-heading">
                <span class="detail-kicker">Role overview</span>
                <h2>About this role</h2>
            </div>
            <p class="job-description">{{ $job->description }}</p>
        </div>

        <div class="glass card match-card">
            <div class="detail-heading">
                <span class="detail-kicker">Your fit</span>
                <h2>Match score</h2>
            </div>
            <div class="match-value">
                <b>{{ $score }}%</b>
                <span>match with your profile</span>
            </div>
            <div class="score-fill"><span style="width:{{ $score }}%"></span></div>
            <p class="match-note">Based on your skills, experience and location.</p>
        </div>
    </div>

    <div class="stack job-detail-side">
        <div class="glass card company-card">
            <div class="ph">
                <div class="avatar">{{ $companyInitials }}</div>
                <div>
                    <strong>{{ $companyName }}</strong>
                    <small>{{ optional($job->recruiter)->industry ?? 'Employer' }} · {{ optional($job->recruiter)->hq_location ?? 'Location not listed' }}</small>
                </div>
            </div>
            @if (optional($job->recruiter)->name || optional($job->recruiter)->verification_status === 'verified')
                <div class="company-badges">
                    @if (optional($job->recruiter)->name)
                        <span class="recruiter-name">Recruiter: {{ $job->recruiter->name }}</span>
                    @endif
                    @if (optional($job->recruiter)->verification_status === 'verified')
                        <span class="verified-pill"><svg class="ic"><use href="#i-shield"/></svg> Verified company</span>
                    @endif
                </div>
            @endif
            <div class="job-facts">
                <div><span>Location</span><b>{{ $job->location ?? '—' }}</b></div>
                <div><span>Job type</span><b>{{ $job->type ?? '—' }}</b></div>
                <div><span>Salary</span><b>
                    @if ($job->salary_min && $job->salary_max)
                        {{ '$' . number_format($job->salary_min) }} – {{ '$' . number_format($job->salary_max) }}
                    @else
                        Not disclosed
                    @endif
                </b></div>
            </div>
            @if ($job->skills && count($job->skills))
                <div class="required-skills">
                    <p class="detail-kicker">Required skills</p>
                    <div class="skill-list">
                        @foreach ($job->skills as $skill)
                            <span>{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
@verbatim
(() => {
    const saveBtn = document.querySelector('.save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            try {
                const res = await window.api(saveBtn.dataset.url, 'POST');
                const saved = res.saved;
                const svg = saveBtn.querySelector('svg');
                saveBtn.dataset.saved = saved ? '1' : '0';
                if (saved) {
                    saveBtn.classList.add('saved');
                    svg.style.fill = 'var(--accent-deep)';
                    window.toast('Job saved.');
                } else {
                    saveBtn.classList.remove('saved');
                    svg.style.fill = '';
                    window.toast('Job removed from saved.');
                }
            } catch (err) {
                window.toast(err.data && err.data.message ? err.data.message : err.message);
            }
        });
    }
})();
@endverbatim
</script>
@endpush
@endsection