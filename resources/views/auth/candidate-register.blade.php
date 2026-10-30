@extends('layouts.candidate-auth')

@section('title', 'Candidate sign up')

@push('styles')
    <link rel="stylesheet" href="{{ asset('style/register-candidate.css') }}">
@endpush

@section('nav_cta')
    <a href="{{ route('candidate.login') }}" class="btn ghost">Log in</a>
    <a href="{{ route('recruiter.register') }}" class="btn primary">Recruiter sign up</a>
@endsection

@section('content')
<div class="reg">
    <!-- left panel -->
    <aside class="aside">
        <div class="eyebrow">For job seekers</div>
        <h1>Find work that <em>fits you.</em></h1>
        <p class="lead">Create your account in a few minutes and get matched with verified companies hiring in your field.</p>

        <ul class="perks">
            <li><svg class="ic"><use href="#i-check" /></svg> Apply to jobs from verified companies only</li>
            <li><svg class="ic"><use href="#i-check" /></svg> See your compatibility score before you apply</li>
            <li><svg class="ic"><use href="#i-check" /></svg> Recruiters can call you directly from your profile</li>
            <li><svg class="ic"><use href="#i-check" /></svg> Instant alerts for jobs in your category</li>
        </ul>

        <div class="how">
            <h3>How it works</h3>
            <ol>
                <li>Create your account</li>
                <li>Add your job category and education</li>
                <li>Start applying and get found by recruiters</li>
            </ol>
        </div>

        <div class="vcard" aria-label="Preview of your candidate profile">
            <span class="vlabel">Your profile will look like this</span>
            <div class="vhead">
                <div class="avatar mint" id="vAvatar">Y</div>
                <div class="vname">
                    <strong><span id="vName">Your Name</span>
                        <span class="vbadge"><svg class="ic"><use href="#i-shield" /></svg> Candidate</span></strong>
                    <small id="vSub">Job category · Experience</small>
                </div>
            </div>
            <div class="vstats">
                <div><b>24</b><span>Matched jobs</span></div>
                <div><b>92%</b><span>Best match</span></div>
                <div><b>5</b><span>Profile views</span></div>
            </div>
            <div class="vsite" id="vSite">you@email.com</div>
        </div>
    </aside>

    <!-- form card -->
    <div class="glass form-card" id="card">
        <div id="formView">
            <h2>Create your account</h2>
            <p class="sub">Already registered? <a href="{{ route('candidate.login') }}">Log in</a></p>

            <div class="stepper" aria-label="Progress">
                <div class="step active"><b>1</b><span>You</span></div>
                <div class="bar"><i></i></div>
                <div class="step"><b>2</b><span>Career</span></div>
                <div class="bar"><i></i></div>
                <div class="step"><b>3</b><span>Review</span></div>
            </div>

            <form id="reg" method="POST"
                  action="{{ route('candidate.register.store') }}"
                  data-action="{{ route('candidate.register.store') }}" novalidate>
                @csrf

                {{-- step 1: you --}}
                <div class="panel-step show" data-step="0">
                    <div class="row2">
                        <div class="f"><label for="name">Full name</label>
                            <input id="name" name="name" autocomplete="name" placeholder="e.g. Amina Rahman">
                            <span class="msg-err"></span>
                        </div>
                        <div class="f"><label for="email">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" placeholder="you@email.com">
                            <span class="msg-err"></span>
                        </div>
                    </div>
                    <div class="f"><label for="phone">Phone</label>
                        <div class="phone">
                            <span>+880</span>
                            <input id="phone" name="phone" type="tel" autocomplete="tel-national" placeholder="1XXX XXXXXX">
                        </div>
                        <span class="msg-err"></span>
                    </div>
                    <div class="row2">
                        <div class="f"><label for="password">Password</label>
                            <div class="pw">
                                <input id="password" name="password" type="password" autocomplete="new-password" placeholder="At least 8 characters">
                                <button type="button" class="eye" data-for="password" aria-label="Show password"><svg class="ic"><use href="#i-eye" /></svg></button>
                            </div>
                            <div class="meter" id="meter" data-s="0"><i></i><i></i><i></i><i></i></div>
                            <span class="hint" id="meterText">Use letters, numbers and a symbol.</span>
                            <span class="msg-err"></span>
                        </div>
                        <div class="f"><label for="password_confirmation">Confirm password</label>
                            <div class="pw">
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Repeat password">
                                <button type="button" class="eye" data-for="password_confirmation" aria-label="Show password"><svg class="ic"><use href="#i-eye" /></svg></button>
                            </div>
                            <span class="msg-err"></span>
                        </div>
                    </div>
                </div>

                {{-- step 2: career + education --}}
                <div class="panel-step" data-step="1">
                    <div class="row2">
                        <div class="f"><label for="category">Job category</label>
                            <select id="category" name="category">
                                <option value="">Select One</option>
                                @foreach (config('jobsphere.categories', []) as $item)
                                    <option>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="msg-err"></span>
                        </div>
                        <div class="f"><label for="experience">Experience</label>
                            <select id="experience" name="experience">
                                <option value="">Select experience</option>
                                @foreach (config('jobsphere.experience', []) as $item)
                                    <option>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="msg-err"></span>
                        </div>
                    </div>

                    <h3 class="sec-title">Education</h3>

                    <div class="row2">
                        <div class="f"><label for="degree">Degree / qualification</label>
                            <select id="degree" name="degree">
                                <option value="">Select One</option>
                                @foreach (config('jobsphere.degrees', []) as $item)
                                    <option>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="msg-err"></span>
                        </div>
                        <div class="f"><label for="grad_year">Passing / expected year</label>
                            <input id="grad_year" name="grad_year" type="number" inputmode="numeric" min="1970" max="2040" placeholder="e.g. 2026">
                            <span class="msg-err"></span>
                        </div>
                    </div>
                    <div class="f"><label for="institution">Institution name</label>
                        <input id="institution" name="institution" autocomplete="organization" placeholder="e.g. Khulna University of Engineering &amp; Technology">
                        <span class="msg-err"></span>
                    </div>
                    <div class="row2">
                        <div class="f"><label for="major">Department / major</label>
                            <input id="major" name="major" placeholder="e.g. Computer Science &amp; Engineering">
                            <span class="msg-err"></span>
                        </div>
                        <div class="f"><label for="cgpa">CGPA / GPA <span class="hint">(optional)</span></label>
                            <input id="cgpa" name="cgpa" type="number" inputmode="decimal" step="0.01" min="0" max="5" placeholder="e.g. 3.75">
                            <span class="msg-err"></span>
                        </div>
                    </div>
                </div>

                {{-- step 3: review --}}
                <div class="panel-step" data-step="2">
                    <dl class="review" id="review"></dl>
                    <div class="f"><label class="cb"><input type="checkbox" name="agree"> <span>I agree to the
                                <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.</span></label>
                        <span class="msg-err"></span>
                    </div>
                    <div class="banner" id="banner" role="alert" hidden></div>
                </div>

                <div class="actions">
                    <button type="button" class="btn ghost" id="back" hidden>Back</button>
                    <button type="button" class="btn primary" id="next">Continue <svg class="ic"><use href="#i-arrow" /></svg></button>
                    <button type="submit" class="btn primary" id="submit" hidden>Create account</button>
                </div>
            </form>
        </div>

        <!-- success -->
        <div class="done-view" id="doneView" hidden>
            <div class="tick"><svg class="ic"><use href="#i-check" /></svg></div>
            <h2>You're all set!</h2>
            <p class="sub">Your account is ready. Log in to complete your profile and start applying.</p>
            <p class="note" id="demoNote" hidden>Preview mode: nothing was saved.</p>
            <div class="cta-row">
                <a class="btn primary" href="{{ route('candidate.login') }}">Go to login</a>
                <a class="btn ghost" href="{{ url('/') }}">Back to home</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/candidate-register.js') }}"></script>
@endpush