<?php

namespace App\Http\Controllers;

use App\Models\Interview;
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
}