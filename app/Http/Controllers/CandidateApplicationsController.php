<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use App\Http\Requests\StoreApplicationRequest;
use App\Services\MatchScoreService;
use Illuminate\Http\Request;

class CandidateApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $c = $request->user();

        $query = Application::where('candidate_id', $c->id)
            ->with(['job.recruiter', 'interviews']);

        $status = $request->string('status')->trim()->toString();
        if ($status) {
            $query->where('status', $status);
        }

        $applications = $query->latest()->paginate(12)->withQueryString();

        $statuses = [
            'new'         => 'Applied',
            'reviewed'    => 'Reviewed',
            'shortlisted' => 'Shortlisted',
            'interview'   => 'Interview',
            'rejected'    => 'Rejected',
            'hired'       => 'Hired',
        ];

        return view('candidate.applications.index', compact('applications', 'statuses', 'status'));
    }

    public function store(StoreApplicationRequest $request, JobPost $job, MatchScoreService $match)
    {
        $c = $request->user();

        if ($job->status !== 'open') {
            return response()->json(['message' => 'This job is not open for applications.'], 422);
        }

        if (Application::where('candidate_id', $c->id)->where('job_post_id', $job->id)->exists()) {
            return response()->json(['message' => 'You have already applied to this job.'], 422);
        }

        $score = $match->score($c, $job);

        Application::create([
            'job_post_id' => $job->id,
            'candidate_id' => $c->id,
            'match_score' => $score,
            'cv_template' => $request->validated('cv_template'),
            'status'      => 'new',
            'skills_score' => (int) ($score * 0.9),
            'experience_score' => (int) ($score * 0.85),
            'location_score' => (int) ($score * 0.95),
            'summary'     => 'Application submitted via JobSphere.',
        ]);

        return response()->json(['ok' => true, 'message' => 'Application submitted.']);
    }

    public function destroy(Request $request, Application $application)
    {
        $c = $request->user();
        abort_unless($application->candidate_id === $c->id, 403);

        $application->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Application withdrawn.']);
        }

        return back()->with('success', 'Application withdrawn.');
    }
}