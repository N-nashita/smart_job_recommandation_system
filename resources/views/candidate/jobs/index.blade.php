@extends('layouts.candidate')

@section('title', 'Browse Jobs — JobSphere')

@section('page-eyebrow', 'Candidate portal')
@section('page-title', 'Browse Jobs')
@section('page-sub', 'Open roles matched to your profile. Use the filters to narrow results.')

@section('main')
<div class="page-head">
    <div>
    </div>
    <div class="acts">
        <a href="{{ route('candidate.profile.edit') }}" class="btn ghost sm">
            <svg class="ic"><use href="#i-pin"/></svg> Edit profile
        </a>
    </div>
</div>

<!-- Filters -->
<section class="filters reveal">
    <form method="GET" action="{{ route('candidate.jobs') }}" class="filters-row">
        <div class="filters-search">
            <svg class="ic"><use href="#i-search"/></svg>
            <input name="q" type="search" placeholder="Search by title, company or city" value="{{ $q ?? '' }}" aria-label="Search jobs">
        </div>
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
        <button class="btn primary sm" type="submit">Search</button>
        <a href="{{ route('candidate.jobs') }}" class="btn ghost sm">Reset</a>
    </form>
</section>

<!-- Job count -->
<div class="jobs-meta reveal">
    <span><b>{{ $jobs->total() }}</b> job{{ $jobs->total() !== 1 ? 's' : '' }} found</span>
</div>

<!-- Job listings -->
<section class="jobs-list reveal">
@forelse ($jobs as $item)
    @php
        $job      = $item['job'];
        $score    = (int) $item['score'];
        $applied  = (bool) $item['applied'];
        $saved    = (bool) $item['saved'];
        $verified = optional($job->recruiter)->verification_status === 'verified';
        $company  = optional($job->recruiter)->company_name ?: 'Company';
        $ini      = collect(explode(' ', trim($company)))
                        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
        $colors   = ['sand', 'sage', 'blush', 'mint'];
        $tier     = $score >= 70 ? 'hi' : ($score >= 40 ? 'mid' : 'low');
        $skills   = collect($job->skills ?? [])->take(3);
        $showUrl  = \Illuminate\Support\Facades\Route::has('candidate.jobs.show')
                        ? route('candidate.jobs.show', $job->id) : null;
    @endphp

    <article class="jc">
        <div class="jc-top">
            <div class="avatar {{ $colors[$job->id % 4] }}">{{ $ini }}</div>
            <div class="jc-id">
                @if ($showUrl)
                    <a href="{{ $showUrl }}" class="jc-title">{{ $job->title }}</a>
                @else
                    <div class="jc-title">{{ $job->title }}</div>
                @endif
                <div class="jc-co">
                    {{ $company }}
                    @if ($verified)
                        <svg class="ic jc-ver"><use href="#i-shield"/></svg>
                    @endif
                </div>
            </div>
            <div class="jc-match {{ $tier }}"><b>{{ $score }}%</b><span>match</span></div>
        </div>

        <div class="jc-tags">
            <span class="jc-chip"><svg class="ic"><use href="#i-pin"/></svg>{{ $job->location ?? '—' }}</span>
            <span class="jc-chip"><svg class="ic"><use href="#i-brief"/></svg>{{ $job->type ?? '—' }}</span>
        </div>

        @if (!empty($job->description))
            <p class="jc-desc">{{ \Illuminate\Support\Str::limit($job->description, 180) }}</p>
        @endif

        @if ($skills->isNotEmpty())
            <div class="jc-skills">
                @foreach ($skills as $s)<span>{{ $s }}</span>@endforeach
            </div>
        @endif

        <div class="jc-foot">
            <div class="jc-bar"><span style="width:{{ $score }}%"></span></div>
            <div class="jc-actions">
                @if ($applied)
                    <button class="btn applied sm" disabled>Applied</button>
                @else
                    <button class="btn primary sm apply-btn"
                            data-url="{{ route('candidate.applications.store', $job->id) }}"
                            type="button">Apply</button>
                @endif
                @if ($showUrl)
                    <a href="{{ $showUrl }}" class="btn ghost sm">Details</a>
                @endif
                <button class="btn ghost sm save-btn {{ $saved ? 'saved' : '' }}"
                        data-url="{{ route('candidate.saved.toggle', $job->id) }}"
                        data-saved="{{ $saved ? '1' : '0' }}"
                        aria-label="Save job"
                        type="button">
                    <svg class="ic"><use href="#i-heart"/></svg>
                </button>
            </div>
        </div>
    </article>
@empty
    <div class="jc-empty glass">
        <svg class="ic"><use href="#i-search"/></svg>
        <h3>No jobs found</h3>
        <p>Try a different keyword or reset the filters.</p>
        <a href="{{ route('candidate.jobs') }}" class="btn primary sm">Reset filters</a>
    </div>
@endforelse
</section>

{{ $jobs->withQueryString()->links() }}

@push('scripts')
<script>
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
</script>
@endpush
@endsection
