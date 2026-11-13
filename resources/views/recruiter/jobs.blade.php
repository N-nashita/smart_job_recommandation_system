@extends('layouts.recruiter')

@section('title', 'My jobs — JobSphere')

@section('main')
<div class="page-head">
    <div><div class="eyebrow">Recruiter workspace</div><h1 style="margin-top:10px">My jobs</h1><p id="sub">Your job postings.</p></div>
    <div class="acts">
        <div class="tabs" id="tabs"></div>
        <button class="btn primary" id="newBtn" type="button"><svg class="ic"><use href="#i-plus"/></svg> Post a job</button>
    </div>
</div>

<!-- composer -->
<section class="glass card" id="composer" hidden>
    <div class="card-head"><div><h3 id="composerTitle">New job posting</h3><p class="sub">Fill in the details, then save it as a draft or publish it right away.</p></div>
        <button class="btn ghost sm" id="closeComposer" type="button">Cancel</button></div>
    <form id="jobForm" class="jform" novalidate>
        <div class="f"><label for="title">Job title</label><input id="title" placeholder="e.g. Backend Engineer"><span class="msg-err"></span></div>
        <div class="f"><label for="loc">Location</label><input id="loc" placeholder="e.g. Khulna or Remote"><span class="msg-err"></span></div>
        <div class="f"><label for="type">Type</label><select class="sel" id="type" style="width:100%"><option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option></select></div>
        <div class="f"><label for="smin">Salary range (BDT / month)</label>
            <div class="two"><input id="smin" type="number" min="0" placeholder="Min"><input id="smax" type="number" min="0" placeholder="Max"></div><span class="msg-err"></span></div>
        <div class="f wide"><label for="desc">Description</label><textarea id="desc" rows="5" placeholder="What will this person do? What skills do they need?"></textarea><span class="msg-err"></span></div>
        <div class="f wide"><label for="skills">Required skills <small style="color:var(--muted)">(comma separated)</small></label><input id="skills" placeholder="Laravel, MySQL, REST APIs"></div>
        <div class="wide row" style="display:flex;gap:12px"><button class="btn primary" type="submit" value="publish">Publish now</button><button class="btn ghost" type="submit" value="draft">Save as draft</button><button class="btn primary" type="submit" value="save" id="saveEdit" hidden>Save changes</button></div>
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
                <div class="jact">
                    <a class="btn ghost sm" href="/recruiter/applicants">View applicants</a>
                    ${j.status === 'live' ? '<button class="btn ghost sm" data-a="close" type="button">Close</button>' : ''}
                    ${j.status === 'draft' ? '<button class="btn primary sm" data-a="publish" type="button">Publish</button>' : ''}
                    ${j.status === 'closed' ? '<button class="btn ghost sm" data-a="reopen" type="button">Reopen</button>' : ''}
                    <button class="btn ghost sm" data-a="edit" type="button">Edit</button>
                    ${j.apps === 0 ? '<button class="btn danger sm" data-a="delete" type="button">Delete</button>' : ''}
                </div>
            </article>`).join('') : `<div class="glass card empty">${JOBS.length ? 'No jobs in this tab.' : 'No jobs yet. Click “Post a job” to create your first one.'}</div>`;
        $('#sub').textContent = cnt('live') + ' live · ' + cnt('draft') + ' draft · ' + cnt('closed') + ' closed';
    }

    $('#tabs').addEventListener('click', e => { const b = e.target.closest('button'); if (b) { filter = b.dataset.f; render(); } });
    $('#grid').addEventListener('click', e => {
        const b = e.target.closest('[data-a]'); if (!b) return;
        const j = JOBS.find(x => x.id == b.closest('.job-card').dataset.id), a = b.dataset.a;
        if (a === 'edit') return openEdit(j);
        if (a === 'delete') {
            if (!confirm('Delete "' + j.title + '"? This cannot be undone.')) return;
            b.disabled = true;
            return window.api('/recruiter/jobs/' + j.id, 'DELETE')
                .then(() => { JOBS.splice(JOBS.indexOf(j), 1); render(); toast(j.title + ' deleted'); })
                .catch(err => { b.disabled = false; toast(err.message); });
        }
        b.disabled = true;
        window.api('/recruiter/jobs/' + j.id + '/status', 'POST', { action: a })
            .then(r => { j.status = r.status; j.posted = r.posted; render();
                toast(j.title + (a === 'publish' ? ' is now live' : a === 'close' ? ' closed' : ' reopened')); })
            .catch(err => { b.disabled = false; toast(err.message); });
    });

    // composer
    const comp = $('#composer');
    let editing = null;   // the job being edited, or null when creating
    const mode = job => {
        editing = job;
        $('#composerTitle').textContent = job ? 'Edit job posting' : 'New job posting';
        document.querySelectorAll('#jobForm button[value=publish], #jobForm button[value=draft]').forEach(b => b.hidden = !!job);
        $('#saveEdit').hidden = !job;
    };
    const open = () => { comp.hidden = false; comp.scrollIntoView({ behavior: 'smooth', block: 'start' }); $('#title').focus(); };
    function openEdit(j) {
        mode(j); $('#jobForm').reset();
        $('#title').value = j.title; $('#loc').value = j.raw_loc; $('#type').value = j.type;
        $('#smin').value = j.smin || ''; $('#smax').value = j.smax || ''; $('#desc').value = j.desc; $('#skills').value = j.skills.join(', ');
        open();
    }
    $('#newBtn').onclick = () => { mode(null); $('#jobForm').reset(); open(); };
    $('#closeComposer').onclick = () => { comp.hidden = true; mode(null); };
    const setErr = (id, msg) => { const f = $('#' + id).closest('.f'); f.classList.toggle('err', !!msg); f.querySelector('.msg-err').textContent = msg || ''; return !msg; };

    const FIELD = { title: 'title', location: 'loc', salary_min: 'smin', salary_max: 'smin', description: 'desc' };

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

        const publish = !!(e.submitter && e.submitter.value === 'publish');
        const btns = document.querySelectorAll('#jobForm button[type=submit]'); btns.forEach(b => b.disabled = true);
        const body = { title: v('title'), location: v('loc'), type: $('#type').value, salary_min: min || null, salary_max: max || null, description: v('desc'), skills: v('skills') };
        const req = editing ? window.api('/recruiter/jobs/' + editing.id, 'PUT', body) : window.api('/recruiter/jobs', 'POST', { ...body, publish });
        req.then(r => {
            if (editing) { JOBS[JOBS.indexOf(editing)] = { ...r.job, status: editing.status, posted: editing.posted }; toast('Changes saved'); }
            else { JOBS.unshift(r.job); filter = 'all'; toast(publish ? v('title') + ' is now live' : 'Saved as a draft. Publish it when you are ready.'); }
            render(); comp.hidden = true; mode(null); $('#jobForm').reset();
        }).catch(err => {
            if (err.data && err.data.errors) Object.entries(err.data.errors).forEach(([k, m]) => FIELD[k] && setErr(FIELD[k], m[0]));
            else toast(err.message);
        }).finally(() => { btns.forEach(b => b.disabled = false); });
    });

    render();
})();
@endverbatim
</script>
@endpush
