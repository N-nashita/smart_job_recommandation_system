<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Notification;
use App\Support\CandidateInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecruiterApplicantsController extends Controller
{
    private const TO_UI = ['new' => 'new', 'reviewed' => 'new', 'shortlisted' => 'short', 'interview' => 'interview', 'hired' => 'offer', 'rejected' => 'rejected'];
    private const TO_DB = ['new' => 'new', 'short' => 'shortlisted', 'interview' => 'interview', 'offer' => 'hired', 'rejected' => 'rejected'];

    public function index(Request $request)
    {
        $r = $request->user('recruiter');
        $people = collect();
        $titles = collect();

        if ($r->verification_status === 'verified') {
            $people = Application::whereHas('job', fn ($q) => $q->where('recruiter_id', $r->id))
                ->with('candidate', 'job')->get()
                ->filter(fn ($a) => $a->candidate)
                ->map(fn ($a) => $this->row($a))->values();
            $titles = JobPost::where('recruiter_id', $r->id)->orderBy('title')->pluck('title')->unique()->values();
        }

        return view('recruiter.applicants', compact('people', 'titles'));
    }

    public function stage(Request $request, Application $application)
    {
        $this->own($request, $application);
        $v = $request->validate(['stage' => 'required|in:new,short,interview,offer,rejected']);
        $application->update(['status' => self::TO_DB[$v['stage']]]);

        Notification::create([
            'user_id' => $application->candidate_id,
            'type'    => 'application',
            'title'   => 'Application status updated',
            'body'    => 'Your application for “'.$application->job->title.'” is now '.ucfirst($application->status).'.',
            'data'    => ['job_id' => $application->job_id, 'application_id' => $application->id],
        ]);

        return response()->json(['ok' => true]);
    }

    public function interview(Request $request, Application $application)
    {
        $this->own($request, $application);
        abort_if($application->status === 'rejected', 422, 'Reopen this applicant before inviting.');
        $v = $request->validate([
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'slot' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'mode' => 'nullable|in:meet,phone,office',
        ]);

        $at = Carbon::createFromFormat('Y-m-d H:i', $v['date'].' '.$v['slot'], 'Asia/Dhaka');
        abort_if($at->isPast(), 422, 'Pick a time in the future.');

        Interview::create([
            'application_id' => $application->id,
            'recruiter_id'   => $request->user('recruiter')->id,
            'scheduled_at'   => $at->setTimezone(config('app.timezone')),
            'mode'           => $v['mode'] ?? 'meet',
            'round'          => 'Interview',
            'status'         => 'waiting',
        ]);
        $application->update(['status' => 'interview']);

        Notification::create([
            'user_id' => $application->candidate_id,
            'type'    => 'interview',
            'title'   => 'Interview scheduled',
            'body'    => 'You have an interview scheduled for “'.$application->job->title.'”.',
            'data'    => ['job_id' => $application->job_id, 'application_id' => $application->id],
        ]);

        return response()->json(['ok' => true]);
    }

    private function own(Request $request, Application $application): void
    {
        abort_unless($application->job && $application->job->recruiter_id === $request->user('recruiter')->id, 403);
    }

    private function row(Application $a): array
    {
        $u = $a->candidate;
        $title = CandidateInfo::title($u);
        return [
            'id'  => $a->id,
            'n'   => CandidateInfo::name($u),
            'ini' => CandidateInfo::initials($u),
            'job' => $a->job->title,
            't'   => $title ?: 'Applied for '.$a->job->title,
            'loc' => CandidateInfo::location($u) ?: '—',
            'm'   => (int) $a->match_score,
            'sk'  => CandidateInfo::skills($u),
            'up'  => (int) ($u->updated_at && $u->updated_at->gt(now()->subDays(3))),
            'upT' => $u->updated_at ? $u->updated_at->diffForHumans() : '—',
            'ago' => (int) $a->created_at->diffInDays(now()),
            'st'  => self::TO_UI[$a->status] ?? 'new',
            'bd'  => [(int) $a->skills_score, (int) $a->experience_score, (int) $a->location_score],
            'av'  => CandidateInfo::color($u),
            'sum' => $a->summary ?: 'No summary yet.',
        ];
    }
}
