@extends('layouts.recruiter')

@section('title', 'My jobs — JobSphere')

@section('main')
<div class="page-head">
    <div><div class="eyebrow">Recruiter workspace</div><h1 style="margin-top:10px">My jobs</h1><p id="sub">Every posting is checked before it goes live.</p></div>
    <div class="acts">
        <div class="tabs" id="tabs"></div>
        <button class="btn primary" id="newBtn" type="button"><svg class="ic"><use href="#i-plus"/></svg> Post a job</button>
    </div>
</div>

<!-- composer -->
<section class="glass card" id="composer" hidden>
    <div class="card-head"><div><h3>New job posting</h3><p class="sub">We run an authenticity check before it can go live.</p></div>
        <button class="btn ghost sm" id="closeComposer" type="button">Cancel</button></div>
    <form id="jobForm" class="jform" novalidate>
        <div class="f"><label for="title">Job title</label><input id="title" placeholder="e.g. Backend Engineer"><span class="msg-err"></span></div>
        <div class="f"><label for="loc">Location</label><input id="loc" placeholder="e.g. Khulna or Remote"><span class="msg-err"></span></div>
        <div class="f"><label for="type">Type</label><select class="sel" id="type" style="width:100%"><option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option></select></div>
        <div class="f"><label for="smin">Salary range (BDT / month)</label>
            <div class="two"><input id="smin" type="number" min="0" placeholder="Min"><input id="smax" type="number" min="0" placeholder="Max"></div><span class="msg-err"></span></div>
        <div class="f wide"><label for="desc">Description</label><textarea id="desc" rows="5" placeholder="What will this person do? What skills do they need?"></textarea><span class="msg-err"></span></div>
        <div class="f wide"><label for="skills">Required skills <small style="color:var(--muted)">(comma separated)</small></label><input id="skills" placeholder="Laravel, MySQL, REST APIs"></div>
        <div class="wide" id="verdict" hidden></div>
        <div class="wide"><button class="btn primary" type="submit">Run authenticity check <svg class="ic"><use href="#i-shield"/></svg></button></div>
    </form>
</section>

<section class="jobs-grid" id="grid"></section>
@endsection

@section('modals')
@endsection

