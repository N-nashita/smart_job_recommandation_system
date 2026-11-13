@extends('layouts.recruiter')

@section('title', 'Verification — JobSphere')

@section('main')
@php
    $r      = auth('recruiter')->user();
    $vs     = $r->verification_status;
    $checks = json_decode($r->verification_checks ?? '[]', true) ?: [];
    $icons  = ['pass' => '✓', 'warn' => '!', 'fail' => '✕', 'skip' => '–'];
    $labels = ['verified' => 'Verified', 'pending' => 'Not verified'];
    $steps  = [
        ['Account created', true],
        ['Checks run', count($checks) > 0],
        ['Verified', $vs === 'verified'],
    ];
@endphp

<div class="page-head">
    <div><div class="eyebrow">Recruiter workspace</div><h1 style="margin-top:10px">Verification</h1>
        <p>Candidates only see jobs from verified companies.</p></div>
    <span class="vstatus {{ $vs }}">{{ $labels[$vs] ?? ucfirst($vs) }}</span>
</div>

@if (session('status'))<div class="vflash">{{ session('status') }}</div>@endif

<section class="glass card">
    <ol class="vsteps">
        @foreach ($steps as [$label, $done])
            <li class="{{ $done ? 'done' : '' }}"><b>{{ $done ? '✓' : $loop->iteration }}</b><span>{{ $label }}</span></li>
        @endforeach
    </ol>
</section>

<section class="split">
    <div class="glass card">
        <div class="card-head"><div><h3>{{ $r->company_name }}</h3><p class="sub">Registration no. {{ $r->registration_no }} · {{ $r->website ?: 'No website' }}</p></div>
            <form method="POST" action="{{ route('recruiter.verification.recheck') }}">@csrf
                <button class="btn ghost sm" type="submit">Run checks again</button></form></div>

        @if ($checks)
            <ul class="vchecks">
                @foreach ($checks as $c)
                    <li class="{{ $c['status'] }}"><span class="vic">{{ $icons[$c['status']] ?? '?' }}</span>
                        <div><b>{{ $c['label'] }}</b><small>{{ $c['detail'] }}</small></div></li>
                @endforeach
            </ul>
        @else
            <p class="sub">The automatic checks are still running. Refresh in a few seconds.</p>
        @endif
    </div>

    <div class="glass card">
        <h3>Company details</h3>
        @if ($vs === 'verified')
            <p class="sub" style="margin-top:8px">Your company is verified. No further action is needed.</p>
        @else
            <p class="sub" style="margin:8px 0 16px">If a check failed, correct the details below. We check them again right away.</p>
            <form method="POST" action="{{ route('recruiter.verification.update') }}" class="vup">
                @csrf
                <label class="dtl">Website <input name="website" value="{{ old('website', $r->website) }}" placeholder="https://yourcompany.com" required></label>
                @error('website')<span class="msg-err">{{ $message }}</span>@enderror
                <label class="dtl">Registration number <input name="registration_no" value="{{ old('registration_no', $r->registration_no) }}" required></label>
                @error('registration_no')<span class="msg-err">{{ $message }}</span>@enderror
                <button class="btn primary" type="submit">Save and check again</button>
            </form>
            <p class="sub" style="margin-top:12px">Your login email must be a company email on the same domain as your website.</p>
        @endif
    </div>
</section>
@endsection
