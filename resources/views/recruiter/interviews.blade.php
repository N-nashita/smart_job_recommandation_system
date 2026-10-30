@extends('layouts.recruiter')

@php
    // MOCK DATA
    $interviews = [
        ['id' => 1, 'n' => 'Nabil Hasan',    'av' => 'sand',  'job' => 'Backend Engineer',   'd' => 0, 't' => '10:00', 'mode' => 'meet',   'status' => 'confirmed'],
        ['id' => 2, 'n' => 'Mim Chowdhury',  'av' => 'blush', 'job' => 'UI Designer',        'd' => 0, 't' => '14:30', 'mode' => 'phone',  'status' => 'confirmed'],
        ['id' => 3, 'n' => 'Rafi Ahmed',     'av' => 'sage',  'job' => 'Backend Engineer',   'd' => 1, 't' => '11:30', 'mode' => 'meet',   'status' => 'confirmed'],
        ['id' => 4, 'n' => 'Sadia Islam',    'av' => 'blush', 'job' => 'Backend Engineer',   'd' => 2, 't' => '15:30', 'mode' => 'office', 'status' => 'confirmed'],
        ['id' => 5, 'n' => 'Imran Hossain',  'av' => 'mint',  'job' => 'Frontend Developer', 'd' => 3, 't' => '10:00', 'mode' => 'meet',   'status' => 'waiting'],
        ['id' => 6, 'n' => 'Tania Akter',    'av' => 'mint',  'job' => 'Frontend Developer', 'd' => 4, 't' => '16:30', 'mode' => 'phone',  'status' => 'waiting'],
        ['id' => 7, 'n' => 'Farhan Kabir',   'av' => 'sand',  'job' => 'QA Intern',          'd' => -3, 't' => '11:30', 'mode' => 'meet',  'status' => 'done'],
    ];
    $feed = [
        ['pip c', 'i-check',   '<b>Nabil Hasan</b> accepted your interview call', 'Yesterday'],
        ['pip',   'i-refresh', '<b>Rafi Ahmed</b> added a new project', '2 hours ago'],
        ['pip',   'i-refresh', '<b>Sadia Islam</b> completed an AWS certificate', '5 hours ago'],
    ];
@endphp

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
                @foreach ($feed as [$cls, $icon, $text, $when])
                    <li><span class="{{ $cls }}"><svg class="ic"><use href="#{{ $icon }}"/></svg></span><div>{!! $text !!}<small>{{ $when }}</small></div></li>
                @endforeach
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
        <div class="slots" id="slots"></div>
        <div class="row"><button class="btn ghost sm" id="cancel" type="button">Cancel</button><button class="btn primary sm" id="save" type="button">Send new time</button></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // MOCK DATA: d = days from today. Replace with real interviews from the database.
    const IV = @json($interviews);
</script>
<script>
@verbatim
(() => {
    const $ = s => document.querySelector(s);
    const SLOTS = ['10:00', '11:30', '14:00', '15:30', '16:30', '17:30'];
    const ST = { confirmed: 'Confirmed', waiting: 'Awaiting reply', done: 'Completed' };
    const MODE = { meet: 'Google Meet', phone: 'Phone call', office: 'On-site' };
    let tab = 'upcoming', cur = null, slot = null;
    const ini = n => n.split(' ').map(w => w[0]).slice(0, 2).join('');
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
                    <div class="ev"><div class="evh"><div class="avatar ${i.av}">${ini(i.n)}</div><div><b>${i.n}</b><small>${i.job} · ${MODE[i.mode]}</small></div>
                        <span class="stage ${i.status === 'confirmed' ? 'short' : i.status === 'done' ? 'offer' : ''}">${ST[i.status]}</span></div>
                        ${i.status === 'done' ? '' : `<div class="evact">
                            ${i.status === 'confirmed' && i.mode !== 'office' ? `<button class="btn primary mini" data-a="join" data-id="${i.id}" type="button">${i.mode === 'meet' ? 'Join meeting' : 'Call now'}</button>` : ''}
                            <button class="btn ghost mini" data-a="re" data-id="${i.id}" type="button">Reschedule</button>
                            <button class="btn danger mini" data-a="cancel" data-id="${i.id}" type="button">Cancel</button></div>`}
                    </div></li>`).join('')}</ul>`).join('') : '<div class="empty">Nothing here right now.</div>';
        $('#sub').textContent = c('upcoming') + ' upcoming · ' + c('waiting') + ' waiting for a reply';
    }

    $('#tabs').addEventListener('click', e => { const b = e.target.closest('button'); if (b) { tab = b.dataset.t; render(); } });
    $('#days').addEventListener('click', e => {
        const b = e.target.closest('[data-a]'); if (!b) return;
        const i = IV.find(x => x.id == b.dataset.id);
        if (b.dataset.a === 'join') return toast('Opening ' + (i.mode === 'meet' ? 'the meeting for ' : 'the dialer for ') + i.n);
        if (b.dataset.a === 'cancel') { IV.splice(IV.indexOf(i), 1); render(); return toast('Interview with ' + i.n + ' cancelled. They have been notified.'); }
        cur = i; slot = null;
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
        cur.t = slot; cur.status = 'waiting'; close(); tab = 'waiting'; render(); toast('New time sent to ' + cur.n);
    };
    render();
})();
@endverbatim
</script>
@endpush
