@extends('layouts.base')

@section('title', 'JobSphere — Work that feels like you')

@section('content')
    <!-- icon sprite -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
        <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M7 17 17 7M8 7h9v9"/></symbol>
        <symbol id="i-heart" viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></symbol>
        <symbol id="i-spark" viewBox="0 0 24 24"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8zM19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8z"/></symbol>
        <symbol id="i-pin" viewBox="0 0 24 24"><path d="M12 21s-6-5.2-6-10a6 6 0 0 1 12 0c0 4.8-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
        <symbol id="i-compass" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5z"/></symbol>
        <symbol id="i-brief" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 4.5-3.4 7.7-8 9-4.6-1.3-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/></symbol>
        <symbol id="i-chat" viewBox="0 0 24 24"><path d="M4 5h16v11H9l-5 4z"/></symbol>
    </defs></svg>


    <nav>
        <a href="{{ url('/') }}" class="logo"><i></i> JobSphere</a>
        <div class="links">
            <a href="#jobs">Roles</a>
            <a href="#corners">Explore</a>
            <a href="#how">How it works</a>
            <a href="#trust">Trust &amp; safety</a>
        </div>
        <div class="nav-cta">
            <a href="{{ url('/login') }}" class="btn ghost">Log in</a>
            <a href="{{ url('/register') }}" class="btn primary">Get started</a>
        </div>
    </nav>

    <main class="wrap">
        <!-- HERO -->
        <div class="hero">
            <div>
                <div class="eyebrow">The career compass</div>
                <h1>Work that feels <em>like you.</em></h1>
                <p class="lead" id="lead">A more considered way to find your next chapter: thoughtfully matched roles, ambitious teams, and a path that makes sense.</p>

                <div class="switch" role="tablist">
                    <button class="on" data-mode="seeker"><svg class="ic"><use href="#i-compass"/></svg> I'm looking for work</button>
                    <button data-mode="hirer"><svg class="ic"><use href="#i-brief"/></svg> I'm hiring</button>
                </div>
                <div class="cta-row">
                    <a href="{{ url('/register') }}" class="btn primary" id="main-cta">Create my profile <svg class="ic"><use href="#i-arrow"/></svg></a>
                    <span class="cta-note" id="cta-note">Your CV is generated for you.</span>
                </div>

                <div class="stats">
                    <div><b>92%</b><span>average match accuracy</span></div>
                    <div><b>100%</b><span>verified recruiters</span></div>
                    <div><b>0</b><span>fake postings tolerated</span></div>
                </div>
            </div>

            <div class="visual">
                <div class="orb"></div>
                <div class="glass fbadge"><svg class="ic"><use href="#i-shield"/></svg> Posting checked · Real</div>                <div class="glass fcard one">
                    <div class="ftop">
                        <div class="avatar blush">N</div>
                        <div><h3>Product Designer</h3><small>Northstar · Remote</small></div>
                    </div>
                    <div class="fscore">
                        <div class="ring" style="--p:98"><b>98</b></div>
                        <div><strong>Beautifully matched</strong><small>Skills and interests align</small></div>
                    </div>
                </div>
                <div class="glass fcard two">
                    <div class="ftop">
                        <div class="avatar mint">W</div>
                        <div><h3>IoT Engineer</h3><small>Wave Systems</small></div>
                    </div>
                    <div class="fscore">
                        <div class="ring warm" style="--p:78"><b>78</b></div>
                        <div><strong>Good match</strong><small>Upskill: MQTT</small></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CURATED JOBS -->
        <section id="jobs">
            <div class="sec-head reveal">
                <div>
                    <div class="eyebrow">Curated for you</div>
                    <h2>Possibilities worth <em>exploring.</em></h2>
                </div>
                <a href="{{ url('/jobs') }}" class="pill-link">View all roles <svg class="ic"><use href="#i-arrow"/></svg></a>
            </div>

            <div class="jobs">
                <article class="job reveal">
                    <div class="job-top">
                        <div class="avatar blush">N</div>
                        <button class="like" aria-label="Save job"><svg class="ic"><use href="#i-heart"/></svg></button>
                    </div>
                    <div class="match"><svg class="ic spark"><use href="#i-spark"/></svg> 98% match</div>
                    <h3>Product Designer</h3>
                    <div class="company">Northstar</div>
                    <div class="meta">
                        <span><svg class="ic"><use href="#i-pin"/></svg> Remote · Asia</span>
                        <span><svg class="ic"><use href="#i-clock"/></svg> Full-time</span>
                    </div>
                    <div class="job-foot">
                        <div class="tags"><span>Figma</span><span>UX Research</span><span>Prototyping</span></div>
                        <a href="{{ url('/jobs') }}" class="apply">Apply <svg class="ic"><use href="#i-arrow"/></svg></a>
                    </div>
                </article>

                <article class="job reveal">
                    <div class="job-top">
                        <div class="avatar sage">R</div>
                        <button class="like" aria-label="Save job"><svg class="ic"><use href="#i-heart"/></svg></button>
                    </div>
                    <div class="match"><svg class="ic spark"><use href="#i-spark"/></svg> 94% match</div>
                    <h3>Senior Frontend Engineer</h3>
                    <div class="company">Rivet Labs</div>
                    <div class="meta">
                        <span><svg class="ic"><use href="#i-pin"/></svg> Remote · Hybrid</span>
                        <span><svg class="ic"><use href="#i-clock"/></svg> Full-time</span>
                    </div>
                    <div class="job-foot">
                        <div class="tags"><span>JavaScript</span><span>CSS</span><span>React</span></div>
                        <a href="{{ url('/jobs') }}" class="apply">Apply <svg class="ic"><use href="#i-arrow"/></svg></a>
                    </div>
                </article>

                <article class="job reveal">
                    <div class="job-top">
                        <div class="avatar sand">L</div>
                        <button class="like" aria-label="Save job"><svg class="ic"><use href="#i-heart"/></svg></button>
                    </div>
                    <div class="match"><svg class="ic spark"><use href="#i-spark"/></svg> 91% match</div>
                    <h3>Junior Laravel Developer</h3>
                    <div class="company">Lumen Works</div>
                    <div class="meta">
                        <span><svg class="ic"><use href="#i-pin"/></svg> Hybrid</span>
                        <span><svg class="ic"><use href="#i-clock"/></svg> Full-time</span>
                    </div>
                    <div class="job-foot">
                        <div class="tags"><span>Laravel</span><span>MySQL</span><span>REST</span></div>
                        <a href="{{ url('/jobs') }}" class="apply">Apply <svg class="ic"><use href="#i-arrow"/></svg></a>
                    </div>
                </article>

                <article class="job reveal">
                    <div class="job-top">
                        <div class="avatar mint">W</div>
                        <button class="like" aria-label="Save job"><svg class="ic"><use href="#i-heart"/></svg></button>
                    </div>
                    <div class="match"><svg class="ic spark"><use href="#i-spark"/></svg> 87% match</div>
                    <h3>IoT Engineer</h3>
                    <div class="company">Wave Systems</div>
                    <div class="meta">
                        <span><svg class="ic"><use href="#i-pin"/></svg> Remote</span>
                        <span><svg class="ic"><use href="#i-clock"/></svg> Contract</span>
                    </div>
                    <div class="job-foot">
                        <div class="tags"><span>ESP32</span><span>MQTT</span><span>C++</span></div>
                        <a href="{{ url('/jobs') }}" class="apply">Apply <svg class="ic"><use href="#i-arrow"/></svg></a>
                    </div>
                </article>
            </div>
        </section>

        <!-- CATEGORIES -->
        <section id="corners">
            <div class="sec-head reveal">
                <div>
                    <div class="eyebrow">Follow your curiosity</div>
                    <h2>Find your <em>corner.</em></h2>
                </div>
                <p>From first spark to next big thing, explore the spaces where thoughtful people are making an impact.</p>
            </div>
            <div class="cats">
                <a href="{{ url('/jobs') }}?c=design" class="cat mint reveal"><div><small>01</small><h3>Design &amp; Creative</h3></div><svg class="ic arrow"><use href="#i-arrow"/></svg></a>
                <a href="{{ url('/jobs') }}?c=tech" class="cat sand reveal"><div><small>02</small><h3>Technology</h3></div><svg class="ic arrow"><use href="#i-arrow"/></svg></a>
                <a href="{{ url('/jobs') }}?c=climate" class="cat sage reveal"><div><small>03</small><h3>Climate &amp; Impact</h3></div><svg class="ic arrow"><use href="#i-arrow"/></svg></a>
                <a href="{{ url('/jobs') }}?c=business" class="cat blush reveal"><div><small>04</small><h3>Business &amp; Strategy</h3></div><svg class="ic arrow"><use href="#i-arrow"/></svg></a>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section id="how">
            <div class="sec-head reveal">
                <div>
                    <div class="eyebrow">How it works</div>
                    <h2>Two sides, <em>one flow.</em></h2>
                </div>
            </div>
            <div class="split">
                <div class="panel cand reveal">
                    <h3>For candidates</h3>
                    <ol class="steps">
                        <li>Fill in your profile form once.</li>
                        <li>Get your CV and public profile instantly.</li>
                        <li>Browse roles ranked by match score.</li>
                        <li>Apply and track your progress.</li>
                        <li>See interview calls right on your profile.</li>
                    </ol>
                </div>
                <div class="panel rec reveal">
                    <h3>For recruiters</h3>
                    <ol class="steps">
                        <li>Verify your company via API.</li>
                        <li>Post a job in minutes.</li>
                        <li>View applicants with compatibility scores.</li>
                        <li>Call top candidates for interviews.</li>
                        <li>Get notified when profiles update.</li>
                    </ol>
                </div>
            </div>
        </section>

        <!-- TRUST -->
        <section id="trust">
            <div class="band reveal">
                <div style="position:relative;z-index:1">
                    <div class="eyebrow" style="color:#fff"><span style="opacity:.9">Trust &amp; safety</span></div>
                    <h2 style="margin-top:20px">Real jobs <em>only.</em></h2>
                    <p>Every posting passes through our fake-job detector, so your time goes to real opportunities and you stay safe from scams.</p>
                </div>
                <div class="verdicts">
                    <div class="verdict">Senior Designer · Atlas Co. <span class="ok">✔ Real</span></div>
                    <div class="verdict">"Earn $5000/day from home" <span class="bad">⚠ Suspicious</span></div>
                    <div class="verdict">Data Analyst · Orbit Inc. <span class="ok">✔ Real</span></div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section>
            <div class="glass cta reveal">
                <h2>Ready to meet your <em>best match?</em></h2>
                <p>Join in a minute. Your CV, profile and first recommendations are on us.</p>
                <div class="cta-row">
                    <a href="{{ url('/register') }}" class="btn primary">Create free account <svg class="ic"><use href="#i-arrow"/></svg></a>
                    <a href="{{ route('recruiter.register') }}" class="btn coral">Post a job</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="wrap foot">
            <span>© <span id="year"></span> JobSphere. All rights reserved.</span>
            <span>Privacy · Terms · Contact</span>
        </div>
    </footer>

    <!-- CHATBOT -->
    <button id="chat-toggle" aria-label="Open chat"><svg class="ic"><use href="#i-chat"/></svg></button>
    <div id="chat-box" class="glass">
        <div class="chat-head"><strong>JobSphere Assistant</strong><small>Ask about applying, CVs, scores...</small></div>
        <div id="chat-log"><div class="msg bot">Hi! 👋 How can I help you today?</div></div>
        <form id="chat-form">
            <input id="chat-input" placeholder="Type a question..." autocomplete="off" maxlength="300">
            <button type="submit">Send</button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
