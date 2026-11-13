<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;

class CandidateSavedJobsController extends Controller
{
    public function index(Request $request)
    {
        $c = $request->user();
        $jobs = $c->savedJobs()
            ->with('recruiter')
            ->latest('saved_jobs.created_at')
            ->paginate(12)
            ->withQueryString();

        return view('candidate.saved.index', compact('jobs'));
    }

    public function toggle(Request $request, JobPost $job)
    {
        $c = $request->user();
        $relation = $c->savedJobs();

        if ($relation->where('job_post_id', $job->id)->exists()) {
            $relation->detach($job->id);
            return response()->json(['ok' => true, 'saved' => false]);
        }

        $relation->attach($job->id);
        return response()->json(['ok' => true, 'saved' => true]);
    }
}