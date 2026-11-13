<div class="eu-sheet">
    <div class="eu-head">
        <div class="eu-head-main">
            <div class="eu-kicker">Curriculum Vitae</div>
            <h1 class="eu-name">{{ $c->name }}</h1>
            <div class="eu-contact">
                @if ($c->email)<span><b>Email</b> {{ $c->email }}</span>@endif
                @if ($c->phone)<span><b>Phone</b> {{ $c->phone }}</span>@endif
                @if ($c->location)<span><b>Address</b> {{ $c->location }}</span>@endif
            @foreach ($links as $label => $url)
                <span><b>{{ $label }}</b> <a href="{{ $url }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $url) }}</a></span>
            @endforeach
            </div>
        </div>
        @if ($c->photo_url)
            <img class="eu-photo" src="{{ $c->photo_url }}" alt="">
        @endif
    </div>

    <section class="eu-sec">
        <h3 class="eu-title">About me</h3>
        <div class="eu-row">
            <div class="eu-label">{{ $role }}</div>
            <div class="eu-text">{{ $summary }}</div>
        </div>
    </section>

    @if (count($exps))
        <section class="eu-sec">
            <h3 class="eu-title">Work experience</h3>
            @foreach ($exps as $e)
                <div class="eu-row">
                    <div class="eu-label">{{ $period($e) }}</div>
                    <div>
                        <div class="eu-strong">{{ $e['title'] ?? '' }}</div>
                        <div class="eu-sub">{{ $e['company'] ?? '' }}</div>
                        @if (!empty($e['description']))<div class="eu-text">{{ $e['description'] }}</div>@endif
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    @if (count($edus))
        <section class="eu-sec">
            <h3 class="eu-title">Education and training</h3>
            @foreach ($edus as $ed)
                <div class="eu-row">
                    <div class="eu-label">{{ $ed['year'] ?? '' }}</div>
                    <div>
                        <div class="eu-strong">{{ $ed['degree'] ?? '' }}{{ !empty($ed['major']) ? ' in '.$ed['major'] : '' }}</div>
                        <div class="eu-sub">{{ $ed['institution'] ?? '' }}</div>
                        @if (!empty($ed['result']))<div class="eu-text">{{ $ed['result'] }}</div>@endif
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    @if (count($skills) || $c->languages)
        <section class="eu-sec">
            <h3 class="eu-title">Personal skills</h3>
            @if ($c->languages)
                <div class="eu-row">
                    <div class="eu-label">Languages</div>
                    <div class="eu-text">{{ $c->languages }}</div>
                </div>
            @endif
            @if (count($skills))
                <div class="eu-row">
                    <div class="eu-label">Job-related skills</div>
                    <div class="eu-text">{{ implode(' · ', $skills) }}</div>
                </div>
            @endif
        </section>
    @endif

    @if (count($projs))
        <section class="eu-sec">
            <h3 class="eu-title">Projects</h3>
            @foreach ($projs as $p)
                <div class="eu-row">
                    <div class="eu-label">{{ $p['year'] ?? '' }}</div>
                    <div>
                        <div class="eu-strong">{{ $p['title'] ?? '' }}</div>
                        @if (!empty($p['link']))
                            <div class="eu-sub">
                                <a href="{{ $p['link'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $p['link']) }}</a>
                            </div>
                        @endif
                        @if (!empty($p['description']))
                            <div class="eu-text">{{ $p['description'] }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    @if ($certs->count())
        <section class="eu-sec">
            <h3 class="eu-title">Certifications</h3>
            @foreach ($certs as $x)
                <div class="eu-row">
                    <div class="eu-label"></div>
                    <div class="eu-strong">{{ $x }}</div>
                </div>
            @endforeach
        </section>
    @endif
</div>

<style>
    body.candidate .eu-sheet { --eu: #0E47CB; max-width: 820px; margin: 0 auto; font-family: var(--sans); color: #1a1a1a; line-height: 1.55; }
    body.candidate .eu-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; padding-bottom: 14px; border-bottom: 3px solid var(--eu); margin-bottom: 22px; }
    body.candidate .eu-head-main { min-width: 0; }
    body.candidate .eu-photo { flex: 0 0 auto; width: 100px; height: 124px; object-fit: cover; border-radius: 6px; border: 1px solid #cfd8ee; }
    body.candidate .eu-kicker { font-size: .76rem; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: var(--eu); }
    body.candidate .eu-name { font-family: var(--head); font-size: 2rem; font-weight: 800; margin: 4px 0 10px; color: #111; }
    body.candidate .eu-contact { display: flex; flex-wrap: wrap; gap: 4px 22px; font-size: .86rem; color: #333; }
    body.candidate .eu-contact b { color: var(--eu); margin-right: 6px; font-weight: 600; }
    body.candidate .eu-contact span { overflow-wrap: anywhere; }
    body.candidate .eu-contact a { color: var(--eu); text-decoration: underline; text-underline-offset: 3px; overflow-wrap: anywhere; }
    body.candidate .eu-sub a { color: var(--eu); text-decoration: underline; text-underline-offset: 3px; }
    body.candidate .eu-sheet .eu-sec { margin: 0 0 24px; padding: 0; }
    body.candidate .eu-title {
        font-size: .95rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
        color: var(--eu); background: #eef3ff; border-left: 4px solid var(--eu);
        padding: 7px 12px; margin: 0 0 14px;
    }
    body.candidate .eu-row { display: grid; grid-template-columns: 170px minmax(0, 1fr); gap: 20px; margin-bottom: 14px; break-inside: avoid; }
    body.candidate .eu-label { font-size: .84rem; font-weight: 600; color: #444; overflow-wrap: anywhere; }
    body.candidate .eu-strong { font-weight: 700; font-size: .95rem; color: #111; }
    body.candidate .eu-sub { font-size: .88rem; color: var(--eu); font-weight: 500; }
    body.candidate .eu-text { font-size: .9rem; color: #222; white-space: pre-line; overflow-wrap: anywhere; }
    @media (max-width: 640px) {
        body.candidate .eu-row { grid-template-columns: 1fr; gap: 2px; }
    }
</style>
