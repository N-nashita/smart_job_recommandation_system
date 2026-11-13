@extends('layouts.candidate')

@section('title', 'Profile — JobSphere')

@section('main')
<div class="page-head">
    <div>
        <div class="eyebrow">Candidate portal</div>
        <h1 style="margin-top:10px">Profile</h1>
        <p id="sub">Edit your profile details. Your CV is generated from this information.</p>
    </div>
    <div class="acts">
        <a href="{{ route('candidate.dashboard') }}" class="btn ghost sm">
            <svg class="ic"><use href="#i-arrow-r"/></svg> Back to dashboard
        </a>
        <a href="{{ route('candidate.profile.cv') }}" class="btn primary">
            <svg class="ic"><use href="#i-file"/></svg> Generate CV
        </a>
    </div>
</div>

<!-- Completeness bar -->
<div class="glass card reveal comp-card">
    <div class="card-head comp-head">
        <div>
            <h3 style="margin:0; font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep)">Profile completeness</h3>
            <p class="sub" style="margin-top:6px; font-size:0.82rem">Fill in your details to improve job matches and CV quality.</p>
        </div>
        <div class="tp-actions">
            <b class="comp-pct">{{ $completeness }}%</b>
        </div>
    </div>
    <div class="score-fill" style="margin-top:14px"><span style="width:{{ $completeness }}%"></span></div>
    <p class="sub" style="margin-top:8px; font-size:0.82rem; color:var(--ink)">
        {{ $completeness === 100 ? 'Complete!' : 'Keep going — a complete profile gets more visibility.' }}
    </p>
</div>
@if (session('success'))
    <div class="glass reveal" style="background:var(--mint); border-color:transparent; color:var(--accent-deep); padding:12px 16px; border-radius:14px; margin-bottom:16px">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="glass reveal" style="background:#fdecea; border-color:transparent; color:var(--accent-deep); padding:12px 16px; border-radius:14px; margin-bottom:16px">
        <ul style="margin:0; padding-left:18px; font-size:0.88rem">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Profile form -->
