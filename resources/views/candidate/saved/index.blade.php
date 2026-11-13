@extends('layouts.candidate')

@section('title', 'Saved Jobs — JobSphere')

@section('page-eyebrow', 'Candidate portal')
@section('page-title', 'Saved Jobs')
@section('page-sub', 'Jobs you\'ve bookmarked for later.')

@section('main')
<div class="page-head">
    <div>
        <a href="{{ route('candidate.jobs') }}" class="btn primary">
            <svg class="ic"><use href="#i-brief"/></svg> Browse jobs
        </a>
    </div>
</div>

<section class="jobs-grid reveal">
@forelse ($jobs as $job)
    @php
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
        <div class="job-actions" style="margin-top:auto; display:flex; gap:8px">
            <a href="{{ route('candidate.jobs.show', $job->id) }}" class="btn primary sm flex-1">View job</a>
            <button class="btn ghost sm save-btn"
                    data-url="{{ route('candidate.saved.toggle', $job->id) }}"
                    data-saved="1"
                    type="button">
                <svg class="ic"><use href="#i-heart"/></svg>
            </button>
        </div>
    </article>
@empty
    <p class="empty-note">You haven’t saved any jobs yet. Browse jobs and click the heart to save.</p>
@endforelse
</section>

{{ $jobs->links() }}

@push('scripts')
<script>
@verbatim
(() => {
    document.querySelectorAll('.save-btn').forEach((b) => {
        b.addEventListener('click', async () => {
            try {
                const res = await window.api(b.dataset.url, 'POST');
                if (!res.saved) {
                    // Remove card from DOM
                    const card = b.closest('.job-card');
                    if (card) card.remove();
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