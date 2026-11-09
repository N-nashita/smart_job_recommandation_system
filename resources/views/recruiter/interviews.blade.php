@extends('layouts.recruiter')

@section('title', 'Interviews — JobSphere')

@section('main')
<div class="page-head">
    <div><div class="eyebrow">Recruiter workspace</div><h1 style="margin-top:10px">Interviews</h1><p id="sub">Your schedule and candidate replies.</p></div>
    <div class="acts"><div class="tabs" id="tabs"></div></div>
</div>

<section class="split">
    <div class="stack">
        <div class="glass card"><div id="days"></div></div>
    </div>
    <div class="stack">
        <div class="glass card">
            <div class="card-head" style="margin-bottom:6px"><div><h3>Candidate activity</h3><p class="sub">Replies and profile updates</p></div></div>
            <ul class="feed">
                @forelse ($feed as [$cls, $icon, $text, $when])
                    <li><span class="{{ $cls }}"><svg class="ic"><use href="#{{ $icon }}"/></svg></span><div>{!! $text !!}<small>{{ $when }}</small></div></li>
                @empty
                    <li class="sub">No activity yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</section>
@endsection

@section('modals')
<div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="mt">
    <div class="sheet">
        <h3 id="mt">Reschedule interview</h3>
        <p id="mp"></p>
        <label class="dtl">Date <input type="date" id="idate"></label>
        <div class="slots" id="slots"></div>
        <div class="row"><button class="btn ghost sm" id="cancel" type="button">Cancel</button><button class="btn primary sm" id="save" type="button">Send new time</button></div>
    </div>
</div>
@endsection

@push('scripts')
<script>const IV = @json($interviews);</script>
<script>
@verbatim
(() => {
    const $ = s => document.querySelector(s);
    const SLOTS = ['10:00', '11:30', '14:00', '15:30', '16:30', '17:30'];
    const ST = { confirmed: 'Confirmed', waiting: 'Awaiting reply', done: 'Completed' };
    const MODE = { meet: 'Google Meet', phone: 'Phone call', office: 'On-site' };
    let tab = 'upcoming', cur = null, slot = null;
    const ini = i => i.ini;
    const dayLabel = d => { const x = new Date(); x.setDate(x.getDate() + d); const s = x.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'short' });
        return d === 0 ? 'Today · ' + s : d === 1 ? 'Tomorrow · ' + s : s; };
    const inTab = i => tab === 'upcoming' ? i.status === 'confirmed' && i.d >= 0 : tab === 'waiting' ? i.status === 'waiting' : i.status === 'done';

    function render() {
        const c = k => IV.filter(i => k === 'upcoming' ? i.status === 'confirmed' && i.d >= 0 : k === 'waiting' ? i.status === 'waiting' : i.status === 'done').length;
        $('#tabs').innerHTML = [['upcoming', 'Upcoming'], ['waiting', 'Awaiting reply'], ['past', 'Past']].map(([k, l]) => `<button data-t="${k}" class="${tab === k ? 'on' : ''}">${l}<small>${c(k)}</small></button>`).join('');
        const list = IV.filter(i => tab === 'past' ? i.status === 'done' : inTab(i)).sort((a, b) => a.d - b.d || a.t.localeCompare(b.t));
        const days = [...new Set(list.map(i => i.d))];
        $('#days').innerHTML = days.length ? days.map(d => `
            <h4 class="dayh">${dayLabel(d)}</h4>
            <ul class="tl ivl">${list.filter(i => i.d === d).map(i => `
                <li class="${i.d === 0 && i.status === 'confirmed' ? 'now' : ''}"><time>${i.t}</time><span class="dot"></span>
                    <div class="ev"><div class="evh"><div class="avatar ${i.av}">${ini(i)}</div><div><b>${i.n}</b><small>${i.job} · ${MODE[i.mode]}</small></div>
                        <span class="stage ${i.status === 'confirmed' ? 'short' : i.status === 'done' ? 'offer' : ''}">${ST[i.status]}</span></div>
                        ${i.status === 'done' ? '' : `<div class="evact">
                            ${i.status === 'confirmed' && i.mode !== 'office' ? `<button class="btn primary mini" data-a="join" data-id="${i.id}" type="button">${i.mode === 'meet' ? 'Join meeting' : 'Call now'}</button>` : ''}
                            <button class="btn ghost mini" data-a="re" data-id="${i.id}" type="button">Reschedule</button>
                            <button class="btn danger mini" data-a="cancel" data-id="${i.id}" type="button">Cancel</button></div>`}
                    </div></li>`).join('')}</ul>`).join('') : `<div class="empty">${IV.length ? 'Nothing here right now.' : 'No interviews yet. Call a candidate from the Applicants page to schedule one.'}</div>`;
        $('#sub').textContent = c('upcoming') + ' upcoming · ' + c('waiting') + ' waiting for a reply';
    }

    $('#tabs').addEventListener('click', e => { const b = e.target.closest('button'); if (b) { tab = b.dataset.t; render(); } });
    $('#days').addEventListener('click', e => {
        const b = e.target.closest('[data-a]'); if (!b) return;
        const i = IV.find(x => x.id == b.dataset.id);
        if (b.dataset.a === 'join') return toast('Opening ' + (i.mode === 'meet' ? 'the meeting for ' : 'the dialer for ') + i.n);
        if (b.dataset.a === 'cancel') return window.api('/recruiter/interviews/' + i.id, 'DELETE')
            .then(() => { IV.splice(IV.indexOf(i), 1); render(); toast('Interview with ' + i.n + ' cancelled. They have been notified.'); })
            .catch(err => toast(err.message));
        cur = i; slot = null;
        const d = new Date(); d.setDate(d.getDate() + 1); $('#idate').min = new Date().toISOString().slice(0, 10); $('#idate').value = d.toISOString().slice(0, 10);
        $('#mp').textContent = 'Pick a new time for ' + i.n + '. They will confirm it on their profile.';
        $('#slots').innerHTML = SLOTS.map(s => `<button type="button">${s}</button>`).join('');
        $('#modal').classList.add('open');
    });
    $('#slots').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; slot = b.textContent; document.querySelectorAll('#slots button').forEach(x => x.classList.toggle('on', x === b)); });
    const close = () => $('#modal').classList.remove('open');
    $('#cancel').onclick = close;
    $('#modal').addEventListener('click', e => { if (e.target.id === 'modal') close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    $('#save').onclick = () => {
        if (!slot) return toast('Pick a time slot first');
        const date = $('#idate').value; if (!date) return toast('Pick a date first');
        window.api('/recruiter/interviews/' + cur.id, 'PATCH', { date, slot })
            .then(() => { const dd = Math.round((new Date(date + 'T00:00') - new Date(new Date().toDateString())) / 864e5);
                cur.t = slot; cur.d = dd; cur.status = 'waiting'; close(); tab = 'waiting'; render(); toast('New time sent to ' + cur.n); })
            .catch(err => toast(err.message));
    };
    render();
})();
@endverbatim
</script>
@endpush