<section class="glass card reveal">
    <form method="POST" action="{{ route('candidate.profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PATCH')

        {{-- Section 1: Personal info --}}
        <div style="padding:22px 22px 0; border-bottom:1px solid var(--line);">
            <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin-bottom:16px">Personal information</h3>
            <div class="pf-photo">
                <div class="pf-photo-box">
                    @unless ($c->photo_url)
                        <span id="photo-ph">{{ \App\Support\CandidateInfo::initials($c) }}</span>
                    @endunless
                    <img id="photo-img" alt="Profile photo" @if ($c->photo_url) src="{{ $c->photo_url }}" @else hidden @endif>
                </div>
                <div style="min-width:0; flex:1">
                    <label class="input-label">Profile photo <small>(JPG, PNG or WEBP, max 2MB)</small></label>
                    <input type="file" name="photo" id="photo-input" accept="image/png,image/jpeg,image/webp">
                    @error('photo') <span class="msg-err">{{ $message }}</span> @enderror
                    @if ($c->photo)
                        <label style="display:inline-flex; align-items:center; gap:8px; margin-top:10px; font-size:.84rem; color:var(--muted); cursor:pointer">
                            <input type="checkbox" name="remove_photo" value="1" style="width:16px; height:16px; padding:0; accent-color:var(--accent-deep)">
                            Remove current photo
                        </label>
                    @endif
                </div>
            </div>
            <div class="tools row" style="gap:12px; flex-wrap:wrap">
                <div class="f {{ $errors->has('name') ? 'err' : '' }}" style="flex:1 1 260px; min-width:220px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Full name <span style="color:var(--accent-deep)">*</span></label>
                    <input name="name" type="text" value="{{ old('name', $c->name) }}" required>
                    @error('name') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('email') ? 'err' : '' }}" style="flex:1 1 260px; min-width:220px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Email <span style="color:var(--accent-deep)">*</span></label>
                    <input name="email" type="email" value="{{ old('email', $c->email) }}" required>
                    @error('email') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('phone') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Phone</label>
                    <input name="phone" type="tel" value="{{ old('phone', $c->phone) }}" placeholder="+880 1xxx xxxxxx">
                    @error('phone') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('location') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Location</label>
                    <input name="location" type="text" value="{{ old('location', $c->location) }}" placeholder="e.g. Khulna, Bangladesh">
                    @error('location') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('languages') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    
                    <label class="input-label">Languages<small style="color:var(--muted); font-weight:400; margin-left:6px">(separate with commas)</small></label>
                    <input name="languages" type="text" value="{{ old('languages', $c->languages) }}" placeholder="e.g. Bangla, English">
                    @error('languages') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Current education --}}
        <div style="padding:22px; border-bottom:1px solid var(--line);">
            <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin-bottom:16px">Current education</h3>
            <div class="tools row" style="gap:12px; flex-wrap:wrap">
                <div class="f {{ $errors->has('degree') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Degree</label>
                    <input name="degree" type="text" value="{{ old('degree', $c->degree) }}" placeholder="e.g. BS Computer Science">
                    @error('degree') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('institution') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Institution</label>
                    <input name="institution" type="text" value="{{ old('institution', $c->institution) }}" placeholder="e.g. State University">
                    @error('institution') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('major') ? 'err' : '' }}" style="flex:1 1 200px; min-width:180px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Major</label>
                    <input name="major" type="text" value="{{ old('major', $c->major) }}" placeholder="e.g. Computer Science">
                    @error('major') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('grad_year') ? 'err' : '' }}" style="flex:1 1 160px; min-width:140px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Grad year</label>
                    <input name="grad_year" type="number" min="1900" max="2100" value="{{ old('grad_year', $c->grad_year) }}">
                    @error('grad_year') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('cgpa') ? 'err' : '' }}" style="flex:1 1 160px; min-width:140px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">CGPA</label>
                    <input name="cgpa" type="number" min="0" max="9.99" step="0.01" value="{{ old('cgpa', $c->cgpa) }}">
                    @error('cgpa') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2b: Job preferences --}}
        <div style="padding:22px; border-bottom:1px solid var(--line);">
            <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin-bottom:16px">Job preferences</h3>
            <div class="tools row" style="gap:12px; flex-wrap:wrap">
                <div class="f {{ $errors->has('category') ? 'err' : '' }}" style="flex:1 1 200px; min-width:180px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Preferred category</label>
                    <input name="category" type="text" value="{{ old('category', $c->category) }}" placeholder="e.g. Software Development">
                    @error('category') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('experience') ? 'err' : '' }}" style="flex:1 1 200px; min-width:180px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Experience level</label>
                    <input name="experience" type="text" value="{{ old('experience', $c->experience) }}" placeholder="e.g. Fresher, 3-5 years">
                    @error('experience') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2c: Previous education --}}
        <div style="padding:22px; border-bottom:1px solid var(--line);">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:6px">
                <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin:0">Previous education</h3>
                <button type="button" class="btn ghost sm" id="add-edu">+ Add education</button>
            </div>
            <p class="sub" style="color:var(--muted); font-size:.82rem; margin:0 0 14px">The degree above is your current/highest. Add SSC, HSC or other previous degrees here.</p>
            <div id="edu-list" style="display:grid; gap:12px"></div>
        </div>

        {{-- Section 3: Online profiles --}}
        <div style="padding:22px; border-bottom:1px solid var(--line);">
            <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin-bottom:16px">Online profiles</h3>
            <div class="tools row" style="gap:12px; flex-wrap:wrap">
                <div class="f {{ $errors->has('linkedin') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">LinkedIn</label>
                    <input name="linkedin" type="url" value="{{ old('linkedin', $c->linkedin) }}" placeholder="https://linkedin.com/in/...">
                    @error('linkedin') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('github') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">GitHub</label>
                    <input name="github" type="url" value="{{ old('github', $c->github) }}" placeholder="https://github.com/...">
                    @error('github') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('portfolio') ? 'err' : '' }}" style="flex:1 1 220px; min-width:200px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Portfolio</label>
                    <input name="portfolio" type="url" value="{{ old('portfolio', $c->portfolio) }}" placeholder="https://...">
                    @error('portfolio') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 4: Skills & certifications --}}
        <div style="padding:22px; border-bottom:1px solid var(--line);">
            <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin-bottom:16px">Skills & certifications</h3>
            <div class="tools row" style="gap:12px; flex-wrap:wrap">
                <div class="f {{ $errors->has('skills') ? 'err' : '' }}" style="flex:1 1 100%; min-width:260px; display:flex; flex-direction:column; gap:8px; margin-top:8px">
                    <label class="input-label">Skills <small style="color:var(--muted)">(separate with commas)</small></label>
                    <input name="skills" type="text" value="{{ old('skills', is_array($c->skills) ? implode(', ', $c->skills) : ($c->skills ?? '')) }}" placeholder="PHP, Laravel, MySQL, Git">
                    @error('skills') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
                <div class="f {{ $errors->has('certifications') ? 'err' : '' }}" style="flex:1 1 100%; min-width:260px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Certifications / Training</label>
                    <textarea name="certifications" rows="3" maxlength="1000" placeholder="One per line, e.g. Laravel Bootcamp, 2025">{{ old('certifications', $c->certifications) }}</textarea>
                    @error('certifications') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 5: Summary --}}
        <div style="padding:22px; border-bottom:1px solid var(--line);">
            <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin-bottom:16px">Summary / About you</h3>
            <div class="tools row" style="gap:12px; flex-wrap:wrap">
                <div class="f {{ $errors->has('summary') ? 'err' : '' }}" style="flex:1 1 100%; min-width:260px; display:flex; flex-direction:column; gap:8px">
                    <label class="input-label">Summary</label>
                    <textarea name="summary" rows="4" maxlength="1000" placeholder="2-3 lines about yourself, strengths, career goal">{{ old('summary', $c->summary) }}</textarea>
                    @error('summary') <span class="msg-err">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section 6: Work experience --}}
        <div style="padding:22px; border-bottom:1px solid var(--line);">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px">
                <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin:0">Work experience</h3>
                <button type="button" class="btn ghost sm" id="add-exp">+ Add experience</button>
            </div>
            <div id="exp-list" style="display:grid; gap:12px"></div>
        </div>

        {{-- Section 7: Projects --}}
        <div style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px">
                <h3 style="font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); margin:0">Projects</h3>
                <button type="button" class="btn ghost sm" id="add-proj">+ Add project</button>
            </div>
            <div id="proj-list" style="display:grid; gap:12px"></div>
        </div>

        <div style="padding:22px; display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end">
            <a href="{{ route('candidate.dashboard') }}" class="btn ghost sm">Cancel</a>
            <button class="btn primary" type="submit">Save changes</button>
        </div>
    </form>
