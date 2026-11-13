@extends('layouts.candidate')

@section('title', '{{ $job->title }} — JobSphere')

@section('main')
<div class="page-head">
    <div>
        <div class="eyebrow">Job details</div>
        <h1 style="margin-top:10px">{{ $job->title }}</h1>
        <p id="sub">{{ optional($job->recruiter)->company_name ?? '—' }}
            @if (optional($job->recruiter)->verification_status === 'verified')
                <span style="color:var(--accent-deep); font-weight:600; margin-left:6px;">
                    <svg class="ic" style="width:12px;height:12px;vertical-align:-1px"><use href="#i-shield"/></svg>
                    verified company
                </span>
            @endif
        </p>
    </div>
    <div class="tp-actions">
        <a href="{{ route('candidate.jobs') }}" class="btn ghost sm">
            <svg class="ic"><use href="#i-arrow-r"/></svg> Back to jobs
        </a>
        @if (!$applied)
            <button class="btn primary apply-btn"
                    data-url="{{ route('candidate.applications.store', $job->id) }}"
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
        <div class="glass card">
            <div class="card-head">
                <div><h3>About this role</h3></div>
            </div>
            <p style="color:var(--ink); line-height:1.6; white-space:pre-line;">{{ $job->description }}</p>
        </div>

        <div class="glass card" style="margin-top:22px">
            <div class="card-head">
                <div><h3>Match score</h3></div>
            </div>
            <div style="display:flex; align-items:baseline; gap:10px; margin-bottom:10px">
                <b style="font-size:2rem; color:var(--accent-deep)">{{ $score }}%</b>
                <span style="color:var(--muted)">match with your profile</span>
            </div>
            <div class="score-fill"><span style="width:{{ $score }}%"></span></div>
            <p class="sub" style="margin-top:8px">Placeholder scoring — ML model plugs in later.</p>
        </div>
    </div>

    <div class="stack" style="max-width:340px">
        <div class="glass card prev">
            <div class="ph">
                <div class="avatar">{{ \App\Support\CandidateInfo::initials(optional($job->recruiter)) }}</div>
                <div>
                    <strong style="font-size:1rem">{{ optional($job->recruiter)->company_name ?? 'Company' }}</strong>
                    <small>{{ optional($job->recruiter)->industry ?? '' }} · {{ optional($job->recruiter)->hq_location ?? '' }}</small>
                </div>
            </div>
            <table style="width:100%; font-size:0.88rem; margin-top:16px">
                <tbody>
                    <tr><td style="color:var(--muted); width:40%">Location</td><td>{{ $job->location ?? '—' }}</td></tr>
                    <tr><td style="color:var(--muted)">Type</td><td>{{ $job->type ?? '—' }}</td></tr>
                    <tr><td style="color:var(--muted)">Salary</td><td>
                        @if ($job->salary_min && $job->salary_max)
                            {{ '$' . number_format($job->salary_min) }} – {{ '$' . number_format($job->salary_max) }}
                        @else
                            —
                        @endif
                    </td></tr>
                </tbody>
            </table>
            @if ($job->skills && count($job->skills))
                <div style="margin-top:16px">
                    <p class="sub" style="margin-bottom:6px">Required skills</p>
                    <div style="display:flex; gap:6px; flex-wrap:wrap">
                        @foreach ($job->skills as $skill)
                            <span style="background:var(--sand); color:var(--accent-deep); border-radius:999px; padding:4px 10px; font-size:0.8rem; font-weight:500">{{ $skill }}</span>
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
    const applyBtn = document.querySelector('.apply-btn');
    if (applyBtn) {
        applyBtn.addEventListener('click', async () => {
            try {
                await window.api(applyBtn.dataset.url, 'POST');
                applyBtn.textContent = 'Applied';
                applyBtn.classList.add('applied');
                applyBtn.disabled = true;
                window.toast('Application submitted.');
            } catch (err) {
                window.toast(err.data && err.data.message ? err.data.message : err.message);
            }
        });
    }

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