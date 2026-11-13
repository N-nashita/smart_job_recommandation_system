@extends('layouts.recruiter')

@section('title', 'Company — JobSphere')

@section('main')
@php
    $r = auth('recruiter')->user();
    $verified = $r->verification_status === 'verified';
@endphp

<div class="page-head">
    <div><div class="eyebrow">Recruiter workspace</div><h1 style="margin-top:10px">Company</h1>
        <p>This is how candidates see your company.</p></div>
    <span class="vstatus {{ $r->verification_status }}">{{ $verified ? 'Verified' : 'Not verified' }}</span>
</div>

@if (session('status'))<div class="vflash">{{ session('status') }}</div>@endif

<section class="split">
    <div class="stack">
        {{-- how candidates see it --}}
        <div class="glass card coview">
            <div class="cohead">
                <div class="avatar mint big">{{ mb_strtoupper(mb_substr($r->company_name, 0, 1)) }}</div>
                <div>
                    <h2>{{ $r->company_name }}</h2>
                    <p class="sub">{{ $r->industry }} · {{ $r->hq_location }}</p>
                    <span class="vb" @if (! $verified) style="background:var(--blush);color:#a65a40" @endif>
                        <svg class="ic"><use href="#i-shield"/></svg><b>{{ $verified ? 'Verified company' : 'Not verified yet' }}</b>
                    </span>
                </div>
            </div>

            <p class="coabout">{{ $r->about ?: 'No description yet. Add a short intro on the right so candidates know who you are.' }}</p>

            <dl class="codl">
                <div><dt>Website</dt><dd>{{ $r->website ?: '—' }}</dd></div>
                <div><dt>Registration no.</dt><dd>{{ $r->registration_no }}</dd></div>
                <div><dt>Contact</dt><dd>{{ $r->name }}@if ($r->job_title), {{ $r->job_title }}@endif</dd></div>
            </dl>

            <div class="costats">
                <div><b>{{ $stats['open'] }}</b><span>Open jobs</span></div>
                <div><b>{{ $stats['applicants'] }}</b><span>Applicants</span></div>
                <div><b>{{ $stats['hired'] }}</b><span>Hired</span></div>
            </div>
        </div>

        <div class="glass card">
            <div class="card-head"><div><h3>Open positions</h3></div><a href="{{ route('recruiter.jobs') }}" class="btn ghost sm">Manage jobs</a></div>
            <ul class="feed">
                @forelse ($openJobs as $j)
                    <li><span class="pip"><svg class="ic"><use href="#i-brief"/></svg></span><div><b>{{ $j->title }}</b><small>{{ $j->location }} · {{ $j->type }}</small></div></li>
                @empty
                    <li class="sub">{{ $verified ? 'No open jobs right now.' : 'Jobs appear here once your company is verified.' }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="stack">
        <div class="glass card">
            <h3>Edit profile</h3>
            <form method="POST" action="{{ route('recruiter.company.update') }}" class="jform" style="margin-top:14px" novalidate>
                @csrf @method('PUT')
                <div class="f"><label for="company_name">Company name</label><input id="company_name" name="company_name" value="{{ old('company_name', $r->company_name) }}" required>
                    @error('company_name')<span class="msg-err">{{ $message }}</span>@enderror</div>
                <div class="f"><label for="industry">Industry</label>
                    <select class="sel" id="industry" name="industry" style="width:100%">
                        @foreach ($industries as $i)<option @if (old('industry', $r->industry) === $i) selected @endif>{{ $i }}</option>@endforeach
                    </select>
                    @error('industry')<span class="msg-err">{{ $message }}</span>@enderror</div>
                <div class="f wide"><label for="hq_location">Headquarters</label><input id="hq_location" name="hq_location" value="{{ old('hq_location', $r->hq_location) }}" required>
                    @error('hq_location')<span class="msg-err">{{ $message }}</span>@enderror</div>
                <div class="f wide"><label for="about">About the company <small style="color:var(--muted)">(max 1500 characters)</small></label>
                    <textarea id="about" name="about" rows="5" maxlength="1500" placeholder="What does your company do? What is it like to work there?">{{ old('about', $r->about) }}</textarea>
                    @error('about')<span class="msg-err">{{ $message }}</span>@enderror</div>
                <div class="f"><label for="name">Your name</label><input id="name" name="name" value="{{ old('name', $r->name) }}" required>
                    @error('name')<span class="msg-err">{{ $message }}</span>@enderror</div>
                <div class="f"><label for="job_title">Your job title</label><input id="job_title" name="job_title" value="{{ old('job_title', $r->job_title) }}">
                    @error('job_title')<span class="msg-err">{{ $message }}</span>@enderror</div>
                <div class="f wide"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone', $r->phone) }}" required>
                    @error('phone')<span class="msg-err">{{ $message }}</span>@enderror</div>
                <div class="wide"><button class="btn primary" type="submit">Save changes</button></div>
            </form>
            <p class="sub" style="margin-top:14px">Email, website and registration number are used for verification and cannot be changed here.
                @if (! $verified) <a href="{{ route('recruiter.verification') }}">Fix them on the Verification page.</a>@endif</p>
        </div>
    </div>
</section>
@endsection
