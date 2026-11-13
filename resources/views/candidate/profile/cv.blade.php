@extends('layouts.candidate')

@section('title', 'CV — '.$c->name)
@section('hide-topbar', '1')

@section('main')
@php
    $skills = $c->skills ?? [];
    $exps   = $c->experiences ?? [];
    $projs  = $c->projects ?? [];
    $certs  = collect(preg_split('/\r\n|\r|\n/', (string) $c->certifications))->map('trim')->filter()->values();
    $links  = collect(['LinkedIn' => $c->linkedin, 'GitHub' => $c->github, 'Portfolio' => $c->portfolio])->filter();
    $contact = collect([$c->phone, $c->email, $c->location])->filter()->all();
    $role   = collect([$c->category, $c->experience])->filter()->implode(' · ');
    $summary = $c->summary ?: 'Dedicated professional with experience in '.($c->category ?? 'my field').', seeking to contribute to a forward-thinking organization.'
        .($c->degree ? ' I hold a '.$c->degree.($c->major ? ' in '.$c->major : '').($c->institution ? ' from '.$c->institution : '').'.' : '');
    $period = function ($e) {
        $s = $e['start'] ?? ''; $en = $e['end'] ?? '';
        if (! $s && ! $en) return '';
        return trim($s.' – '.($en ?: 'Present'));
    };

    $edus = collect([[
        'degree' => $c->degree, 'institution' => $c->institution, 'major' => $c->major,
        'year' => $c->grad_year, 'result' => $c->cgpa ? 'CGPA: '.$c->cgpa : '',
    ]])
        ->filter(fn ($e) => filled($e['degree']) || filled($e['institution']))
        ->concat(collect($c->educations ?? []))
        ->sortByDesc(fn ($e) => (int) ($e['year'] ?? 0))
        ->values()->all();
    $templates = [
        'classic'  => ['Classic', 'Two-column layout, suitable for any job'],
        'modern'   => ['Modern', 'Banner + timeline'],
        'europass' => ['Europass', 'Formal layout for international roles or scholarships'],
    ];
@endphp

<div class="page-head">
    <div>
        <div class="eyebrow">Candidate portal</div>
        <h1 style="margin-top:10px">Curriculum Vitae</h1>
        <p id="sub">Auto-generated from your profile. Select Template then Print or Save as PDF.</p>
    </div>
    <div class="acts">
        <a href="{{ route('candidate.profile.edit') }}" class="btn ghost sm">
            <svg class="ic"><use href="#i-arrow-r"/></svg> Edit profile
        </a>
        <button class="btn primary" onclick="window.print()">
            <svg class="ic"><use href="#i-file"/></svg> Print / Save PDF
        </button>
    </div>
</div>

<div class="cv-picker">
    @foreach ($templates as $key => [$label, $desc])
        <a href="{{ route('candidate.profile.cv', ['template' => $key]) }}" class="cv-pick {{ $template === $key ? 'on' : '' }}">
            <b>{{ $label }}</b>
            <span>{{ $desc }}</span>
        </a>
    @endforeach
</div>

<section class="glass card cv-wrap reveal">
    @include('candidate.profile.cv-templates.'.$template)
</section>

<style>
    body.candidate .cv-picker { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    body.candidate .cv-pick {
        display: block; padding: 14px 18px; border-radius: 18px;
        background: var(--card); border: 1.5px solid var(--line);
        transition: .2s;
    }
    body.candidate .cv-pick b { display: block; font-family: var(--head); font-size: .98rem; color: var(--ink); }
    body.candidate .cv-pick span { font-size: .8rem; color: var(--muted); }
    body.candidate .cv-pick:hover { border-color: var(--accent); transform: translateY(-2px); }
    body.candidate .cv-pick.on { border-color: var(--accent-deep); box-shadow: 0 0 0 3px rgba(150, 23, 26, .12); background: #fff; }
    body.candidate .cv-pick.on b { color: var(--accent-deep); }

    body.candidate .cv-wrap.glass.card { padding: 36px 40px; overflow: hidden; }
    body.candidate .cv-wrap * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    @media (max-width: 760px) {
        body.candidate .cv-picker { grid-template-columns: 1fr; }
        body.candidate .cv-wrap.glass.card { padding: 22px 18px; }
    }
    @media print {
        body.candidate .side, body.candidate .page-head, body.candidate .cv-picker,
        body.candidate .no-print, body.candidate .toast { display: none !important; }
        body.candidate .shell { display: block; width: 100%; padding: 0; }
        body.candidate .cv-wrap.glass.card {
            padding: 0; border: 0; box-shadow: none; background: #fff;
            backdrop-filter: none; -webkit-backdrop-filter: none;
        }
    }
</style>
@endsection
