@extends('layouts.candidate')

@section('title', 'Notifications — JobSphere')

@section('main')
<div class="page-head">
    <div>
        <div class="eyebrow">Candidate portal</div>
        <h1 style="margin-top:10px">Notifications</h1>
        <p id="sub">Updates on your applications and interviews.</p>
    </div>
    <div class="acts">
        <a href="{{ route('candidate.dashboard') }}" class="btn ghost sm">
            <svg class="ic"><use href="#i-arrow-r"/></svg> Back to dashboard
        </a>
    </div>
</div>

<section class="glass card reveal">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Body</th>
                    <th>Type</th>
                    <th>When</th>
                    <th style="width:60px; text-align:center">Read</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($notifications as $n)
                @php
                    $unread = is_null($n->read_at);
                @endphp
                <tr class="{{ $unread ? '' : 'read' }}">
                    <td style="font-weight:600">{{ $n->title }}</td>
                    <td>{{ $n->body }}</td>
                    <td><span class="stage" style="font-size:0.7rem; background:var(--sage); color:var(--accent-deep)">{{ ucfirst($n->type) }}</span></td>
                    <td>{{ $n->created_at->diffForHumans() }}</td>
                    <td style="text-align:center">
                        @if ($unread)
                            <span style="width:8px;height:8px;border-radius:50%;background:var(--accent-deep);display:inline-block"></span>
                        @else
                            <span style="color:var(--muted); font-size:0.7rem">read</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-note">No notifications yet.</td>
                </tr>
            @endforelse
        </table>
    </div>
</section>

{{ $notifications->links() }}

@push('scripts')
<script>
@verbatim
(() => {
    // Mark notification as read when row is clicked (AJAX)
    document.querySelectorAll('table tbody tr').forEach((row) => {
        row.addEventListener('click', async (e) => {
            if (e.target.closest('a, button')) return;
            const link = row.querySelector('a');
            if (!link) return;
            const id = link.getAttribute('href').match(/read\/(\d+)/);
            if (!id) return;
            try {
                await window.api('/candidate/notifications/' + id[1] + '/read', 'PATCH');
                const badge = row.querySelector('td:last-child');
                badge.innerHTML = '<span style="color:var(--muted); font-size:0.7rem">read</span>';
                row.classList.remove('unread');
                row.classList.add('read');
                // Update bell badge
                const bellBadge = document.querySelector('.icon-btn .badge');
                if (bellBadge) {
                    const n = parseInt(bellBadge.textContent) - 1;
                    if (n > 0) bellBadge.textContent = n;
                    else bellBadge.remove();
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