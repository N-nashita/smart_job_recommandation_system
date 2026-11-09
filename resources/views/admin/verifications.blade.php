@extends('layouts.base')

@section('title', 'Review companies')

@push('styles')
    <link rel="stylesheet" href="{{ asset('style/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('style/pages.css') }}">
@endpush

@section('bodyclass', 'app')

@section('content')
<div class="wrap" style="padding:32px 0 60px; display:grid; gap:22px">
    <div class="page-head">
        <div><div class="eyebrow">Admin</div><h1 style="margin-top:10px">Company verification</h1>
            <p>{{ $pending->count() }} waiting for review</p></div>
        <a href="{{ url('/dashboard') }}" class="btn ghost">Back</a>
    </div>

    @if (session('status'))<div class="vflash">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="vflash bad">{{ $errors->first() }}</div>@endif

    @forelse ($pending as $r)
        @php($checks = json_decode($r->verification_checks ?? '[]', true) ?: [])
        <section class="glass card">
            <div class="card-head">
                <div><h3>{{ $r->company_name }}</h3>
                    <p class="sub">{{ $r->name }} · {{ $r->email }} · {{ $r->phone }}<br>
                        Reg. no. {{ $r->registration_no }} · {{ $r->website ?: 'no website' }} · {{ $r->industry }} · {{ $r->hq_location }}</p></div>
                @if ($r->verification_document)
                    <a class="btn ghost sm" href="{{ route('admin.verifications.document', $r->id) }}">Open document</a>
                @else
                    <span class="tag">No document yet</span>
                @endif
            </div>
            @if ($checks)
                <ul class="vchecks">
                    @foreach ($checks as $c)
                        <li class="{{ $c['status'] }}"><span class="vic">{{ ['pass'=>'✓','warn'=>'!','fail'=>'✕','skip'=>'–'][$c['status']] ?? '?' }}</span>
                            <div><b>{{ $c['label'] }}</b><small>{{ $c['detail'] }}</small></div></li>
                    @endforeach
                </ul>
            @endif
            <div class="vreview">
                <form method="POST" action="{{ route('admin.verifications.approve', $r->id) }}">@csrf
                    <button class="btn primary" type="submit">Approve</button></form>
                <form method="POST" action="{{ route('admin.verifications.reject', $r->id) }}" class="vrej">@csrf
                    <input name="note" placeholder="Reason shown to the recruiter" required minlength="5">
                    <button class="btn danger" type="submit">Reject</button></form>
            </div>
        </section>
    @empty
        <div class="glass card empty">Nothing waiting for review.</div>
    @endforelse

    @if ($reviewed->count())
        <section class="glass card">
            <h3 style="margin-bottom:12px">Recently reviewed</h3>
            <ul class="feed">
                @foreach ($reviewed as $r)
                    <li><span class="pip {{ $r->verification_status === 'rejected' ? 'c' : '' }}">{{ $r->verification_status === 'verified' ? '✓' : '✕' }}</span>
                        <div><b>{{ $r->company_name }}</b> {{ $r->verification_status }}<small>{{ $r->reviewed_at?->diffForHumans() ?? '—' }}</small></div></li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
