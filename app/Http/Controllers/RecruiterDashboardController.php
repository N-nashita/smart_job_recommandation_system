<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\FocusTask;
use App\Models\Interview;
use App\Models\JobPost;
use Illuminate\Http\Request;

class RecruiterDashboardController extends Controller
{
    public function index(Request $request)
    {
        $r        = $request->user('recruiter');
        $verified = $r->verification_status === 'verified';
        $tz       = 'Asia/Dhaka';
        $today    = now($tz)->toDateString();

        // Unverified recruiters get empty data: nothing is queried.
        $data = [
            'verified' => $verified,
            'stats' => ['new' => 0, 'interviews' => 0, 'open' => 0],
            'matches' => collect(), 'interviews' => collect(),
            'auto' => collect(), 'tasks' => collect(), 'jobs' => collect(),
        ];
        if (! $verified) {
            return view('recruiter.dashboard', $data);
        }

        $apps = Application::whereHas('job', fn ($q) => $q->where('recruiter_id', $r->id));

        $newCount   = (clone $apps)->where('status', 'new')->count();
        $strong     = (clone $apps)->where('status', 'new')->where('match_score', '>=', 85)->count();
        $todays     = Interview::where('recruiter_id', $r->id)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('scheduled_at', [now($tz)->startOfDay()->setTimezone(config('app.timezone')), now($tz)->endOfDay()->setTimezone(config('app.timezone'))])->with('application.candidate')->orderBy('scheduled_at')->get();
        $drafts     = JobPost::where('recruiter_id', $r->id)->where('status', 'draft')->get();
        $openCount  = JobPost::where('recruiter_id', $r->id)->where('status', 'open')->count();

        // AUTO suggestions: computed from live data every load, nothing stored.
        $auto = collect();
        if ($strong)  $auto->push(['title' => "Review {$strong} strong new ".str('match')->plural($strong), 'note' => '85%+ compatibility', 'url' => route('recruiter.applicants')]);
        if ($todays->count()) $auto->push(['title' => "Hold {$todays->count()} ".str('interview')->plural($todays->count()).' today', 'note' => 'First at '.$todays->first()->scheduled_at->setTimezone($tz)->format('H:i'), 'url' => route('recruiter.interviews')]);
        foreach ($drafts->take(2) as $d) $auto->push(['title' => "Publish “{$d->title}”", 'note' => 'Still a draft', 'url' => route('recruiter.jobs')]);
        if ($newCount && ! $strong) $auto->push(['title' => "Review {$newCount} new ".str('applicant')->plural($newCount), 'note' => 'Waiting for a first look', 'url' => route('recruiter.applicants')]);

        $data['jobs']       = JobPost::where('recruiter_id', $r->id)->withCount('applications')->latest()->limit(5)->get();
        $data['stats']      = ['new' => $newCount, 'interviews' => $todays->count(), 'open' => $openCount];
        $data['matches']    = (clone $apps)->where('status', 'new')->with('candidate', 'job')->orderByDesc('match_score')->limit(3)->get();
        $data['interviews'] = $todays;
        $data['auto']       = $auto;
        // MANUAL tasks: the recruiter's own, for today.
        $data['tasks']      = FocusTask::where('recruiter_id', $r->id)->where('for_date', $today)->orderBy('done')->orderBy('id')->get();

        return view('recruiter.dashboard', $data);
    }

    public function storeTask(Request $request)
    {
        $v = $request->validate(['title' => 'required|string|max:140', 'note' => 'nullable|string|max:200']);
        FocusTask::create($v + ['recruiter_id' => $request->user('recruiter')->id, 'for_date' => now('Asia/Dhaka')->toDateString()]);
        return back();
    }

    public function toggleTask(Request $request, FocusTask $task)
    {
        abort_unless($task->recruiter_id === $request->user('recruiter')->id, 403);
        $task->update(['done' => ! $task->done]);
        return response()->noContent();
    }

    public function destroyTask(Request $request, FocusTask $task)
    {
        abort_unless($task->recruiter_id === $request->user('recruiter')->id, 403);
        $task->delete();
        return back();
    }
}
