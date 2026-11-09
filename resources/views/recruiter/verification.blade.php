@extends('layouts.recruiter')

@section('title', 'Verification — JobSphere')

@section('main')
@php
    $r      = auth('recruiter')->user();
    $vs     = $r->verification_status;
    $checks = json_decode($r->verification_checks ?? '[]', true) ?: [];
    $icons  = ['pass' => '✓', 'warn' => '!', 'fail' => '✕', 'skip' => '–'];
    $labels = ['verified' => 'Verified', 'pending' => 'Under review', 'rejected' => 'Rejected'];
    $steps  = [
        ['Account created', true],
        ['Automatic checks', count($checks) > 0],
        ['Document reviewed', $vs === 'verified' || $vs === 'rejected'],
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
        <h3>Company document</h3>
        @if ($vs === 'verified')
            <p class="sub" style="margin-top:8px">Your company is verified. No further action is needed.</p>
        @else
            @if ($vs === 'rejected' && $r->verification_note)
                <div class="vflash bad">Reason: {{ $r->verification_note }}</div>
            @endif
            <p class="sub" style="margin:8px 0 16px">Upload your trade licence or certificate of incorporation. Only our review team can open it.</p>
            <form method="POST" action="{{ route('recruiter.verification.upload') }}" enctype="multipart/form-data" class="vup">
                @csrf
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                @if ($errors->any())<span class="msg-err" style="display:block">{{ $errors->first('document') }}</span>@endif
                <button class="btn primary" type="submit">{{ $r->verification_document ? 'Replace document' : 'Upload document' }}</button>
            </form>
            @if ($r->verification_document)<p class="sub" style="margin-top:12px">✓ A document is on file and waiting for review.</p>@endif
        @endif
    </div>
</section>
@endsection
