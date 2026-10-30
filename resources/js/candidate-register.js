 (() => {
            const DEMO = false;                 // true = show success screen without a backend
            const ENDPOINT = document.getElementById('reg').dataset.action;
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const FIELDS = [
                ['name', 'email', 'phone', 'password', 'password_confirmation'],
                ['category', 'experience', 'degree', 'grad_year', 'institution', 'major', 'cgpa'],
                ['agree']
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

            const RULES = {
                name: v => v.trim().length < 2 ? 'Please enter your full name.' : '',
                email: v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'Enter a valid email address.',
                phone: v => /^[0-9\s\-]{9,12}$/.test(v.trim()) ? '' : 'Enter a valid phone number.',
                password: v => v.length < 8 ? 'Use at least 8 characters.' : (!/[A-Za-z]/.test(v) || !/\d/.test(v)) ? 'Include at least one letter and one number.' : '',
                password_confirmation: v => !v ? 'Please confirm your password.' : v !== el('password').value ? 'Passwords do not match.' : '',
                category: v => v ? '' : 'Select a job category.',
                experience: v => v ? '' : 'Select your experience.',
                degree: v => v ? '' : 'Select your degree.',
                grad_year: v => {
                    const y = Number(v);
                    return /^\d{4}$/.test(v.trim()) && y >= 1970 && y <= new Date().getFullYear() + 8 ? '' : 'Enter a valid year, e.g. 2026.';
                },
                institution: v => v.trim().length < 2 ? 'Enter your institution name.' : '',
                major: v => v.trim().length < 2 ? 'Enter your department or major.' : '',
                cgpa: v => !v.trim() || (Number(v) >= 0 && Number(v) <= 5) ? '' : 'Enter a valid CGPA (0 to 5).',
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

            // live validation
            Object.keys(RULES).forEach(n => {
                const f = el(n);
                const evt = (f.type === 'checkbox' || f.tagName === 'SELECT') ? 'change' : 'input';
                f.addEventListener('blur', () => { if (f.type !== 'checkbox' && f.value !== '') check(n); });
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

            // live profile preview
            const vAvatar = document.getElementById('vAvatar'), vName = document.getElementById('vName'),
                vSub = document.getElementById('vSub'), vSite = document.getElementById('vSite');
            function updateCard() {
                const name = el('name').value.trim() || 'Your Name';
                vAvatar.textContent = name.charAt(0).toUpperCase();
                vName.textContent = name;
                vSub.textContent = [el('category').value, el('experience').value].filter(Boolean).join(' · ') || 'Job category · Experience';
                vSite.textContent = el('email').value.trim() || 'you@email.com';
            }
            ['name', 'email', 'category', 'experience'].forEach(n => {
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
                const rows = [['Name', 'name'], ['Email', 'email'], ['Phone', 'phone'], ['Job category', 'category'], ['Experience', 'experience'],
                ['Degree', 'degree'], ['Institution', 'institution'], ['Department', 'major'], ['Passing year', 'grad_year'], ['CGPA', 'cgpa']];
                const dl = document.getElementById('review');
                dl.replaceChildren();
                rows.forEach(([label, n]) => {
                    const d = document.createElement('div');
                    const dt = document.createElement('dt'); dt.textContent = label;
                    const dd = document.createElement('dd');
                    dd.textContent = (n === 'phone' ? '+880 ' : '') + (el(n).value.trim() || '—');
                    d.append(dt, dd);
                    dl.append(d);
                });
            }

            nextBtn.addEventListener('click', () => { if (validateStep(cur)) go(cur + 1); });
            backBtn.addEventListener('click', () => go(cur - 1));

            function showDone(demo) {
                document.getElementById('formView').hidden = true;
                document.getElementById('doneView').hidden = false;
                
                card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            function showBanner(msg) { banner.textContent = msg; banner.hidden = false; }

            form.addEventListener('submit', async e => {
                e.preventDefault();
                if (cur < 2) { nextBtn.click(); return; }
                if (!validateStep(2)) return;

                const body = { agree: true, password: el('password').value, password_confirmation: el('password_confirmation').value };
                ['name', 'email', 'phone', 'category', 'experience', 'degree', 'grad_year', 'institution', 'major', 'cgpa']
                    .forEach(n => { body[n] = el(n).value.trim(); });
                body.phone = '+880' + body.phone.replace(/\D/g, '').replace(/^0/, '');

                const label = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating account…';
                banner.hidden = true;
                try {
                    if (DEMO) { await new Promise(r => setTimeout(r, 600)); showDone(true); return; }

                    const res = await fetch(ENDPOINT, {
                        method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },                        body: JSON.stringify(body)
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
                    showDone(false);
                } catch (err) {
                    showBanner(err.message || 'Could not reach the server. Please try again.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = label;
                }
            });
        })();