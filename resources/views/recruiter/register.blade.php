@extends('layouts.base')

@section('title', 'Recruiter sign up — JobSphere')

@push('styles')
    <link rel="stylesheet" href="{{ asset('style/register.css') }}">
@endpush

@section('content')
    <!-- icon sprite -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
        <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>
        <symbol id="i-eye" viewBox="0 0 24 24"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></symbol>
        <symbol id="i-eye-off" viewBox="0 0 24 24"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.5 6.6A17 17 0 0 0 2 12s3.6 7 10 7a10 10 0 0 0 4.4-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></symbol>
        <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M7 17 17 7M8 7h9v9"/></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 4.5-3.4 7.7-8 9-4.6-1.3-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/></symbol>
    </defs></svg>

    <nav>
        <a href="{{ url('/') }}" class="logo"><i></i> JobSphere</a>
        <div class="links">
            <a href="{{ url('/') }}#jobs">Roles</a>
            <a href="{{ url('/') }}#how">How it works</a>
            <a href="{{ url('/') }}#trust">Trust &amp; safety</a>
        </div>
        <div class="nav-cta">
            <a href="{{ route('recruiter.login') }}" class="btn ghost">Log in</a>
            <a href="{{ url('/register') }}" class="btn primary">Candidate sign up</a>
        </div>
    </nav>

    <main class="wrap">
        <div class="reg">
            <!-- left panel -->
            <aside class="aside">
                <div class="eyebrow">For recruiters</div>
                <h1>Hire people who <em>truly fit.</em></h1>
                <p class="lead">Create your recruiter account in a few minutes. Every company is verified, so candidates trust your postings.</p>

                <ul class="perks">
                    <li><svg class="ic"><use href="#i-check"/></svg> Verified company badge on every job you post</li>
                    <li><svg class="ic"><use href="#i-check"/></svg> Applicants ranked by compatibility score</li>
                    <li><svg class="ic"><use href="#i-check"/></svg> Call top candidates for interview from their profile</li>
                    <li><svg class="ic"><use href="#i-check"/></svg> Instant alerts when a candidate updates their profile</li>
                </ul>

                <div class="how">
                    <h3>How verification works</h3>
                    <ol>
                        <li>You submit your company details</li>
                        <li>We check them against the company registry API</li>
                        <li>You get a verified badge and can post jobs</li>
                    </ol>
                </div>

                <div class="vcard" aria-label="Preview of your verified company profile">
                    <span class="vlabel">Your verified profile will look like this</span>
                    <div class="vhead">
                        <div class="avatar mint" id="vAvatar">Y</div>
                        <div class="vname">
                            <strong><span id="vName">Your Company</span>
                                <span class="vbadge"><svg class="ic"><use href="#i-shield"/></svg> Verified</span></strong>
                            <small id="vSub">Industry · City, Country</small>
                        </div>
                    </div>
                    <div class="vstats">
                        <div><b>3</b><span>Open roles</span></div>
                        <div><b>128</b><span>Applicants</span></div>
                        <div><b>12</b><span>Interviews</span></div>
                    </div>
                    <div class="vsite" id="vSite">yourcompany.com</div>
                </div>
            </aside>

            <!-- form card -->
            <div class="glass form-card" id="card">
                <div id="formView">
                    <h2>Create your recruiter account</h2>
                    <p class="sub">Already registered? <a href="{{ route('recruiter.login') }}">Log in</a></p>

                    <div class="stepper" aria-label="Progress">
                        <div class="step active"><b>1</b><span>You</span></div>
                        <div class="bar"><i></i></div>
                        <div class="step"><b>2</b><span>Company</span></div>
                        <div class="bar"><i></i></div>
                        <div class="step"><b>3</b><span>Review</span></div>
                    </div>

                    <form id="reg" novalidate>
                        <!-- step 1: you -->
                        <div class="panel-step show" data-step="0">
                            <div class="row2">
                                <div class="f"><label for="name">Full name</label>
                                    <input id="name" name="name" autocomplete="name" placeholder="e.g. Amina Rahman">
                                    <span class="msg-err"></span></div>
                                <div class="f"><label for="job_title">Job title</label>
                                    <input id="job_title" name="job_title" autocomplete="organization-title" placeholder="e.g. HR Manager">
                                    <span class="msg-err"></span></div>
                            </div>
                            <div class="row2">
                                <div class="f"><label for="email">Work email</label>
                                    <input id="email" name="email" type="email" autocomplete="email" placeholder="you@company.com">
                                    <span class="msg-err"></span></div>
                                <div class="f"><label for="phone">Phone <span class="hint">(optional)</span></label>
                                    <input id="phone" name="phone" type="tel" autocomplete="tel" placeholder="+880 1XXX XXXXXX">
                                    <span class="msg-err"></span></div>
                            </div>
                            <div class="row2">
                                <div class="f"><label for="password">Password</label>
                                    <div class="pw">
                                        <input id="password" name="password" type="password" autocomplete="new-password" placeholder="At least 8 characters">
                                        <button type="button" class="eye" data-for="password" aria-label="Show password"><svg class="ic"><use href="#i-eye"/></svg></button>
                                    </div>
                                    <div class="meter" id="meter" data-s="0"><i></i><i></i><i></i><i></i></div>
                                    <span class="hint" id="meterText">Use letters, numbers and a symbol.</span>
                                    <span class="msg-err"></span></div>
                                <div class="f"><label for="password_confirmation">Confirm password</label>
                                    <div class="pw">
                                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Repeat password">
                                        <button type="button" class="eye" data-for="password_confirmation" aria-label="Show password"><svg class="ic"><use href="#i-eye"/></svg></button>
                                    </div>
                                    <span class="msg-err"></span></div>
                            </div>
                        </div>

                        <!-- step 2: company -->
                        <div class="panel-step" data-step="1">
                            <div class="row2">
                                <div class="f"><label for="company_name">Company name</label>
                                    <input id="company_name" name="company_name" autocomplete="organization" placeholder="e.g. Acme Labs Ltd.">
                                    <span class="msg-err"></span></div>
                                <div class="f"><label for="registration_no">Registration / trade licence no.</label>
                                    <input id="registration_no" name="registration_no" placeholder="e.g. TL-2024-1187">
                                    <span class="msg-err"></span></div>
                            </div>
                            <div class="f"><label for="website">Official website</label>
                                <input id="website" name="website" type="url" inputmode="url" autocomplete="url" placeholder="https://yourcompany.com">
                                <span class="hint">Used to verify your company with its registry.</span>
                                <span class="msg-err"></span></div>
                            <div class="row2">
                                <div class="f"><label for="industry">Industry</label>
                                    <select id="industry" name="industry">
                                        <option value="">Select industry</option>
                                        <option>Technology</option><option>Design &amp; Creative</option><option>Finance</option>
                                        <option>Healthcare</option><option>Education</option><option>Climate &amp; Impact</option>
                                        <option>Manufacturing</option><option>Retail &amp; E-commerce</option>
                                        <option>Business &amp; Strategy</option><option>Other</option>
                                    </select>
                                    <span class="msg-err"></span></div>
                                <div class="f"><label for="hq_location">Headquarters</label>
                                    <input id="hq_location" name="hq_location" placeholder="City, Country">
                                    <span class="msg-err"></span></div>
                            </div>
                        </div>

                        <!-- step 3: review -->
                        <div class="panel-step" data-step="2">
                            <dl class="review" id="review"></dl>
                            <p class="note" id="domainNote" hidden>Heads up: your email domain differs from your company website, so verification may take a little longer.</p>
                            <div class="f"><label class="cb"><input type="checkbox" name="confirm_auth"> <span>I confirm I'm authorised to hire on behalf of this company.</span></label>
                                <span class="msg-err"></span></div>
                            <div class="f"><label class="cb"><input type="checkbox" name="agree"> <span>I agree to the <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.</span></label>
                                <span class="msg-err"></span></div>
                            <div class="banner" id="banner" role="alert" hidden></div>
                        </div>

                        <div class="actions">
                            <button type="button" class="btn ghost" id="back" hidden>Back</button>
                            <button type="button" class="btn primary" id="next">Continue <svg class="ic"><use href="#i-arrow"/></svg></button>
                            <button type="submit" class="btn primary" id="submit" hidden>Create account</button>
                        </div>
                    </form>
                </div>

                <!-- success -->
                <div class="done-view" id="doneView" hidden>
                    <div class="tick"><svg class="ic"><use href="#i-check"/></svg></div>
                    <h2>You're all set!</h2>
                    <p class="sub" id="doneText"></p>
                    <span class="status-pill pending" id="statusPill"><svg class="ic"><use href="#i-shield"/></svg> <span id="statusText"></span></span>
                    <p class="note" id="demoNote" hidden>Preview mode: nothing was saved.</p>
                    <div class="cta-row">
                        <a class="btn primary" href="{{ route('recruiter.login') }}">Go to login</a>
                        <a class="btn ghost" href="{{ url('/') }}">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="slim">
        <div class="wrap foot">
            <span>© <span id="year"></span> JobSphere. All rights reserved.</span>
            <span>Privacy · Terms · Contact</span>
        </div>
    </footer>
