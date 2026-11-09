<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Services\JobAuthenticityChecker;
use Illuminate\Http\Request;

class RecruiterJobsController extends Controller
{
    private const TO_UI = ['open' => 'live', 'draft' => 'draft', 'closed' => 'closed'];

    public function index(Request $request)
    {
        $r = $request->user('recruiter');
        $jobs = collect();
        if ($r->verification_status === 'verified') {
            $jobs = JobPost::where('recruiter_id', $r->id)->withCount([
                'applications as apps',
                'applications as strong' => fn ($q) => $q->where('match_score', '>=', 85),
            ])->latest()->get()->map(fn ($j) => $this->row($j))->values();
        }
        return view('recruiter.jobs', compact('jobs'));
    }

    public function store(Request $request)
    {
        $r = $request->user('recruiter');
        $v = $request->validate([
            'title'       => 'required|string|min:3|max:120',
            'location'    => 'required|string|max:80',
            'type'        => 'required|in:Full-time,Part-time,Contract,Internship',
            'salary_min'  => 'nullable|integer|min:0',
            'salary_max'  => 'nullable|integer|min:0|gte:salary_min',
            'description' => 'required|string|min:40|max:5000',
            'skills'      => 'nullable|string|max:300',
        ]);

        [$pass, $checks] = JobAuthenticityChecker::run($v['title'], $v['description'], $v['salary_max'] ?? null, $r->verification_status === 'verified');
        if (! $pass) {
            return response()->json(['message' => 'Needs changes before it can go live.', 'checks' => $checks], 422);
        }

        $skills = collect(explode(',', $v['skills'] ?? ''))->map('trim')->filter()->take(5)->values()->all();
        $job = JobPost::create([
            'recruiter_id' => $r->id, 'status' => 'draft', 'skills' => $skills,
        ] + collect($v)->only(['title', 'location', 'type', 'salary_min', 'salary_max', 'description'])->all());

        return response()->json(['ok' => true, 'checks' => $checks, 'job' => $this->row($job->loadCount(['applications as apps', 'applications as strong' => fn ($q) => $q->where('match_score', '>=', 85)]))]);
    }

    public function status(Request $request, JobPost $job)
    {
        abort_unless($job->recruiter_id === $request->user('recruiter')->id, 403);
        $a = $request->validate(['action' => 'required|in:publish,close,reopen'])['action'];

        $ok = match (true) {
            $a === 'publish' && $job->status === 'draft'  => $job->update(['status' => 'open', 'published_at' => now()]),
            $a === 'close'   && $job->status === 'open'   => $job->update(['status' => 'closed']),
            $a === 'reopen'  && $job->status === 'closed' => $job->update(['status' => 'open', 'published_at' => $job->published_at ?? now()]),
            default => false,
        };
        abort_unless($ok, 422, 'That action is not available for this job.');

        return response()->json(['ok' => true, 'status' => self::TO_UI[$job->status], 'posted' => $this->posted($job)]);
    }

    private function posted(JobPost $j): string
    {
        return $j->published_at ? $j->published_at->diffForHumans() : 'Not published';
    }

    private function row(JobPost $j): array
    {
        return [
            'id' => $j->id, 'title' => $j->title, 'loc' => $j->location ?: '—', 'type' => $j->type,
            'status' => self::TO_UI[$j->status] ?? 'draft', 'apps' => (int) ($j->apps ?? 0), 'strong' => (int) ($j->strong ?? 0),
            'posted' => $this->posted($j), 'goal' => 25, 'skills' => $j->skills ?? [],
        ];
    }
}
