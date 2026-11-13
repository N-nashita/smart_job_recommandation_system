<div class="mo-sheet">
    <div class="mo-banner">
        <div class="mo-avatar {{ $c->photo_url ? 'has-photo' : '' }}">
            @if ($c->photo_url)<img src="{{ $c->photo_url }}" alt="">@else{{ \App\Support\CandidateInfo::initials($c) }}@endif
        </div>
        <div class="mo-who">
            <div class="mo-name">{{ $c->name }}</div>
            @if ($role)<div class="mo-role">{{ $role }}</div>@endif
            <div class="mo-contact">
                @foreach ($contact as $x)<span>{{ $x }}</span>@endforeach
            </div>
        </div>
    </div>

    <div class="mo-body">
        <section class="mo-sec">
            <h3 class="mo-title">About me</h3>
            <p class="mo-text">{{ $summary }}</p>
        </section>

        @if (count($exps))
            <section class="mo-sec">
                <h3 class="mo-title">Experience</h3>
                <div class="mo-timeline">
                    @foreach ($exps as $e)
                        <div class="mo-tl-item">
                            <div class="mo-tl-top">
                                <b>{{ $e['title'] ?? '' }}</b>
                                <span class="mo-date">{{ $period($e) }}</span>
                            </div>
                            <div class="mo-sub">{{ $e['company'] ?? '' }}</div>
                            @if (!empty($e['description']))<p class="mo-text">{{ $e['description'] }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (count($edus))
            <section class="mo-sec">
                <h3 class="mo-title">Education</h3>
                <div class="mo-timeline">
                    @foreach ($edus as $ed)
                        <div class="mo-tl-item">
                            <div class="mo-tl-top">
                                <b>{{ $ed['degree'] ?? '' }}</b>
                                <span class="mo-date">{{ $ed['year'] ?? '' }}</span>
                            </div>
                            <div class="mo-sub">{{ $ed['institution'] ?? '' }}</div>
                            <p class="mo-text">{{ collect([$ed['major'] ?? '', $ed['result'] ?? ''])->filter()->implode(' · ') }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (count($projs))
            <section class="mo-sec">
                <h3 class="mo-title">Projects</h3>
                <div class="mo-cards">
                    @foreach ($projs as $p)
                        <div class="mo-card">
                            <b>{{ $p['title'] ?? '' }}</b>
                            @if (!empty($p['description']))<p class="mo-text">{{ $p['description'] }}</p>@endif
                            @if (!empty($p['link']))<a href="{{ $p['link'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $p['link']) }}</a>@endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (count($skills))
            <section class="mo-sec">
                <h3 class="mo-title">Skills</h3>
                <div class="mo-chips">@foreach ($skills as $s)<span>{{ $s }}</span>@endforeach</div>
            </section>
        @endif

        <div class="mo-two">
            @if ($c->languages)
                <section class="mo-sec">
                    <h3 class="mo-title">Languages</h3>
                    <p class="mo-text">{{ $c->languages }}</p>
                </section>
            @endif
            @if ($certs->count())
                <section class="mo-sec">
                    <h3 class="mo-title">Certifications</h3>
                    @foreach ($certs as $x)<p class="mo-text">{{ $x }}</p>@endforeach
                </section>
            @endif
        </div>

        @if ($links->count())
            <section class="mo-sec">
                <h3 class="mo-title">Links</h3>
                <div class="mo-links">
                    @foreach ($links as $label => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener"><b>{{ $label }}</b> {{ preg_replace('#^https?://(www\.)?#', '', $url) }}</a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>

<style>
    body.candidate .mo-sheet { max-width: 860px; margin: 0 auto; font-family: var(--sans); color: var(--ink); line-height: 1.55; }
    body.candidate .mo-banner {
        display: flex; gap: 20px; align-items: center;
        padding: 28px 30px; border-radius: 22px; color: #fff;
        background: linear-gradient(135deg, var(--accent-deep), #6E0F12);
        margin-bottom: 28px;
    }
    body.candidate .mo-avatar { flex: 0 0 72px; width: 72px; height: 72px; border-radius: 20px; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); display: grid; place-items: center; font-weight: 700; font-size: 1.8rem; overflow: hidden; }
    body.candidate .mo-avatar img { width: 100%; height: 100%; object-fit: cover; }
    body.candidate .mo-avatar.has-photo { flex-basis: 96px; width: 96px; height: 96px; border-radius: 24px; border: 2px solid rgba(255,255,255,.5); }
    body.candidate .mo-who { min-width: 0; }
    body.candidate .mo-name { font-family: var(--head); font-size: 1.8rem; font-weight: 800; line-height: 1.2; }
    body.candidate .mo-role { color: #ffdada; font-size: .95rem; margin-top: 2px; }
    body.candidate .mo-contact { display: flex; flex-wrap: wrap; gap: 6px 16px; margin-top: 10px; font-size: .84rem; color: #f8caca; }
    body.candidate .mo-body { display: grid; gap: 26px; }
    body.candidate .mo-sheet .mo-sec { margin: 0; padding: 0; break-inside: avoid; }
    body.candidate .mo-title { display: flex; align-items: center; gap: 12px; font-family: var(--head); font-size: 1rem; font-weight: 700; color: var(--accent-deep); margin: 0 0 14px; }
    body.candidate .mo-title::after { content: ''; flex: 1; height: 1px; background: var(--line); }
    body.candidate .mo-text { font-size: .9rem; margin: 4px 0 0; color: var(--ink); white-space: pre-line; overflow-wrap: anywhere; }
    body.candidate .mo-timeline { position: relative; padding-left: 22px; display: grid; gap: 18px; }
    body.candidate .mo-timeline::before { content: ''; position: absolute; left: 5px; top: 6px; bottom: 6px; width: 2px; background: var(--line); }
    body.candidate .mo-tl-item { position: relative; break-inside: avoid; }
    body.candidate .mo-tl-item::before { content: ''; position: absolute; left: -22px; top: 6px; width: 12px; height: 12px; border-radius: 50%; background: #fff; border: 3px solid var(--accent-deep); box-sizing: border-box; }
    body.candidate .mo-tl-top { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    body.candidate .mo-date { font-size: .82rem; color: var(--accent-deep); font-weight: 600; }
    body.candidate .mo-sub { color: var(--muted); font-size: .88rem; font-weight: 500; }
    body.candidate .mo-cards { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    body.candidate .mo-card { padding: 14px 16px; border: 1px solid var(--line); border-radius: 16px; background: rgba(255,255,255,.7); break-inside: avoid; min-width: 0; }
    body.candidate .mo-card a { display: inline-block; margin-top: 6px; font-size: .8rem; color: var(--accent-deep); text-decoration: underline; text-underline-offset: 3px; overflow-wrap: anywhere; }
    body.candidate .mo-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    body.candidate .mo-chips span { background: linear-gradient(135deg, var(--accent-deep), #6E0F12); color: #fff; border-radius: 999px; padding: 5px 14px; font-size: .8rem; font-weight: 500; }
    body.candidate .mo-two { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 26px; }
    body.candidate .mo-links { display: flex; flex-wrap: wrap; gap: 8px 22px; font-size: .86rem; }
    body.candidate .mo-links a { color: var(--ink); overflow-wrap: anywhere; }
    body.candidate .mo-links b { color: var(--accent-deep); margin-right: 6px; }
    @media (max-width: 760px) {
        body.candidate .mo-banner { flex-wrap: wrap; padding: 22px; }
        body.candidate .mo-cards, body.candidate .mo-two { grid-template-columns: 1fr; }
    }
</style>
