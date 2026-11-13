<div class="cv-sheet">
    <div class="cv-head">
        <div class="cv-avatar {{ $c->photo_url ? 'has-photo' : '' }}">
            @if ($c->photo_url)<img src="{{ $c->photo_url }}" alt="">@else{{ \App\Support\CandidateInfo::initials($c) }}@endif
        </div>
        <div>
            <div class="cv-name">{{ $c->name }}</div>
            <div class="cv-line">{{ $role }}</div>
            <div class="cv-line">{{ implode(' · ', $contact) }}</div>
        </div>
    </div>

    <div class="cv-body">
        <div class="cv-col">
            <section class="cv-sec">
                <h3 class="cv-sec-title">Summary</h3>
                <p class="cv-text">{{ $summary }}</p>
            </section>

            @if (count($exps))
                <section class="cv-sec">
                    <h3 class="cv-sec-title">Experience</h3>
                    @foreach ($exps as $e)
                        <div class="cv-item">
                            <div class="cv-item-top">
                                <b>{{ $e['title'] ?? '' }}</b>
                                <span class="cv-right">{{ $period($e) }}</span>
                            </div>
                            <div class="cv-school">{{ $e['company'] ?? '' }}</div>
                            @if (!empty($e['description']))<div class="cv-detail">{{ $e['description'] }}</div>@endif
                        </div>
                    @endforeach
                </section>
            @endif

            @if (count($projs))
                <section class="cv-sec">
                    <h3 class="cv-sec-title">Projects</h3>
                    @foreach ($projs as $p)
                        <div class="cv-item">
                            <div class="cv-item-top">
                                <b>{{ $p['title'] ?? '' }}</b>
                                @if (!empty($p['link']))<a class="cv-right" href="{{ $p['link'] }}" target="_blank" rel="noopener">Link</a>@endif
                            </div>
                            @if (!empty($p['description']))<div class="cv-detail">{{ $p['description'] }}</div>@endif
                        </div>
                    @endforeach
                </section>
            @endif

            @if (count($edus))
                <section class="cv-sec">
                    <h3 class="cv-sec-title">Education</h3>
                    @foreach ($edus as $ed)
                        <div class="cv-item">
                            <div class="cv-item-top">
                                <b>{{ $ed['degree'] ?? '' }}</b>
                                <span class="cv-right">{{ $ed['year'] ?? '' }}</span>
                            </div>
                            <div class="cv-school">{{ $ed['institution'] ?? '' }}</div>
                            <div class="cv-detail">{{ collect([$ed['major'] ?? '', $ed['result'] ?? ''])->filter()->implode(' · ') }}</div>
                        </div>
                    @endforeach
                </section>
            @endif

            @if ($certs->count())
                <section class="cv-sec">
                    <h3 class="cv-sec-title">Certifications</h3>
                    <ul class="cv-list">@foreach ($certs as $x)<li>{{ $x }}</li>@endforeach</ul>
                </section>
            @endif
        </div>

        <div class="cv-col cv-side">
            <section class="cv-sec">
                <h3 class="cv-sec-title">Contact</h3>
                <ul class="cv-list">
                    <li><b>Phone:</b> {{ $c->phone ?: '—' }}</li>
                    <li><b>Email:</b> {{ $c->email ?: '—' }}</li>
                    <li><b>Location:</b> {{ $c->location ?: '—' }}</li>
                    @foreach ($links as $label => $url)
                        <li><b>{{ $label }}:</b> <a href="{{ $url }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $url) }}</a></li>
                    @endforeach
                </ul>
            </section>

            @if (count($skills))
                <section class="cv-sec">
                    <h3 class="cv-sec-title">Skills</h3>
                    <div class="cv-chips">@foreach ($skills as $s)<span class="cv-chip">{{ $s }}</span>@endforeach</div>
                </section>
            @endif

            @if ($c->languages)
                <section class="cv-sec">
                    <h3 class="cv-sec-title">Languages</h3>
                    <p class="cv-text">{{ $c->languages }}</p>
                </section>
            @endif
        </div>
    </div>
</div>

<style>
    body.candidate .cv-sheet { max-width: 860px; margin: 0 auto; font-family: var(--sans); color: var(--ink); line-height: 1.55; }
    body.candidate .cv-head { display: flex; gap: 18px; align-items: center; border-bottom: 3px solid var(--accent-deep); padding-bottom: 18px; margin-bottom: 26px; }
    body.candidate .cv-avatar { flex: 0 0 56px; width: 56px; height: 56px; border-radius: 14px; background: var(--accent-deep); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 1.5rem; overflow: hidden; }
    body.candidate .cv-avatar img { width: 100%; height: 100%; object-fit: cover; }
    body.candidate .cv-avatar.has-photo { flex-basis: 88px; width: 88px; height: 88px; border-radius: 18px; }
    body.candidate .cv-name { font-size: 1.6rem; font-weight: 800; font-family: var(--head); margin: 0 0 4px; }
    body.candidate .cv-line { color: var(--muted); font-size: .9rem; margin: 2px 0; }
    body.candidate .cv-body { display: grid; grid-template-columns: minmax(0, 1fr) 260px; gap: 32px; }
    body.candidate .cv-col { min-width: 0; }
    body.candidate .cv-sheet .cv-sec { margin: 0 0 26px; padding: 0; break-inside: avoid; }
    body.candidate .cv-sheet .cv-col > .cv-sec:last-child { margin-bottom: 0; }
    body.candidate .cv-sec-title { font-size: .92rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--accent-deep); border-bottom: 1px solid var(--line); padding-bottom: 8px; margin: 0 0 14px; }
    body.candidate .cv-text { font-size: .92rem; margin: 0; overflow-wrap: anywhere; }
    body.candidate .cv-item { margin-bottom: 16px; break-inside: avoid; }
    body.candidate .cv-item:last-child { margin-bottom: 0; }
    body.candidate .cv-item-top { display: flex; justify-content: space-between; gap: 12px; }
    body.candidate .cv-school { font-weight: 600; font-size: .9rem; margin: 4px 0 2px; }
    body.candidate .cv-detail { color: var(--muted); font-size: .84rem; white-space: pre-line; overflow-wrap: anywhere; }
    body.candidate .cv-right { font-size: .84rem; color: var(--accent-deep); font-weight: 600; white-space: nowrap; }
    body.candidate .cv-list { list-style: none; margin: 0; padding: 0; }
    body.candidate .cv-list li { margin-bottom: 10px; font-size: .9rem; overflow-wrap: anywhere; }
    body.candidate .cv-list li:last-child { margin-bottom: 0; }
    body.candidate .cv-list a { color: var(--accent-deep); text-decoration: underline; text-underline-offset: 3px; }
    body.candidate .cv-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    body.candidate .cv-chip { background: var(--mint); color: var(--accent-deep); border-radius: 999px; padding: 4px 12px; font-size: .8rem; font-weight: 500; }
    body.candidate .cv-side { border-left: 1px solid var(--line); padding-left: 24px; }
    @media (max-width: 760px) {
        body.candidate .cv-body { grid-template-columns: 1fr; gap: 24px; }
        body.candidate .cv-side { border-left: 0; padding-left: 0; border-top: 1px solid var(--line); padding-top: 20px; }
        body.candidate .cv-head { flex-wrap: wrap; }
    }
</style>
