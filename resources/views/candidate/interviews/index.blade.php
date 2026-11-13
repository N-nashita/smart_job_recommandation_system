@extends('layouts.candidate')

@section('title', 'Interviews — JobSphere')

@section('main')
<div class="page-head">
    <div>
        <div class="eyebrow">Candidate portal</div>
        <h1 style="margin-top:10px">Interviews</h1>
        <p id="sub">Interview calls scheduled by recruiters.</p>
    </div>
    <div class="acts">
        <a href="{{ route('candidate.jobs') }}" class="btn primary">
            <svg class="ic"><use href="#i-brief"/></svg> Browse jobs
        </a>
    </div>
</div>

<section class="glass card reveal">
    <div class="table-wrap">
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
                    $statusLabel = ucfirst($iv->status);
                    $statusClass = 'stage '.str_replace(' ', '-', $iv->status);
                    $when = $iv->scheduled_at?->setTimezone('Asia/Dhaka')->format('l, j F Y · H:i') ?? '—';
                @endphp
                <tr>
                    <td><b>{{ $job?->title ?? '—' }}</b></td>
                    <td>{{ $recruiter->company_name ?? '—' }}</td>
                    <td>{{ $iv->round ?? '—' }} · {{ $iv->mode ?? '—' }}</td>
                    <td>{{ $when }}</td>
                    <td><span class="stage {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td style="text-align:right">
                        @if ($iv->status === 'cancelled')
                            <span class="btn applied sm" style="cursor:default">Cancelled</span>
                        @elseif ($iv->status === 'done')
                            <span class="btn applied sm" style="cursor:default">Completed</span>
                        @else
                            <a href="{{ route('candidate.jobs.show', $job?->id ?? 0) }}" class="btn primary sm">View job</a>
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