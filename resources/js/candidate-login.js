 (() => {
            const DEMO = false;                 // true = preview without a backend
            const ENDPOINT = document.getElementById('login').dataset.action;
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;

            const form = document.getElementById('login');
            const banner = document.getElementById('banner');
            const submitBtn = document.getElementById('submit');
            const el = n => form.elements[n];
            let leaving = false;

            // Run blur validation only after a click finishes, so a shifting error message
            // doesn't make the click land on the wrong spot.
            let pressed = false;
            const queue = [];
            const flush = () => { while (queue.length) queue.shift()(); };
            const release = () => { pressed = false; setTimeout(flush, 0); };
            document.addEventListener('pointerdown', () => { pressed = true; }, true);
            document.addEventListener('pointerup', release, true);
            document.addEventListener('pointercancel', release, true);
            const later = fn => pressed ? queue.push(fn) : setTimeout(fn, 120);

            document.getElementById('year').textContent = new Date().getFullYear();

            const RULES = {
                email: v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'Enter a valid email address.',
                password: v => v ? '' : 'Enter your password.'
            };

            function setError(name, msg) {
                const box = el(name).closest('.f');
                box.classList.toggle('err', !!msg);
                box.querySelector('.msg-err').textContent = msg || '';
                el(name).setAttribute('aria-invalid', msg ? 'true' : 'false');
            }
            function check(name) {
                const msg = RULES[name](el(name).value);
                setError(name, msg);
                return !msg;
            }
            Object.keys(RULES).forEach(n => {
                const f = el(n);
                f.addEventListener('blur', () => later(() => { if (f.value !== '') check(n); }));
                f.addEventListener('input', () => { if (f.closest('.f').classList.contains('err')) check(n); });
            });

            // show / hide password
            const eye = document.getElementById('eye');
            eye.addEventListener('click', () => {
                const show = el('password').type === 'password';
                el('password').type = show ? 'text' : 'password';
                eye.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                eye.querySelector('use').setAttribute('href', show ? '#i-eye-off' : '#i-eye');
            });

            function showBanner(msg, ok) {
                banner.textContent = msg;
                banner.className = 'banner' + (ok ? ' ok' : '');
                banner.hidden = false;
            }

            form.addEventListener('submit', async e => {
                e.preventDefault();
                banner.hidden = true;
                const results = Object.keys(RULES).map(check);          // check every field so all errors show
                if (results.includes(false)) { el(Object.keys(RULES)[results.indexOf(false)]).focus(); return; }

                const body = { email: el('email').value.trim(), password: el('password').value, remember: el('remember').checked };
                const label = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Logging in…';
                try {
                    if (DEMO) {
                        await new Promise(r => setTimeout(r, 600));
                        showBanner('Preview mode: you would be taken to your dashboard now.', true);
                        return;
                    }
                    const res = await fetch(ENDPOINT, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify(body)
                    });
                    const data = await res.json().catch(() => ({}));

                    if (res.status === 422) {                                  // Laravel validation errors
                        const errs = data.errors || {};
                        const names = Object.keys(errs).filter(n => el(n) && RULES[n]);
                        names.forEach(n => setError(n, errs[n][0]));
                        if (names.length) el(names[0]).focus();
                        else showBanner(data.message || 'Please check your details and try again.');
                        return;
                    }
                    if (res.status === 429) throw new Error('Too many attempts. Please wait a minute and try again.');
                    if (!res.ok) throw new Error(data.message || 'Something went wrong (' + res.status + '). Please try again.');

                    leaving = true;
                    window.location.href = data.redirect || '/';
                } catch (err) {
                    showBanner(err instanceof TypeError ? 'Could not reach the server. Please try again.' : (err.message || 'Something went wrong. Please try again.'));
                } finally {
                    if (!leaving) { submitBtn.disabled = false; submitBtn.innerHTML = label; }
                }
            });
        })();