@php($vs = auth('recruiter')->user()->verification_status)
@if ($vs !== 'verified')
    <div class="vbanner pending" role="status">
        <svg class="ic"><use href="#i-shield"/></svg>
        <div>
            <strong>Not verified yet</strong>
            <span>Some automatic checks did not pass. You cannot post jobs, shortlist or call candidates until your company is verified.</span>
        </div>
        <a class="btn light sm" href="{{ route('recruiter.verification') }}">Fix and verify</a>
    </div>
@endif
