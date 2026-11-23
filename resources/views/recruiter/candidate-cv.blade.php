@extends('layouts.base')

@section('title', 'CV — '.$c->name)
@section('bodyclass', 'candidate')

@push('styles')
<style>
    .cvpage { --sans: 'Outfit', system-ui, -apple-system, 'Segoe UI', sans-serif; --head: var(--sans); --card: #fff;
        max-width: 980px; margin: 0 auto; padding: 24px 20px 60px; display: grid; gap: 16px; }
    .cvbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    .cvbar h1 { font-size: 1.4rem; }
    .cvbar .acts { display: flex; gap: 10px; flex-wrap: wrap; }
    .cvpicker { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .cvpick { display: block; padding: 12px 16px; border-radius: 16px; background: #fff; border: 1.5px solid var(--line); color: inherit; text-decoration: none; }
    .cvpick b { display: block; color: var(--ink); }
    .cvpick span { font-size: .8rem; color: var(--muted); }
    .cvpick.on { border-color: var(--accent-deep); background: var(--mint); }
    .cvwrap { padding: 36px 40px; border-radius: 24px; background: #fff; border: 1px solid var(--line); overflow: hidden; }
    .cvwrap * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    @media (max-width: 760px) { .cvpicker { grid-template-columns: 1fr; } .cvwrap { padding: 22px 18px; } }
    @media print {
        .cvbar, .cvpicker { display: none !important; }
        body.candidate { background: #fff; }
        .cvpage { padding: 0; max-width: none; }
        .cvwrap { padding: 0; border: 0; border-radius: 0; }
    }
</style>
@endpush

@section('content')
@php
    $templates = ['classic' => ['Classic', 'Two-column layout'], 'modern' => ['Modern', 'Banner + timeline'], 'europass' => ['Europass', 'Formal layout']];
@endphp
<div class="cvpage">
    <div class="cvbar">
        <div><h1>{{ $c->name }} — CV</h1><p class="sub" style="color:var(--muted)">Applied for {{ $a->job->title }}</p></div>
        <div class="acts">
            <a class="btn ghost sm" href="{{ route('recruiter.applicants.profile', $a->id) }}">← Profile</a>
            <button class="btn primary sm" type="button" onclick="window.print()">Print / Save as PDF</button>
        </div>
    </div>

    <div class="cvpicker">
        @foreach ($templates as $key => [$label, $desc])
            <a href="{{ route('recruiter.applicants.cv', ['application' => $a->id, 'template' => $key]) }}" class="cvpick {{ $template === $key ? 'on' : '' }}"><b>{{ $label }}</b><span>{{ $desc }}</span></a>
        @endforeach
    </div>

    <section class="cvwrap">
        @include('candidate.profile.cv-templates.'.$template)
    </section>
</div>

@if (request()->boolean('download'))
    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 500));</script>
@endif
@endsection
