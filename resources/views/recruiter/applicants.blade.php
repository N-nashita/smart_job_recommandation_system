@extends('layouts.recruiter')

@section('title', 'Applicants — JobSphere')

@section('main')
        <div class="page-head">
            <div><div class="eyebrow">Recruiter workspace</div><h1 style="margin-top:10px">Applicants</h1><p id="sub">Everyone who applied to your open roles, ranked by compatibility.</p></div>
            <div class="acts">
                <div class="tabs" id="view"><button class="on" data-v="list">List</button><button data-v="board">Pipeline</button></div>
                <a href="{{ route('recruiter.jobs') }}" class="btn primary"><svg class="ic"><use href="#i-plus"/></svg> Post a job</a>
            </div>
        </div>

        <!-- filters -->
        <section class="glass tools">
            <div class="row">
                <label class="search"><svg class="ic"><use href="#i-search"/></svg><input id="q" type="search" placeholder="Search by name, skill or city" aria-label="Search applicants"></label>
                <select class="sel" id="job" aria-label="Role"><option value="">All roles</option><option>Backend Engineer</option><option>Frontend Developer</option><option>UI Designer</option></select>
                <select class="sel" id="min" aria-label="Minimum match"><option value="0">Any match</option><option value="90">90% and above</option><option value="80">80% and above</option></select>
                <select class="sel" id="sort" aria-label="Sort"><option value="match">Best match</option><option value="new">Newest first</option><option value="upd">Recently updated</option></select>
            </div>
            <div class="row" style="justify-content:space-between"><div class="tabs" id="stages"></div>
                <label class="chk"><input type="checkbox" id="upd"> Updated profiles only</label></div>
        </section>

        <section class="split" id="split">
            <div class="stack">
                <div class="glass card" id="listCard"><div class="cands" id="cands"></div></div>
                <div class="glass card" id="boardCard" hidden>
                    <div class="card-head"><div><h3>Hiring pipeline</h3><p class="sub">Use the arrow on a card to move it to the next stage</p></div></div>
                    <div class="board" id="board"></div>
                </div>
            </div>
            <div class="stack">
                <aside class="glass prev" id="prev" aria-live="polite"></aside>
            </div>
        </section>
    
@endsection

@section('modals')
<div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="mt">
    <div class="sheet">
        <h3 id="mt">Call for interview</h3>
        <p id="mp"></p>
        <div class="slots" id="slots"></div>
        <div class="row"><button class="btn ghost sm" id="cancel" type="button">Cancel</button><button class="btn primary sm" id="send" type="button">Send invitation</button></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
