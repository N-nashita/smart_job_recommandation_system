@extends('layouts.recruiter')
@php
    // MOCK DATA: replace with real jobs from the database
    $jobs = [
        ['id' => 1, 'title' => 'Backend Engineer',   'loc' => 'Khulna', 'type' => 'Full-time',  'status' => 'live',   'apps' => 18, 'strong' => 8, 'posted' => '5 days ago',    'goal' => 25, 'skills' => ['Laravel', 'MySQL', 'REST APIs']],
        ['id' => 2, 'title' => 'Frontend Developer', 'loc' => 'Remote', 'type' => 'Full-time',  'status' => 'live',   'apps' => 6,  'strong' => 2, 'posted' => '2 days ago',    'goal' => 25, 'skills' => ['React', 'CSS', 'TypeScript']],
        ['id' => 3, 'title' => 'UI Designer',        'loc' => 'Dhaka',  'type' => 'Contract',   'status' => 'draft',  'apps' => 0,  'strong' => 0, 'posted' => 'Not published', 'goal' => 25, 'skills' => ['Figma', 'Prototyping']],
        ['id' => 4, 'title' => 'QA Intern',          'loc' => 'Khulna', 'type' => 'Internship', 'status' => 'closed', 'apps' => 11, 'strong' => 1, 'posted' => '6 weeks ago',   'goal' => 25, 'skills' => ['Testing', 'SQL']],
    ];
@endphp

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
<script>
    const JOBS = @json($jobs);
</script>
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
            </article>`).join('') : '<div class="glass card empty">No jobs here yet.</div>';
        $('#sub').textContent = cnt('live') + ' live · ' + cnt('draft') + ' draft · ' + cnt('closed') + ' closed';
    }

    $('#tabs').addEventListener('click', e => { const b = e.target.closest('button'); if (b) { filter = b.dataset.f; render(); } });
    $('#grid').addEventListener('click', e => {
        const b = e.target.closest('[data-a]'); if (!b) return;
        const j = JOBS.find(x => x.id == b.closest('.job-card').dataset.id), a = b.dataset.a;
        if (a === 'publish') { j.status = 'live'; j.posted = 'Just now'; toast(j.title + ' is now live'); }
        if (a === 'close')   { j.status = 'closed'; toast(j.title + ' closed'); }
        if (a === 'reopen')  { j.status = 'live'; toast(j.title + ' reopened'); }
        render();
    });

    // composer + authenticity check (mock rules; the real check will call your fake-job model)
    const comp = $('#composer');
    $('#newBtn').onclick = () => { comp.hidden = false; comp.scrollIntoView({ behavior: 'smooth', block: 'start' }); $('#title').focus(); };
    $('#closeComposer').onclick = () => { comp.hidden = true; };
    const setErr = (id, msg) => { const f = $('#' + id).closest('.f'); f.classList.toggle('err', !!msg); f.querySelector('.msg-err').textContent = msg || ''; return !msg; };

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

        const text = (v('title') + ' ' + v('desc')).toLowerCase();
        const checks = [
            ['Company is verified', true],
            ['No upfront fee requested', !/(registration|processing|training|security) (fee|deposit)|pay (to|before)|send money/.test(text)],
            ['Contact stays on JobSphere', !/(whatsapp|telegram) (only|number)|contact me on|gmail\.com|yahoo\.com/.test(text)],
            ['Salary looks realistic', !(max && max > 400000) && !/guaranteed|earn \d+ ?(k|000) (daily|per day)|easy money/.test(text)]
        ];
        const pass = checks.every(c => c[1]);
        const verdict = $('#verdict');
        verdict.hidden = false;
        verdict.className = 'wide verdict ' + (pass ? 'pass' : 'fail');
        verdict.innerHTML = `<strong>${pass ? 'Passed. This posting looks genuine.' : 'Needs changes before it can go live.'}</strong><ul>` +
            checks.map(([l, ok]) => `<li class="${ok ? 'ok' : 'bad'}">${ok ? '✓' : '✕'} ${l}</li>`).join('') + '</ul>';
        if (!pass) return;

        JOBS.unshift({ id: Date.now(), title: v('title'), loc: v('loc'), type: $('#type').value, status: 'draft', apps: 0, strong: 0, posted: 'Not published', goal: 25,
                       skills: v('skills') ? v('skills').split(',').map(s => s.trim()).filter(Boolean).slice(0, 5) : [] });
        filter = 'all'; render(); toast('Saved as a draft. Publish it when you are ready.');
        setTimeout(() => { comp.hidden = true; $('#jobForm').reset(); verdict.hidden = true; }, 1400);
    });

    render();
})();
@endverbatim
</script>
@endpush