@push('scripts')
<script>const JOBS = @json($jobs);</script>
<script>
@verbatim
(() => {
    const $ = s => document.querySelector(s);
    const STATUS = { live: 'Live', draft: 'Draft', closed: 'Closed' };
    let filter = 'all';
    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function render() {
        const cnt = k => k === 'all' ? JOBS.length : JOBS.filter(j => j.status === k).length;
        $('#tabs').innerHTML = [['all', 'All'], ['live', 'Live'], ['draft', 'Drafts'], ['closed', 'Closed']]
            .map(([k, l]) => `<button data-f="${k}" class="${filter === k ? 'on' : ''}">${l}<small>${cnt(k)}</small></button>`).join('');
        const list = JOBS.filter(j => filter === 'all' || j.status === filter);
        $('#grid').innerHTML = list.length ? list.map(j => `
            <article class="glass job-card" data-id="${j.id}">
                <div class="jt"><h3>${esc(j.title)}</h3><span class="st ${j.status === 'live' ? '' : j.status}">${STATUS[j.status]}</span></div>
                <p class="jm">${esc(j.loc)} · ${esc(j.type)} · ${esc(j.posted)}</p>
                <div class="skills">${j.skills.map(s => `<span>${esc(s)}</span>`).join('')}</div>
                <div class="meter"><i style="width:${Math.min(100, Math.round(j.apps / j.goal * 100))}%"></i></div>
                <div class="jf"><span><b>${j.apps}</b> applicants</span><span><b>${j.strong}</b> strong matches</span></div>
                <div class="jchk"><svg class="ic"><use href="#i-shield"/></svg> Authenticity check passed</div>
                <div class="jact">
                    <a class="btn ghost sm" href="/recruiter/applicants">View applicants</a>
                    ${j.status === 'live' ? '<button class="btn ghost sm" data-a="close" type="button">Close</button>' : ''}
                    ${j.status === 'draft' ? '<button class="btn primary sm" data-a="publish" type="button">Publish</button>' : ''}
                    ${j.status === 'closed' ? '<button class="btn ghost sm" data-a="reopen" type="button">Reopen</button>' : ''}
                </div>
            </article>`).join('') : `<div class="glass card empty">${JOBS.length ? 'No jobs in this tab.' : 'No jobs yet. Click “Post a job” to create your first one.'}</div>`;
        $('#sub').textContent = cnt('live') + ' live · ' + cnt('draft') + ' draft · ' + cnt('closed') + ' closed';
    }

    $('#tabs').addEventListener('click', e => { const b = e.target.closest('button'); if (b) { filter = b.dataset.f; render(); } });
    $('#grid').addEventListener('click', e => {
        const b = e.target.closest('[data-a]'); if (!b) return;
        const j = JOBS.find(x => x.id == b.closest('.job-card').dataset.id), a = b.dataset.a;
        b.disabled = true;
        window.api('/recruiter/jobs/' + j.id + '/status', 'POST', { action: a })
            .then(r => { j.status = r.status; j.posted = r.posted; render();
                toast(j.title + (a === 'publish' ? ' is now live' : a === 'close' ? ' closed' : ' reopened')); })
            .catch(err => { b.disabled = false; toast(err.message); });
    });

    // composer + authenticity check (the server runs the real rules)
    const comp = $('#composer');
    $('#newBtn').onclick = () => { comp.hidden = false; comp.scrollIntoView({ behavior: 'smooth', block: 'start' }); $('#title').focus(); };
    $('#closeComposer').onclick = () => { comp.hidden = true; };
    const setErr = (id, msg) => { const f = $('#' + id).closest('.f'); f.classList.toggle('err', !!msg); f.querySelector('.msg-err').textContent = msg || ''; return !msg; };

    const FIELD = { title: 'title', location: 'loc', salary_min: 'smin', salary_max: 'smin', description: 'desc' };
    const showVerdict = (checks, pass) => {
        const verdict = $('#verdict'); verdict.hidden = false;
        verdict.className = 'wide verdict ' + (pass ? 'pass' : 'fail');
        verdict.innerHTML = `<strong>${pass ? 'Passed. This posting looks genuine.' : 'Needs changes before it can go live.'}</strong><ul>` +
            checks.map(c => `<li class="${c.ok ? 'ok' : 'bad'}">${c.ok ? '✓' : '✕'} ${esc(c.label)}</li>`).join('') + '</ul>';
    };

    $('#jobForm').addEventListener('submit', e => {
        e.preventDefault();
        const v = id => $('#' + id).value.trim();
        const min = +v('smin') || 0, max = +v('smax') || 0;
        const ok = [
            setErr('title', v('title').length < 3 ? 'Enter a job title.' : ''),
            setErr('loc', v('loc') ? '' : 'Enter a location.'),
            setErr('smin', min && max && min > max ? 'Minimum cannot be above maximum.' : ''),
            setErr('desc', v('desc').length < 40 ? 'Write at least 40 characters so candidates know what to expect.' : '')
        ].every(Boolean);
        if (!ok) return;

        const btn = $('#jobForm button[type=submit]'); btn.disabled = true;
        window.api('/recruiter/jobs', 'POST', {
            title: v('title'), location: v('loc'), type: $('#type').value,
            salary_min: min || null, salary_max: max || null, description: v('desc'), skills: v('skills')
        }).then(r => {
            showVerdict(r.checks, true);
            JOBS.unshift(r.job); filter = 'all'; render(); toast('Saved as a draft. Publish it when you are ready.');
            setTimeout(() => { comp.hidden = true; $('#jobForm').reset(); $('#verdict').hidden = true; }, 1400);
        }).catch(err => {
            if (err.data && err.data.checks) showVerdict(err.data.checks, false);
            else if (err.data && err.data.errors) Object.entries(err.data.errors).forEach(([k, m]) => FIELD[k] && setErr(FIELD[k], m[0]));
            else toast(err.message);
        }).finally(() => { btn.disabled = false; });
    });

    render();
})();
@endverbatim
</script>
@endpush
