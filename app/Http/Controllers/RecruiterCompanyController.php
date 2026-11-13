<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\Request;

class RecruiterCompanyController extends Controller
{
    public const INDUSTRIES = ['Technology', 'Design & Creative', 'Finance', 'Healthcare', 'Education',
        'Climate & Impact', 'Manufacturing', 'Retail & E-commerce', 'Business & Strategy', 'Other'];

    public function show(Request $request)
    {
        $r = $request->user('recruiter');
        $stats = ['open' => 0, 'applicants' => 0, 'hired' => 0];
        $openJobs = collect();

        if ($r->verification_status === 'verified') {
            $jobs = JobPost::where('recruiter_id', $r->id);
            $apps = Application::whereHas('job', fn ($q) => $q->where('recruiter_id', $r->id));
            $stats = [
                'open'       => (clone $jobs)->where('status', 'open')->count(),
                'applicants' => (clone $apps)->count(),
                'hired'      => (clone $apps)->where('status', 'hired')->count(),
            ];
            $openJobs = (clone $jobs)->where('status', 'open')->latest('published_at')->limit(5)->get(['title', 'location', 'type']);
        }

        return view('recruiter.company', ['industries' => self::INDUSTRIES] + compact('stats', 'openJobs'));
    }

    public function update(Request $request)
    {
        $r = $request->user('recruiter');
        $v = $request->validate([
            'name'         => 'required|string|max:100',
            'job_title'    => 'nullable|string|max:100',
            'phone'        => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'company_name' => 'required|string|max:120',
            'industry'     => 'required|in:'.implode(',', self::INDUSTRIES),
            'hq_location'  => 'required|string|max:100',
            'about'        => 'nullable|string|max:1500',
        ], ['phone.regex' => 'Enter a valid phone number.']);

        // email, website and registration number are NOT editable here: the verification checks depend on them.
        $r->forceFill($v)->save();

        return back()->with('status', 'Company profile updated.');
    }
}
