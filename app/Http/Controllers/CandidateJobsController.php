<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use App\Services\MatchScoreService;
use Illuminate\Http\Request;

class CandidateJobsController extends Controller
{
    public function index(Request $request, MatchScoreService $match)
    {
        $c = $request->user();

        $appliedJobIds = Application::where('candidate_id', $c->id)->pluck('job_post_id')->toArray();
        $savedJobIds   = $c->savedJobs()->pluck('job_post_id')->toArray();

        $query = JobPost::open()
            ->where('is_suspicious', false)
            ->with('recruiter');

        $q = $request->string('q');
        if ($q) {
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $location = $request->string('location');
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }

        $type = $request->string('type');
        if ($type) {
            $query->where('type', $type);
        }

        $category = $request->string('category');
        if ($category) {
            $query->where('skills', 'like', "%{$category}%");
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();

        $jobs->getCollection()->transform(fn ($j) => [
            'job'   => $j,
            'score' => $match->score($c, $j),
            'applied' => in_array($j->id, $appliedJobIds, true),
            'saved'   => in_array($j->id, $savedJobIds, true),
        ]);

        $locations = JobPost::open()->where('is_suspicious', false)->distinct()->pluck('location');
        $types     = ['Full-time', 'Part-time', 'Contract', 'Internship'];
        $categories = ['Software Development', 'Design', 'DevOps', 'Marketing', 'Sales', 'Data'];

        return view('candidate.jobs.index', compact(
            'jobs', 'q', 'location', 'type', 'category',
            'locations', 'types', 'categories'
        ));
    }

    public function show(Request $request, JobPost $job, MatchScoreService $match)
    {
        $c = $request->user();
        $job->load(['recruiter', 'applications' => fn ($q) => $q->where('candidate_id', $c->id)]);

        $applied = $job->applications->isNotEmpty();
        $saved   = $job->savedBy()->where('user_id', $c->id)->exists();
        $score   = $match->score($c, $job);

        return view('candidate.jobs.show', compact('job', 'applied', 'saved', 'score'));
    }
}