@verbatim
        document.getElementById('year').textContent = new Date().getFullYear();

        // seeker / hirer switch
        const modes = {
            seeker: { lead: 'A more considered way to find your next chapter: thoughtfully matched roles, ambitious teams, and a path that makes sense.',
                      cta: 'Create my profile', href: '/register', note: 'Your CV is generated for you.' },
            hirer:  { lead: 'Meet candidates who genuinely fit. Verified company, ranked applicants, and no fake-posting noise.',
                      cta: 'Post a job', href: '/recruiter/register', note: 'Verified recruiters only.' }
        };
        document.querySelectorAll('.switch button').forEach(btn => btn.addEventListener('click', () => {
            document.querySelectorAll('.switch button').forEach(b => b.classList.remove('on'));
            btn.classList.add('on');
            const m = modes[btn.dataset.mode];
            document.getElementById('lead').textContent = m.lead;
            document.getElementById('cta-note').textContent = m.note;
            const cta = document.getElementById('main-cta');
            cta.href = m.href;
            cta.firstChild.textContent = m.cta + ' ';
        }));

        // save (heart) toggle
        document.querySelectorAll('.like').forEach(b => b.addEventListener('click', () => b.classList.toggle('on')));

        // scroll reveal
        const io = new IntersectionObserver((es) => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .12 });
        document.querySelectorAll('.reveal').forEach(el => io.observe(el));

        // chatbot
        const box = document.getElementById('chat-box');
        const log = document.getElementById('chat-log');
        const input = document.getElementById('chat-input');
        document.getElementById('chat-toggle').onclick = () => box.classList.toggle('open');

        // Local FAQ (used when the backend /chat endpoint isn't available)
        const FAQ = [
            [/apply|application/, 'Open a job and click Apply. Track it under My Applications.'],
            [/cv|resume/, 'Your CV is auto-generated from your profile form. Edit the form to update it.'],
            [/score|compatib|match/, 'The compatibility score compares your skills and interests with the job requirements.'],
            [/interview|call/, 'Interview calls from recruiters appear on your profile.'],
            [/verify|verification/, 'Recruiters are verified via their company API before posting.'],
            [/fake|scam/, 'Every posting passes our fake-job detector. Report anything suspicious.']
        ];
        function localReply(msg) {
            msg = msg.toLowerCase();
            for (const [re, ans] of FAQ) if (re.test(msg)) return ans;
            return "Sorry, I didn't get that. Try asking about applying, your CV, scores, or interviews.";
        }
        function add(text, who) {
            const d = document.createElement('div');
            d.className = 'msg ' + who;
            d.textContent = text;          // textContent prevents XSS
            log.appendChild(d);
            log.scrollTop = log.scrollHeight;
        }
        document.getElementById('chat-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;
            add(message, 'me');
            input.value = '';
            try {
                const res = await fetch('/chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ message })
                });
                if (!res.ok) throw new Error('no backend');
                const data = await res.json();
                add(data.reply || localReply(message), 'bot');
            } catch (err) {
                add(localReply(message), 'bot');
            }
        });
    
@endverbatim
</script>
@endpush
