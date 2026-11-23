@extends('layouts.recruiter')

@section('title', $c->name.' — JobSphere')

@section('main')
@php
    $ini = \App\Support\CandidateInfo::initials($c);
    $color = \App\Support\CandidateInfo::color($c);
    $bars = [['Skills', $a->skills_score], ['Experience', $a->experience_score], ['Location', $a->location_score]];
@endphp

<div class="page-head">
    <div><div class="eyebrow">Candidate profile</div><h1 style="margin-top:10px">{{ $c->name }}</h1>
        <p>Applied for {{ $a->job->title }} · {{ $statusLabel }}</p></div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('recruiter.applicants') }}" class="btn ghost">← Applicants</a>
        <a href="{{ route('recruiter.applicants.cv', ['application' => $a->id, 'download' => 1]) }}" target="_blank" rel="noopener" class="btn primary">Download CV</a>
    </div>
</div>

<section class="split">
    <div class="stack">
        <div class="glass card">
            <div class="cohead">
                @if ($c->photo_url)<img class="cpphoto" src="{{ $c->photo_url }}" alt="">@else<div class="avatar {{ $color }} big">{{ $ini }}</div>@endif
                <div>
                    <h2>{{ $c->name }}</h2>
                    <p class="sub">{{ $role ?: 'No role added' }}</p>
                    <p class="sub">{{ collect([$c->location, $c->email, $c->phone])->filter()->implode(' · ') }}</p>
                </div>
            </div>
            <p class="coabout">{{ $realSummary ?: 'This candidate has not written a summary yet.' }}</p>
        </div>

        @if (count($exps))
            <div class="glass card"><h3>Experience</h3>
                @foreach ($exps as $e)
                    <div class="cpitem"><div class="cptop"><b>{{ $e['title'] ?? '' }}</b><span>{{ $period($e) }}</span></div>
                        <div class="cpsub">{{ $e['company'] ?? '' }}</div>
                        @if (!empty($e['description']))<p class="cpdesc">{{ $e['description'] }}</p>@endif</div>
                @endforeach
            </div>
        @endif

        @if (count($projs))
            <div class="glass card"><h3>Projects</h3>
                @foreach ($projs as $p)
                    <div class="cpitem"><div class="cptop"><b>{{ $p['title'] ?? '' }}</b>
                        @if (!empty($p['link']))<a href="{{ $p['link'] }}" target="_blank" rel="noopener noreferrer">Link ↗</a>@endif</div>
                        @if (!empty($p['description']))<p class="cpdesc">{{ $p['description'] }}</p>@endif</div>
                @endforeach
            </div>
        @endif

        @if (count($edus))
            <div class="glass card"><h3>Education</h3>
                @foreach ($edus as $ed)
                    <div class="cpitem"><div class="cptop"><b>{{ $ed['degree'] ?? '' }}</b><span>{{ $ed['year'] ?? '' }}</span></div>
                        <div class="cpsub">{{ $ed['institution'] ?? '' }}</div>
                        <p class="cpdesc">{{ collect([$ed['major'] ?? '', $ed['result'] ?? ''])->filter()->implode(' · ') }}</p></div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="stack">
        <div class="glass card">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:6px">
                <div class="ring {{ $a->match_score < 85 ? 'warm' : '' }}" style="--p:{{ $a->match_score }};width:62px;height:62px"><b>{{ $a->match_score }}</b></div>
                <div><h3>Match for this job</h3><p class="sub">{{ $a->job->title }}</p></div>
            </div>
            <div class="bd">
                @foreach ($bars as [$label, $v])<div><span>{{ $label }}</span><i style="--v:{{ (int) $v }}%"></i><b>{{ (int) $v }}%</b></div>@endforeach
            </div>
        </div>

        @if (count($skills))
            <div class="glass card"><h3>Skills</h3><div class="skills" style="margin-top:12px">@foreach ($skills as $s)<span>{{ $s }}</span>@endforeach</div></div>
        @endif

        @if ($links->count() || $c->languages || $certs->count())
            <div class="glass card">
                @if ($links->count())<h3>Links</h3><ul class="cplist">
                    @foreach ($links as $label => $url)<li><b>{{ $label }}:</b> <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ preg_replace('#^https?://(www\.)?#', '', $url) }}</a></li>@endforeach</ul>@endif
                @if ($c->languages)<h3 style="margin-top:16px">Languages</h3><p class="cpdesc">{{ $c->languages }}</p>@endif
                @if ($certs->count())<h3 style="margin-top:16px">Certifications</h3><ul class="cplist">@foreach ($certs as $x)<li>{{ $x }}</li>@endforeach</ul>@endif
            </div>
        @endif
    </div>
</section>
@endsection
