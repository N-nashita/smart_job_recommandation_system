@extends('layouts.candidate')

@section('title', 'Interviews — JobSphere')

@section('page-eyebrow', 'Candidate portal')
@section('page-title', 'Interviews')
@section('page-sub', 'Interview calls scheduled by recruiters.')

@section('main')
<div class="page-head">
    <div>
        <a href="{{ route('candidate.jobs') }}" class="btn primary">
            <svg class="ic"><use href="#i-brief"/></svg> Browse jobs
        </a>
    </div>
</div>

<section class="glass card reveal">
    @if (session('status'))
        <div class="interview-alert">{{ session('status') }}</div>
    @endif
    <div class="table-wrap interview-table">
        <table>
            <thead>
                <tr>
                    <th>Job</th>
                    <th>Company</th>
                    <th>Round / Mode</th>
                    <th>When</th>
                    <th>Status</th>
                    <th style="text-align:right">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($interviews as $iv)
                @php
                    $job     = $iv->application?->job;
                    $recruiter = optional($job?->recruiter);
                    $statusLabel = $iv->status === 'confirmed' ? 'Accepted' : ucfirst($iv->status);
                    $statusClass = 'stage '.str_replace(' ', '-', $iv->status);
                    $when = $iv->scheduled_at?->setTimezone('Asia/Dhaka')->format('l, j F Y · H:i') ?? '—';
                @endphp
                <tr>
                    <td class="interview-job"><b>{{ $job?->title ?? '—' }}</b></td>
                    <td class="interview-company">{{ $recruiter->company_name ?? '—' }}</td>
                    <td><span class="interview-mode">{{ $iv->round ?? '—' }} · {{ $iv->mode ?? '—' }}</span></td>
                    <td class="interview-when">{{ $when }}</td>
                    <td><span class="stage {{ $statusClass }}">{{ $statusLabel }}</span></td>
<td class="interview-actions">
    @if ($iv->status === 'cancelled')
        <span class="btn applied sm" style="cursor:default">Cancelled</span>
    @elseif ($iv->status === 'done')
        <span class="btn applied sm" style="cursor:default">Completed</span>
    @elseif (in_array($iv->status, ['accepted', 'confirmed'], true))
        <span class="btn primary sm" style="cursor:default">Accepted</span>
    @elseif ($iv->status === 'rejected')
        <span class="btn applied sm" style="cursor:default">Declined</span>
    @else
        <form method="POST" action="{{ route('candidate.interviews.accept', $iv->id) }}" style="display:inline">
            @csrf
            <button class="btn primary sm" type="submit" onclick="return confirm('Accept this interview?')">Accept</button>
        </form>
        <form method="POST" action="{{ route('candidate.interviews.reject', $iv->id) }}">
            @csrf
            <button class="btn ghost sm" type="submit" onclick="return confirm('Decline this interview?')">Decline</button>
        </form>
    @endif
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-note">No interviews scheduled yet. Recruiters will invite you here.</td>
                </tr>
            @endforelse
        </table>
    </div>
</section>

@endsection