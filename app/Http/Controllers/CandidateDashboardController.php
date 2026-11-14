<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Notification;
use App\Models\User;
use App\Services\MatchScoreService;
use Illuminate\Http\Request;

class CandidateDashboardController extends Controller
{
    public function index(Request $request, MatchScoreService $match)
    {
        $c = $request->user();

        $applications = Application::where('candidate_id', $c->id)
            ->with(['job.recruiter'])->latest()->get();

        $interviews = Interview::whereHas('application', fn ($q) => $q->where('candidate_id', $c->id))
            ->with(['application.job'])->latest()->take(5)->get();

        $upcoming = $interviews->whereNotIn('status', ['cancelled', 'done'])->count();

        $stats = [
            'applications' => $applications->count(),
            'interviews'   => $upcoming,
            'saved'        => $c->savedJobs()->count(),
            'profileViews' => Notification::where('user_id', $c->id)
                ->where('type', 'profile_view')->count(),
        ];

        $recommended = JobPost::open()
            ->where('is_suspicious', false)
            ->whereDoesntHave('applications', fn ($q) => $q->where('candidate_id', $c->id))
            ->with('recruiter')->get()
            ->map(fn ($j) => ['job' => $j, 'score' => $match->score($c, $j)])
            ->sortByDesc('score')
            ->take(6)
            ->values();

        $completeness = $this->completeness($c);

        return view('candidate.dashboard', compact(
            'stats', 'applications', 'interviews', 'recommended', 'completeness'
        ));
    }

    private function completeness(User $c): int
    {
        $fields = [
            'name', 'email', 'phone', 'category', 'experience',
            'degree', 'institution', 'major', 'grad_year', 'cgpa',
            'summary', 'location', 'skills', 'languages', 'certifications',
            'linkedin', 'github', 'portfolio',
        ];
        $filled = 0;
        foreach ($fields as $f) {
            $val = $c->{$f};
            if ($val !== null && $val !== '' && $val !== false) {
                $filled++;
            }
        }
        return (int) round(($filled / count($fields)) * 100);
    }
}