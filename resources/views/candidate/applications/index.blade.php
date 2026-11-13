@extends('layouts.candidate')

@section('title', 'My Applications — JobSphere')

@section('main')
<div class="page-head">
    <div>
        <div class="eyebrow">Candidate portal</div>
        <h1 style="margin-top:10px">My Applications</h1>
        <p id="sub">Track the status of every job you’ve applied to.</p>
    </div>
    <div class="acts">
        <a href="{{ route('candidate.jobs') }}" class="btn primary">
            <svg class="ic"><use href="#i-brief"/></svg> Browse jobs
        </a>
    </div>
</div>

<!-- Status filter tabs -->
<section class="tools reveal">
    <div class="row">
        <div class="tabs" id="statusTabs">
            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="{{ $status ? '' : 'on' }}">All</a>
            @foreach ($statuses as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}" class="{{ $status === $key ? 'on' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
        <span class="chk" style="margin-left:auto">
            {{ $applications->total() }} total
        </span>
    </div>
</section>

<!-- Applications table -->
<section class="glass card reveal">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Job</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Match</th>
                    <th>Applied</th>
                    <th style="text-align:right">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($applications as $app)
                @php
                    $statusLabel = $statuses[$app->status] ?? ucfirst($app->status);
                    $statusClass = 'stage '.str_replace(' ', '-', $app->status);
                    $interview   = $app->interviews->first();
                    $showWithdraw = in_array($app->status, ['new', 'reviewed', 'shortlisted']);
                @endphp
                <tr>
                    <td>
                        <b>{{ $app->job->title }}</b>
                        @if ($interview)
                            <br><small style="color:var(--accent-deep)">Interview scheduled</small>
                        @endif
                    </td>
                    <td>{{ optional($app->job->recruiter)->company_name ?? '—' }}</td>
                    <td><span class="stage {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td>
                        @if ($app->match_score)
                            {{ $app->match_score }}%
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $app->created_at->diffForHumans() }}</td>
                    <td style="text-align:right">
                        @if ($showWithdraw)
                            <form method="POST" action="{{ route('candidate.applications.destroy', $app->id) }}" style="display:inline"
                                  onsubmit="return confirm('Withdraw this application?')">
                                @csrf @method('DELETE')
                                <button class="btn danger sm" type="submit">Withdraw</button>
                            </form>
                        @else
                            <span class="btn applied sm" style="cursor:default">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-note">No applications yet. Browse jobs and apply.</td>
                </tr>
            @endforelse
        </table>
    </div>
</section>

{{ $applications->links() }}

@endsection