@verbatim
(() => {
    const $ = s => document.querySelector(s);
    const STAGES = [['new', 'New'], ['short', 'Shortlisted'], ['interview', 'Interview'], ['offer', 'Offer']];
    const LABEL = { new: 'New', short: 'Shortlisted', interview: 'Interview', offer: 'Offer', rejected: 'Rejected' };

    // MOCK DATA: replace with fetch('/recruiter/applicants-data') once the applicants tables exist
    const people = [
        { id: 1, n: 'Rafi Ahmed',      job: 'Backend Engineer',   t: 'Backend Engineer · 3 yrs',     loc: 'Khulna', m: 94, sk: ['Laravel','MySQL','REST APIs','Docker'], up: 1, upT: '2 hours ago',  ago: 1, st: 'short',     bd: [96,90,100], av: 'sage',  sum: 'Built three production Laravel APIs and just added a payments project to his profile.' },
        { id: 2, n: 'Sadia Islam',     job: 'Backend Engineer',   t: 'Backend Engineer · 2 yrs',     loc: 'Dhaka',  m: 91, sk: ['PHP','AWS','Docker'],                  up: 1, upT: '5 hours ago',  ago: 1, st: 'new',       bd: [92,88,80],  av: 'blush', sum: 'Recently completed an AWS certificate. Strong in deployment and cloud basics.' },
        { id: 5, n: 'Imran Hossain',   job: 'Frontend Developer', t: 'Frontend Developer · 3 yrs',   loc: 'Dhaka',  m: 90, sk: ['React','TypeScript','Tailwind'],       up: 1, upT: 'Today',        ago: 0, st: 'new',       bd: [93,88,80],  av: 'mint',  sum: 'Ships polished React interfaces. Open-source contributor with an active portfolio.' },
        { id: 3, n: 'Nabil Hasan',     job: 'Backend Engineer',   t: 'Full-stack Developer · 4 yrs', loc: 'Khulna', m: 88, sk: ['Vue','Laravel','Redis'],               up: 0, upT: '3 days ago',   ago: 3, st: 'interview', bd: [86,95,100], av: 'sand',  sum: 'Experienced full-stack developer. Accepted your interview call yesterday.' },
        { id: 6, n: 'Mim Chowdhury',   job: 'UI Designer',        t: 'UI Designer · 2 yrs',          loc: 'Dhaka',  m: 86, sk: ['Figma','Prototyping','Design systems'],up: 0, upT: '4 days ago',   ago: 4, st: 'interview', bd: [90,80,80],  av: 'blush', sum: 'Designs clean, systematic interfaces. Strong case studies on mobile apps.' },
        { id: 4, n: 'Tania Akter',     job: 'Frontend Developer', t: 'Frontend Developer · 1 yr',    loc: 'Khulna', m: 82, sk: ['React','CSS','Figma'],                 up: 0, upT: '1 week ago',   ago: 2, st: 'new',       bd: [84,70,100], av: 'mint',  sum: 'Early-career developer with a good eye for design and clean CSS.' },
        { id: 8, n: 'Lamia Sultana',   job: 'UI Designer',        t: 'Graphic Designer · 2 yrs',     loc: 'Rajshahi', m: 79, sk: ['Illustrator','Figma'],               up: 1, upT: 'Yesterday',    ago: 2, st: 'short',     bd: [78,80,70],  av: 'sage',  sum: 'Strong visual designer moving into product design. Updated her portfolio yesterday.' },
        { id: 7, n: 'Farhan Kabir',    job: 'Backend Engineer',   t: 'Backend Developer · 1 yr',     loc: 'Sylhet', m: 74, sk: ['Node.js','MongoDB'],                   up: 0, upT: '2 weeks ago',  ago: 5, st: 'rejected',  bd: [72,60,70],  av: 'sand',  sum: 'Limited experience with PHP and SQL, which your Backend role asks for.' }
    ];
    const slotsList = ['10:00', '11:30', '14:00', '15:30', '16:30', '17:30'];
    const f = { q: '', job: '', min: 0, sort: 'match', upd: false, stage: 'all', view: 'list' };
    let selId = people[0].id, slot = null;

    const ini = n => n.split(' ').map(w => w[0]).slice(0, 2).join('');
    const get = id => people.find(p => p.id === id);

    function filtered(ignoreStage) {
        const q = f.q.toLowerCase();
        return people.filter(p => {
            if (f.job && p.job !== f.job) return false;
            if (p.m < f.min) return false;
            if (f.upd && !p.up) return false;
            if (!ignoreStage && f.stage !== 'all' && p.st !== f.stage) return false;
            return !q || (p.n + ' ' + p.loc + ' ' + p.t + ' ' + p.sk.join(' ')).toLowerCase().includes(q);
        }).sort((a, b) => f.sort === 'match' ? b.m - a.m : f.sort === 'new' ? a.ago - b.ago : (b.up - a.up) || (a.ago - b.ago));
    }

    function renderStages() {
        const base = filtered(true);
        const cnt = k => k === 'all' ? base.length : base.filter(p => p.st === k).length;
        $('#stages').innerHTML = [['all', 'All'], ...STAGES, ['rejected', 'Rejected']].map(([k, l]) => `<button data-s="${k}" class="${f.stage === k ? 'on' : ''}">${l}<small>${cnt(k)}</small></button>`).join('');
    }

    function renderList(list) {
        $('#cands').innerHTML = list.length ? list.map(p => `
            <article class="cand ${p.id === selId ? 'is-sel' : ''}" data-id="${p.id}" tabindex="0">
                <div class="avatar ${p.av}">${ini(p.n)}</div>
                <div>
                    <div class="nm">${p.n} ${p.up ? '<span class="tag">Updated profile</span>' : ''} <span class="stage ${p.st}">${LABEL[p.st]}</span></div>
                    <div class="rl">${p.t}</div>
                    <div class="skills">${p.sk.map(s => `<span>${s}</span>`).join('')}</div>
                    <div class="meta2"><span>${p.job} · ${p.loc} · Updated ${p.upT}</span></div>
                </div>
                <div class="right"><div class="ring ${p.m < 85 ? 'warm' : ''}" style="--p:${p.m}"><b>${p.m}</b></div>match</div>
            </article>`).join('') : '<div class="empty">No applicants match these filters. Try clearing a filter.</div>';
    }

    function renderBoard(list) {
        $('#board').innerHTML = STAGES.map(([k, l], i) => {
            const items = list.filter(p => p.st === k);
            return `<div class="lane"><h4>${l} <b>${items.length}</b></h4>${items.map(p => `
                <div class="pc ${p.id === selId ? 'is-sel' : ''}" data-id="${p.id}"><div class="avatar ${p.av}">${ini(p.n)}</div>
                <div class="t"><strong>${p.n}</strong><small>${p.job} · ${p.m}%</small></div>
                ${i < 3 ? `<button type="button" data-next="${p.id}" aria-label="Move ${p.n} to ${STAGES[i + 1][1]}" title="Move to ${STAGES[i + 1][1]}"><svg class="ic"><use href="#i-arrow-r"/></svg></button>` : ''}</div>`).join('') || '<div class="none">Nothing here yet</div>'}</div>`;
        }).join('');
    }

    function renderPrev(list) {
        const p = get(selId);
        if (!p || !list.includes(p)) {
            const first = list[0];
            if (first) { selId = first.id; return renderPrev(list); }
            $('#prev').innerHTML = '<div class="empty">Select an applicant to see their full profile here.</div>'; return;
        }
        const closed = p.st === 'rejected';
        $('#prev').innerHTML = `
            <div class="ph"><div class="avatar ${p.av}">${ini(p.n)}</div><div><h3>${p.n}</h3>
                <div class="rl"><span>${p.t}</span><span><svg class="ic" style="vertical-align:-2px"><use href="#i-pin"/></svg> ${p.loc}</span></div></div>
                <div class="ring ${p.m < 85 ? 'warm' : ''}" style="--p:${p.m}"><b>${p.m}</b></div></div>
            <p class="sum">${p.sum}</p>
            <div style="display:flex;gap:8px;flex-wrap:wrap"><span class="stage ${p.st}">${LABEL[p.st]}</span><span class="tag g">Applied for ${p.job}</span>${p.up ? '<span class="tag">Updated ' + p.upT + '</span>' : ''}</div>
            <div class="bd">${[['Skills', p.bd[0]], ['Experience', p.bd[1]], ['Location', p.bd[2]]].map(([l, v]) => `<div><span>${l}</span><i style="--v:${v}%"></i><b>${v}%</b></div>`).join('')}</div>
            <div class="skills">${p.sk.map(s => `<span>${s}</span>`).join('')}</div>
            <div class="acts2">
                <button class="btn ${p.st === 'short' || p.st === 'interview' || p.st === 'offer' ? 'mini done' : 'ghost'}" data-a="short" ${closed ? 'disabled' : ''}>${p.st === 'new' || closed ? 'Shortlist' : 'Shortlisted ✓'}</button>
                <button class="btn primary" data-a="call" ${p.st === 'interview' || p.st === 'offer' || closed ? 'disabled style="opacity:.55;cursor:default"' : ''}><svg class="ic"><use href="#i-phone"/></svg> ${p.st === 'interview' || p.st === 'offer' ? 'Invited' : 'Call for interview'}</button>
                ${closed ? '<button class="btn ghost" data-a="reopen">Reopen</button>' : '<button class="btn danger" data-a="reject">Reject</button>'}
            </div>
            <div class="lnk"><a href="#" data-a="profile"><svg class="ic"><use href="#i-users"/></svg> View full profile</a><button type="button" data-a="cv"><svg class="ic"><use href="#i-file"/></svg> Download CV</button></div>`;
    }

    function render() {
        const list = filtered(false);
        renderStages(); renderList(list); renderBoard(filtered(true)); renderPrev(list.length ? list : filtered(true).filter(() => false));
        const board = f.view === 'board';
        $('#split').classList.toggle('wide', board); $('#listCard').hidden = board; $('#boardCard').hidden = !board;
        $('#sub').textContent = list.length + ' of ' + people.length + ' applicants shown';
    }

    function setStage(p, st, msg, icon) {
        p.st = st; render(); window.toast(msg.replace(/<\/?b>/g, ''));
    }

    // filters
    $('#q').addEventListener('input', e => { f.q = e.target.value.trim(); render(); });
    $('#job').addEventListener('change', e => { f.job = e.target.value; render(); });
    $('#min').addEventListener('change', e => { f.min = +e.target.value; render(); });
    $('#sort').addEventListener('change', e => { f.sort = e.target.value; render(); });
    $('#upd').addEventListener('change', e => { f.upd = e.target.checked; render(); });
    $('#stages').addEventListener('click', e => { const b = e.target.closest('button'); if (b) { f.stage = b.dataset.s; render(); } });
    $('#view').addEventListener('click', e => {
        const b = e.target.closest('button'); if (!b) return;
        f.view = b.dataset.v; document.querySelectorAll('#view button').forEach(x => x.classList.toggle('on', x === b));
        if (f.view === 'board') { f.stage = 'all'; } render();
    });

    // selecting a candidate
    function select(id) {
        selId = id; render();
        if (innerWidth < 1180) $('#prev').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    $('#cands').addEventListener('click', e => { const c = e.target.closest('.cand'); if (c) select(+c.dataset.id); });
    $('#cands').addEventListener('keydown', e => { if (e.key === 'Enter') { const c = e.target.closest('.cand'); if (c) select(+c.dataset.id); } });
    $('#board').addEventListener('click', e => {
        const nx = e.target.closest('[data-next]');
        if (nx) {
            const p = get(+nx.dataset.next), i = STAGES.findIndex(s => s[0] === p.st), to = STAGES[i + 1];
            if (to[0] === 'interview') return openCall(p);
            return setStage(p, to[0], `<b>${p.n}</b> moved to ${to[1]}`);
        }
        const c = e.target.closest('.pc'); if (c) select(+c.dataset.id);
    });

    // preview actions
    $('#prev').addEventListener('click', e => {
        const b = e.target.closest('[data-a]'); if (!b || b.disabled) return;
        e.preventDefault();
        const p = get(selId), a = b.dataset.a;
        if (a === 'short') return p.st === 'new' ? setStage(p, 'short', `You shortlisted <b>${p.n}</b>`) : setStage(p, 'new', `<b>${p.n}</b> moved back to New`, 'i-refresh');
        if (a === 'call') return openCall(p);
        if (a === 'reject') return setStage(p, 'rejected', `You rejected <b>${p.n}</b>`, 'i-x');
        if (a === 'reopen') return setStage(p, 'new', `<b>${p.n}</b> reopened`, 'i-refresh');
        if (a === 'profile') return window.toast('Full candidate profile opens here (connect to your teammate\'s profile page)');
        if (a === 'cv') return window.toast('CV download starts here once the CV generator is connected');
    });

    // interview modal
    function openCall(p) {
        selId = p.id; slot = null;
        $('#mp').textContent = 'Pick a slot for ' + p.n + '. They will see it on their profile right away.';
        $('#slots').innerHTML = slotsList.map(s => `<button type="button">${s}</button>`).join('');
        $('#modal').classList.add('open');
    }
    const close = () => $('#modal').classList.remove('open');
    $('#slots').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; slot = b.textContent; document.querySelectorAll('#slots button').forEach(x => x.classList.toggle('on', x === b)); });
    $('#cancel').onclick = close;
    $('#modal').addEventListener('click', e => { if (e.target.id === 'modal') close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    $('#send').onclick = () => {
        if (!slot) return window.toast('Pick a time slot first');
        const p = get(selId); close();
        setStage(p, 'interview', `Interview set with <b>${p.n}</b> at ${slot}`, 'i-phone');
    };

    render();
})();
@endverbatim
</script>
@endpush