@endsection

@push('scripts')
<script>
@verbatim
    (() => {
        const DEMO = false;                       // true = show the success screen without a backend (preview only)
        const ENDPOINT = '/recruiter/register';   // Laravel route
        const FREE = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 'proton.me', 'protonmail.com', 'live.com', 'aol.com'];
        const FIELDS = [
            ['name', 'job_title', 'email', 'phone', 'password', 'password_confirmation'],
            ['company_name', 'registration_no', 'website', 'industry', 'hq_location'],
            ['confirm_auth', 'agree']
        ];

        const form = document.getElementById('reg');
        const card = document.getElementById('card');
        const panels = [...form.querySelectorAll('.panel-step')];
        const stepEls = [...document.querySelectorAll('.stepper .step')];
        const bars = [...document.querySelectorAll('.stepper .bar')];
        const backBtn = document.getElementById('back');
        const nextBtn = document.getElementById('next');
        const submitBtn = document.getElementById('submit');
        const banner = document.getElementById('banner');
        let cur = 0;

        document.getElementById('year').textContent = new Date().getFullYear();

        const el = n => form.elements[n];
        const domainOf = v => (v.split('@')[1] || '').trim().toLowerCase();
        const hostOf = v => {
            v = v.trim();
            if (!v) return '';
            try {
                const u = new URL(/^https?:\/\//i.test(v) ? v : 'https://' + v);
                return /^[a-z0-9-]+(\.[a-z0-9-]+)+$/i.test(u.hostname) ? u.hostname.replace(/^www\./, '').toLowerCase() : '';
            } catch { return ''; }
        };

        // validation rules (return an error message, or '' when valid)
        const RULES = {
            name: v => v.trim().length < 2 ? 'Please enter your full name.' : '',
            job_title: v => v.trim().length < 2 ? 'Enter your job title.' : '',
            email: v => {
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim())) return 'Enter a valid email address.';
                if (FREE.includes(domainOf(v))) return 'Please use your company email, not a personal one.';
                return '';
            },
            phone: v => v.trim() && !/^\+?[0-9\s\-()]{7,18}$/.test(v.trim()) ? 'Enter a valid phone number.' : '',
            password: v => v.length < 8 ? 'Use at least 8 characters.' : (!/[A-Za-z]/.test(v) || !/\d/.test(v)) ? 'Include at least one letter and one number.' : '',
            password_confirmation: v => !v ? 'Please confirm your password.' : v !== el('password').value ? 'Passwords do not match.' : '',
            company_name: v => v.trim().length < 2 ? 'Enter your company name.' : '',
            registration_no: v => /^[A-Za-z0-9\-\/ ]{4,30}$/.test(v.trim()) ? '' : 'Enter your registration or trade licence number (4–30 characters).',
            website: v => hostOf(v) ? '' : 'Enter a valid website, e.g. https://yourcompany.com',
            industry: v => v ? '' : 'Select an industry.',
            hq_location: v => v.trim().length < 2 ? 'Enter the city and country.' : '',
            confirm_auth: c => c ? '' : 'Please confirm this to continue.',
            agree: c => c ? '' : 'You need to accept the terms to continue.'
        };

        function setError(name, msg) {
            const f = el(name);
            const box = f.closest('.f');
            box.classList.toggle('err', !!msg);
            box.querySelector('.msg-err').textContent = msg || '';
            f.setAttribute('aria-invalid', msg ? 'true' : 'false');
        }
        function check(name) {
            const f = el(name);
            const msg = RULES[name](f.type === 'checkbox' ? f.checked : f.value);
            setError(name, msg);
            return !msg;
        }
        function validateStep(i) {
            let first = null;
            FIELDS[i].forEach(n => { if (!check(n) && !first) first = n; });
            if (first) el(first).focus();
            return !first;
        }

        // Run blur validation only after a click finishes. Otherwise an error message appearing or
        // disappearing shifts the layout mid-click and the click lands on the wrong spot.
        let pressed = false;
        const queue = [];
        const flush = () => { while (queue.length) queue.shift()(); };
        const release = () => { pressed = false; setTimeout(flush, 0); };
        document.addEventListener('pointerdown', () => { pressed = true; }, true);
        document.addEventListener('pointerup', release, true);
        document.addEventListener('pointercancel', release, true);
        const later = fn => pressed ? queue.push(fn) : setTimeout(fn, 120);

        // live validation
        Object.keys(RULES).forEach(n => {
            const f = el(n);
            const evt = (f.type === 'checkbox' || f.tagName === 'SELECT') ? 'change' : 'input';
            f.addEventListener('blur', () => later(() => { if (f.type !== 'checkbox' && f.value !== '') check(n); }));
            f.addEventListener(evt, () => { if (f.closest('.f').classList.contains('err')) check(n); });
        });

        // password strength
        const meter = document.getElementById('meter');
        const meterText = document.getElementById('meterText');
        el('password').addEventListener('input', e => {
            const v = e.target.value;
            let s = 0;
            if (v.length >= 8) s++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
            if (/\d/.test(v)) s++;
            if (/[^A-Za-z0-9]/.test(v) || v.length >= 12) s++;
            if (v) s = Math.max(1, s);
            meter.dataset.s = s;
            meterText.textContent = v ? ['', 'Weak', 'Fair', 'Good', 'Strong'][s] : 'Use letters, numbers and a symbol.';
            if (el('password_confirmation').value) check('password_confirmation');
        });

        // show / hide password
        document.querySelectorAll('.eye').forEach(b => b.addEventListener('click', () => {
            const i = el(b.dataset.for);
            const show = i.type === 'password';
            i.type = show ? 'text' : 'password';
            b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            b.querySelector('use').setAttribute('href', show ? '#i-eye-off' : '#i-eye');
        }));

        // live "verified profile" preview in the left panel
        const vAvatar = document.getElementById('vAvatar'), vName = document.getElementById('vName'),
              vSub = document.getElementById('vSub'), vSite = document.getElementById('vSite');
        function updateCard() {
            const name = el('company_name').value.trim() || 'Your Company';
            vAvatar.textContent = name.charAt(0).toUpperCase();
            vName.textContent = name;
            vSub.textContent = [el('industry').value, el('hq_location').value.trim()].filter(Boolean).join(' · ') || 'Industry · City, Country';
            vSite.textContent = hostOf(el('website').value) || 'yourcompany.com';
        }
        ['company_name', 'industry', 'hq_location', 'website'].forEach(n => {
            el(n).addEventListener('input', updateCard);
            el(n).addEventListener('change', updateCard);
        });

        // steps
        function go(i) {
            cur = i;
            panels.forEach((p, j) => p.classList.toggle('show', j === i));
            stepEls.forEach((s, j) => {
                s.classList.toggle('active', j === i);
                s.classList.toggle('done', j < i);
                s.querySelector('b').innerHTML = j < i ? '<svg class="ic"><use href="#i-check"/></svg>' : String(j + 1);
            });
            bars.forEach((b, j) => b.classList.toggle('full', j < i));
            backBtn.hidden = i === 0;
            nextBtn.hidden = i === 2;
            submitBtn.hidden = i !== 2;
            banner.hidden = true;
            if (i === 2) fillReview();
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function fillReview() {
            const rows = [['Name', 'name'], ['Job title', 'job_title'], ['Work email', 'email'], ['Phone', 'phone'],
                ['Company', 'company_name'], ['Registration no.', 'registration_no'], ['Website', 'website'],
                ['Industry', 'industry'], ['Headquarters', 'hq_location']];
            const dl = document.getElementById('review');
            dl.replaceChildren();
            rows.forEach(([label, n]) => {
                const d = document.createElement('div');
                const dt = document.createElement('dt'); dt.textContent = label;
                const dd = document.createElement('dd'); dd.textContent = el(n).value.trim() || '—';
                d.append(dt, dd);
                dl.append(d);
            });
            const a = domainOf(el('email').value), b = hostOf(el('website').value);
            const related = a && b && (a === b || a.endsWith('.' + b) || b.endsWith('.' + a));
            document.getElementById('domainNote').hidden = !!related;
        }

        nextBtn.addEventListener('click', () => { if (validateStep(cur)) go(cur + 1); });
        backBtn.addEventListener('click', () => go(cur - 1));

        function showDone(status, demo) {
            document.getElementById('formView').hidden = true;
            document.getElementById('doneView').hidden = false;
            const verified = status === 'verified';
            document.getElementById('statusPill').className = 'status-pill' + (verified ? '' : ' pending');
            document.getElementById('statusText').textContent = verified ? 'Company verified' : 'Verification pending';
            document.getElementById('doneText').textContent = verified
                ? 'Your company is verified. You can start posting jobs right away.'
                : "We're checking your company details against the official registry. You'll be notified by email once it's approved.";
            document.getElementById('demoNote').hidden = !demo;
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function showBanner(msg) { banner.textContent = msg; banner.hidden = false; }

        form.addEventListener('submit', async e => {
            e.preventDefault();
            if (cur < 2) { nextBtn.click(); return; }
            if (!validateStep(2)) return;

            const body = { agree: true, password: el('password').value, password_confirmation: el('password_confirmation').value };
            ['name', 'job_title', 'email', 'phone', 'company_name', 'registration_no', 'website', 'industry', 'hq_location']
                .forEach(n => { body[n] = el(n).value.trim(); });

            const label = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating account…';
            banner.hidden = true;
            try {
                if (DEMO) { await new Promise(r => setTimeout(r, 600)); showDone('pending', true); return; }

                const res = await fetch(ENDPOINT, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(body)
                });
                const data = await res.json().catch(() => ({}));

                if (res.status === 422) {                       // Laravel validation errors
                    const errs = data.errors || {};
                    const names = Object.keys(errs).filter(n => el(n));
                    names.forEach(n => setError(n, errs[n][0]));
                    if (names.length) {
                        const step = FIELDS.findIndex(list => names.some(n => list.includes(n)));
                        go(step);
                        el(names.find(n => FIELDS[step].includes(n))).focus();
                    } else {
                        showBanner(data.message || 'Please check your details and try again.');
                    }
                    return;
                }
                if (!res.ok) throw new Error(data.message || 'Something went wrong (' + res.status + '). Please try again.');
                showDone(data.status || 'pending', false);
            } catch (err) {
                showBanner(err instanceof TypeError ? 'Could not reach the server. Please try again.' : (err.message || 'Something went wrong. Please try again.'));
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = label;
            }
        });
    })();
    
@endverbatim
</script>
@endpush
