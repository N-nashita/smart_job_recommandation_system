@php($vs = auth('recruiter')->user()->verification_status)
@if ($vs !== 'verified')
    @php($note = auth('recruiter')->user()->verification_note)
    <div class="vbanner {{ $vs }}" role="status">
        <svg class="ic"><use href="#i-shield"/></svg>
        <div>
            @if ($vs === 'rejected')
                <strong>Verification failed</strong>
                <span>{{ $note ?: 'We could not verify your company.' }} Upload a clearer certificate to try again.</span>
            @else
                <strong>Not verified yet</strong>
                <span>Your company is being reviewed. You can look around, but you cannot post jobs, shortlist or call candidates until it is verified.</span>
            @endif
        </div>
        <a class="btn light sm" href="{{ route('recruiter.verification') }}">{{ $vs === 'rejected' ? 'Fix and resubmit' : 'See status' }}</a>
    </div>
@endif
