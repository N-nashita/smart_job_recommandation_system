@extends('layouts.candidate')

@section('title', 'Browse Jobs — JobSphere')

@section('main')
<div class="page-head">
    <div>
        <div class="eyebrow">Candidate portal</div>
        <h1 style="margin-top:10px">Browse Jobs</h1>
        <p id="sub">Open roles matched to your profile. Use the filters to narrow results.</p>
    </div>
    <div class="acts">
        <a href="{{ route('candidate.profile.edit') }}" class="btn ghost sm">
            <svg class="ic"><use href="#i-pin"/></svg> Edit profile
        </a>
    </div>
</div>

<!-- Filters -->
<section class="tools reveal">
    <form method="GET" action="{{ route('candidate.jobs') }}" class="row">
        <label class="search"><svg class="ic"><use href="#i-search"/></svg>
            <input name="q" type="search" placeholder="Search by title, company or city" value="{{ $q ?? '' }}" aria-label="Search jobs">
        </label>
        <select class="sel" name="category" aria-label="Category">
            <option value="">All categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}" {{ ($category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <select class="sel" name="location" aria-label="Location">
            <option value="">All locations</option>
            @foreach ($locations as $loc)
                <option value="{{ $loc }}" {{ ($location ?? '') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
            @endforeach
        </select>
        <select class="sel" name="type" aria-label="Job type">
            <option value="">All types</option>
            @foreach ($types as $t)
                <option value="{{ $t }}" {{ ($type ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <button class="btn primary sm" type="button" onclick="this.form.submit()">Search</button>
        <a href="{{ route('candidate.jobs') }}" class="btn ghost sm">Reset</a>
    </form>
</section>

<!-- Job listings -->
<section class="jobs-grid reveal">
@forelse ($jobs as $item)
    @php
        $job   = $item['job'];
        $score = (int) $item['score'];
        $applied = (bool) $item['applied'];
        $saved   = (bool) $item['saved'];
        $verified = optional($job->recruiter)->verification_status === 'verified';
    @endphp
    <article class="job-card">
        <div class="job-title">{{ $job->title }}</div>
        <div class="job-meta">
            {{ optional($job->recruiter)->company_name ?? '—' }}
            · {{ $job->location ?? '—' }}
            · {{ $job->type }}
            @if ($verified)
                <span style="color:var(--accent-deep)">
                    <svg class="ic" style="width:12px;height:12px"><use href="#i-shield"/></svg>
                    verified
                </span>
            @endif
        </div>
        <p style="color:var(--muted); font-size:0.84rem; margin-top:6px; line-height:1.4;">{{ \Illuminate\Support\Str::limit($job->description ?? '', 120) }}</p>
        <div class="match-score"><b>{{ $score }}</b>% match</div>
        <div class="score-fill"><span style="width:{{ $score }}%"></span></div>
        <div class="job-actions" style="margin-top:auto; display:flex; gap:8px">
            @if ($applied)
                <button class="btn applied sm" disabled>Applied</button>
            @else
                <button class="btn primary sm apply-btn"
                        data-url="{{ route('candidate.applications.store', $job->id) }}"
                        type="button">Apply</button>
            @endif
            <button class="btn ghost sm save-btn"
                    data-url="{{ route('candidate.saved.toggle', $job->id) }}"
                    data-saved="{{ $saved ? '1' : '0' }}"
                    type="button">
                <svg class="ic"><use href="#i-heart"/></svg>
            </button>
        </div>
    </article>
@empty
    <p class="empty-note">No jobs match your filters. Try adjusting your search.</p>
@endforelse
</section>

{{ $jobs->links() }}

@push('scripts')
<script>
@verbatim
(() => {
    document.querySelectorAll('.apply-btn').forEach((b) => {
        b.addEventListener('click', async () => {
            try {
                await window.api(b.dataset.url, 'POST');
                b.textContent = 'Applied';
                b.classList.add('applied');
                b.disabled = true;
                window.toast('Application submitted.');
            } catch (err) {
                window.toast(err.data && err.data.message ? err.data.message : err.message);
            }
        });
    });

    document.querySelectorAll('.save-btn').forEach((b) => {
        b.addEventListener('click', async () => {
            try {
                const res = await window.api(b.dataset.url, 'POST');
                const saved = res.saved;
                const svg = b.querySelector('svg');
                b.dataset.saved = saved ? '1' : '0';
                if (saved) {
                    b.classList.add('saved');
                    svg.style.fill = 'var(--accent-deep)';
                    window.toast('Job saved.');
                } else {
                    b.classList.remove('saved');
                    svg.style.fill = '';
                    window.toast('Job removed from saved.');
                }
            } catch (err) {
                window.toast(err.data && err.data.message ? err.data.message : err.message);
            }
        });
    });
})();
@endverbatim
</script>
@endpush
@endsection