</section>
@push('scripts')
@php
    $cvData = [
        'educations'  => old('educations', $c->educations ?? []),
        'experiences' => old('experiences', $c->experiences ?? []),
        'projects'    => old('projects', $c->projects ?? []),
    ];
@endphp
<script>
window.__cvData = @json($cvData);
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
        });
    }

    const field = (label, name, value, opts = {}) => {
        const wrap = document.createElement('div');
        if (opts.full) wrap.className = 'full';
        const l = document.createElement('label'); l.textContent = label;
        const el = document.createElement(opts.area ? 'textarea' : 'input');
        if (opts.area) el.rows = 3; else el.type = opts.type || 'text';
        el.name = name; el.value = value || ''; el.placeholder = opts.ph || '';
        wrap.append(l, el);
        return wrap;
    };

    const makeList = (listId, addId, prefix, build, items) => {
        const list = document.getElementById(listId);
        const addBtn = document.getElementById(addId);
        if (!list || !addBtn) return;
        let i = 0;
        const add = (data = {}) => {
            const row = document.createElement('div');
            row.className = 'pf-row';
            build(row, `${prefix}[${i++}]`, data);
            const del = document.createElement('button');
            del.type = 'button'; del.className = 'btn danger mini pf-del'; del.textContent = 'Remove';
            del.addEventListener('click', () => row.remove());
            row.append(del);
            list.append(row);
        };
        addBtn.addEventListener('click', () => add());
        (items || []).forEach(add);
    };

    const photoInput = document.getElementById('photo-input');
    if (photoInput) {
        photoInput.addEventListener('change', () => {
            const f = photoInput.files[0];
            if (!f) return;
            const img = document.getElementById('photo-img');
            const ph = document.getElementById('photo-ph');
            img.src = URL.createObjectURL(f);
            img.hidden = false;
            if (ph) ph.hidden = true;
        });
    }

    makeList('edu-list', 'add-edu', 'educations', (row, n, d) => {
        row.append(
            field('Degree / Exam', `${n}[degree]`, d.degree, { ph: 'e.g. HSC' }),
            field('Institution', `${n}[institution]`, d.institution, { ph: 'e.g. Govt. College' }),
            field('Group / Major', `${n}[major]`, d.major, { ph: 'e.g. Science' }),
            field('Passing year', `${n}[year]`, d.year, { ph: '2020' }),
            field('Result', `${n}[result]`, d.result, { ph: 'GPA 5.00', full: true })
        );
    }, window.__cvData.educations);

    makeList('exp-list', 'add-exp', 'experiences', (row, n, d) => {
        row.append(
            field('Company', `${n}[company]`, d.company, { ph: 'e.g. ABC Ltd' }),
            field('Job title', `${n}[title]`, d.title, { ph: 'e.g. Junior Developer' }),
            field('Start', `${n}[start]`, d.start, { ph: 'Jan 2024' }),
            field('End', `${n}[end]`, d.end, { ph: 'Present' }),
            field('Description', `${n}[description]`, d.description, { area: true, full: true, ph: 'Describe your work' })
        );
    }, window.__cvData.experiences);

    makeList('proj-list', 'add-proj', 'projects', (row, n, d) => {
        row.append(
            field('Project title', `${n}[title]`, d.title, { ph: 'e.g. JobSphere' }),
            field('Link', `${n}[link]`, d.link, { type: 'url', ph: 'https://github.com/...' }),
            field('Description', `${n}[description]`, d.description, { area: true, full: true, ph: 'What you built, which tech stack' })
        );
    }, window.__cvData.projects);
});
</script>
<style>
    body.candidate .input-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 6px;
        letter-spacing: 0.02em;
    }
    body.candidate .input-label small {
        font-weight: 400;
        font-size: 0.72rem;
        color: var(--muted);
        margin-left: 6px;
    }

    body.candidate input,
    body.candidate textarea,
    body.candidate select {
        width: 100%;
        font: inherit;
        font-size: 0.92rem;
        padding: 11px 16px;
        border: 1.5px solid var(--line);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.85);
        color: var(--ink);
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    body.candidate input:focus,
    body.candidate textarea:focus,
    body.candidate select:focus {
        border-color: var(--accent-deep);
        box-shadow: 0 0 0 4px rgba(150, 23, 26, 0.1);
        background: #fff;
    }
    body.candidate textarea {
        resize: vertical;
        min-height: 100px;
    }

    body.candidate .f {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    body.candidate .msg-err {
        color: #c0392b;
        font-size: 0.78rem;
        margin-top: 2px;
        display: block;
    }

    /* error state on inputs */
    body.candidate .f.err input,
    body.candidate .f.err textarea,
    body.candidate .f.err select {
        border-color: #c0392b;
        box-shadow: 0 0 0 4px rgba(192, 57, 43, 0.1);
    }

    body.candidate .pf-area, body.candidate .pf-row input, body.candidate .pf-row textarea {
        width: 100%; font: inherit; font-size: .9rem; padding: 10px 14px;
        border: 1.5px solid var(--line); border-radius: 14px; background: #fff; color: var(--ink);
        outline: none; transition: border-color .2s ease, box-shadow .2s ease;
    }
    body.candidate .pf-area:focus, body.candidate .pf-row input:focus, body.candidate .pf-row textarea:focus {
        border-color: var(--accent-deep);
        box-shadow: 0 0 0 4px rgba(150, 23, 26, 0.1);
        background: #fff;
    }
    body.candidate .pf-area { resize: vertical; min-height: 80px; }
    body.candidate .pf-photo { display: flex; gap: 18px; align-items: center; flex-wrap: wrap; margin-bottom: 18px; }
    body.candidate .pf-photo-box {
        flex: 0 0 96px; width: 96px; height: 96px; border-radius: 24px; overflow: hidden;
        background: var(--mint); color: var(--accent-deep); border: 1px solid var(--line);
        display: grid; place-items: center; font-family: var(--head); font-weight: 700; font-size: 1.8rem;
    }
    body.candidate .pf-photo-box img { width: 100%; height: 100%; object-fit: cover; }
    body.candidate .pf-photo input[type="file"] { padding: 9px 12px; }
    body.candidate .pf-row {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px;
        padding: 16px; border: 1px solid var(--line); border-radius: 18px; background: rgba(255,255,255,.6);
    }
    body.candidate .pf-row .full { grid-column: 1 / -1; }
    body.candidate .pf-row label { display: block; font-size: .78rem; color: var(--muted); margin-bottom: 4px; }
    body.candidate .pf-row .pf-del { grid-column: 1 / -1; justify-self: end; }
    @media (max-width: 640px) { body.candidate .pf-row { grid-template-columns: 1fr; } }

    /* Bigger beautiful percentage on edit page */
    body.candidate .comp-card .card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
    body.candidate .comp-pct {
        font-size: 2.2rem; font-weight: 800; font-family: var(--head); color: var(--accent-deep);
        line-height: 1.1;
    }
    body.candidate .score-fill span {
        height: 10px; border-radius: 6px; background: var(--accent-deep);
    }
    body.candidate .comp-head h3 { margin:0; font-size:1rem; font-weight:700; font-family:var(--head); text-transform:uppercase; letter-spacing:.08em; color:var(--accent-deep); }
</style>
@endpush
@endsection
