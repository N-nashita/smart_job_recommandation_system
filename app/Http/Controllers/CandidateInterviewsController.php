<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Notification;
use Illuminate\Http\Request;

class CandidateInterviewsController extends Controller
{
    public function index(Request $request)
    {
        $c = $request->user();

        $interviews = Interview::whereHas('application', fn ($q) => $q->where('candidate_id', $c->id))
            ->with(['application.job', 'application.job.recruiter'])
            ->latest('scheduled_at')
            ->get();

        return view('candidate.interviews.index', compact('interviews'));
    }

    public function accept(Request $request, Interview $interview)
    {
        $c = $request->user();

        abort_unless($interview->application->candidate_id === $c->id, 403);
        abort_if($interview->status !== 'waiting', 422, 'This interview is no longer pending.');

        $interview->update(['status' => 'confirmed']);

        $this->notifyRecruiter($interview, 'accepted');

        return back()->with('status', 'Interview accepted.');
    }

    public function reject(Request $request, Interview $interview)
    {
        $c = $request->user();

        abort_unless($interview->application->candidate_id === $c->id, 403);
        abort_if($interview->status !== 'waiting', 422, 'This interview is no longer pending.');

        $interview->update(['status' => 'rejected']);

        $this->notifyRecruiter($interview, 'rejected');

        return back()->with('status', 'Interview declined.');
    }

    private function notifyRecruiter(Interview $interview, string $action): void
    {
        $application = $interview->application;
        $job = $application->job;
        $recruiter = $job->recruiter;
        $candidate = $application->candidate;

        $verb = $action === 'accepted' ? 'accepted your interview call' : 'declined your interview call';

        Notification::create([
            'user_id' => $recruiter->id,
            'type'    => 'interview_' . $action,
            'title'   => 'Interview ' . ucfirst($action),
            'body'    => "{$candidate->name} {$verb} for {$job->title}.",
            'data'    => [
                'interview_id' => $interview->id,
                'application_id' => $application->id,
                'candidate_id' => $candidate->id,
                'job_id' => $job->id,
                'action' => $action,
            ],
        ]);
    }
}