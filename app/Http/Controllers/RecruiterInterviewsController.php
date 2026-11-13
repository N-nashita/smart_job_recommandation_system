<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use App\Support\CandidateInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecruiterInterviewsController extends Controller
{
    private const TZ = 'Asia/Dhaka';

    public function index(Request $request)
    {
        $r = $request->user('recruiter');
        $interviews = collect();
        $feed = collect();

        if ($r->verification_status === 'verified') {
            $rows = Interview::where('recruiter_id', $r->id)->where('status', '!=', 'cancelled')
                ->with('application.candidate', 'application.job')->orderBy('scheduled_at')->get()
                ->filter(fn ($i) => $i->application && $i->application->candidate);

            $today = now(self::TZ)->startOfDay();
            $interviews = $rows->map(function ($i) use ($today) {
                $at = $i->scheduled_at->copy()->setTimezone(self::TZ);
                $u  = $i->application->candidate;
                return [
                    'id' => $i->id, 'n' => CandidateInfo::name($u), 'ini' => CandidateInfo::initials($u), 'av' => CandidateInfo::color($u),
                    'job' => $i->application->job->title,
                    'd' => (int) $today->diffInDays($at->copy()->startOfDay(), false),
                    't' => $at->format('H:i'), 'mode' => $i->mode ?: 'meet',
                    'status' => $i->status === 'confirmed' && $i->scheduled_at->isPast() ? 'done' : $i->status,
                ];
            })->values();

            $feed = $this->feed($r->id);
        }
        return view('recruiter.interviews', compact('interviews', 'feed'));
    }

    public function update(Request $request, Interview $interview)
    {
        $this->own($request, $interview);
        $v = $request->validate([
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'slot' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ]);
        $at = Carbon::createFromFormat('Y-m-d H:i', $v['date'].' '.$v['slot'], self::TZ);
        abort_if($at->isPast(), 422, 'Pick a time in the future.');

        $interview->update(['scheduled_at' => $at->setTimezone(config('app.timezone')), 'status' => 'waiting']);
        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Interview $interview)
    {
        $this->own($request, $interview);
        $interview->update(['status' => 'cancelled']);   // kept, so the candidate side can show "cancelled"
        return response()->json(['ok' => true]);
    }

    private function own(Request $request, Interview $interview): void
    {
        abort_unless($interview->recruiter_id === $request->user('recruiter')->id, 403);
    }

    /** Live activity: replies and new applicants, newest first. Nothing is stored for it. */
    private function feed(int $recruiterId)
    {
        $mine = fn ($q) => $q->where('recruiter_id', $recruiterId);

        $replies = Interview::where('recruiter_id', $recruiterId)->where('status', 'confirmed')
            ->with('application.candidate')->latest('updated_at')->limit(5)->get()
            ->filter(fn ($i) => $i->application?->candidate)
            ->map(fn ($i) => ['pip c', 'i-check', '<b>'.e($i->application->candidate->name).'</b> accepted your interview call', $i->updated_at]);

        $applied = Application::whereHas('job', $mine)->with('candidate', 'job')->latest()->limit(5)->get()
            ->filter(fn ($a) => $a->candidate)
            ->map(fn ($a) => ['pip', 'i-refresh', '<b>'.e($a->candidate->name).'</b> applied for '.e($a->job->title), $a->created_at]);

        return $replies->concat($applied)->sortByDesc(fn ($x) => $x[3])->take(6)
            ->map(fn ($x) => [$x[0], $x[1], $x[2], $x[3]->diffForHumans()])->values();
    }
}
