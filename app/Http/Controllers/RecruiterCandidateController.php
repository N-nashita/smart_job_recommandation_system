<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Lets a recruiter open the profile / CV of a candidate who applied to ONE OF THEIR OWN jobs.
 * Nothing else about the candidate (e.g. other applications) is loaded or shown.
 */
class RecruiterCandidateController extends Controller
{
    private const TEMPLATES = ['classic', 'modern', 'europass'];
    private const STATUS = ['new' => 'Applied', 'reviewed' => 'Reviewed', 'shortlisted' => 'Shortlisted',
        'interview' => 'Interview', 'rejected' => 'Rejected', 'hired' => 'Offer'];

    public function profile(Request $request, Application $application)
    {
        $a = $this->own($request, $application);
        $c = $a->candidate;

        return view('recruiter.candidate-profile', ['a' => $a, 'c' => $c, 'statusLabel' => self::STATUS[$a->status] ?? $a->status] + $this->cvData($c));
    }

    public function cv(Request $request, Application $application)
    {
        $a = $this->own($request, $application);
        $c = $a->candidate;

        $template = $request->query('template', $c->cv_template ?: 'classic');
        if (! in_array($template, self::TEMPLATES, true)) $template = 'classic';

        // NOTE: the candidate's saved template choice is NOT changed here.
        return view('recruiter.candidate-cv', ['a' => $a, 'c' => $c, 'template' => $template, 'completeness' => null] + $this->cvData($c));
    }

    private function own(Request $request, Application $application): Application
    {
        $r = $request->user('recruiter');
        abort_unless($r->verification_status === 'verified', 403);

        $application->load(['job', 'candidate']);
        abort_unless($application->job && $application->job->recruiter_id === $r->id, 403);
        abort_unless($application->candidate, 404);

        return $application;
    }

    /** The same variables the candidate's own CV templates expect. */
    private function cvData(User $c): array
    {
        $summary = $c->summary ?: 'Dedicated professional with experience in '.($c->category ?? 'my field').', seeking to contribute to a forward-thinking organization.'
            .($c->degree ? ' I hold a '.$c->degree.($c->major ? ' in '.$c->major : '').($c->institution ? ' from '.$c->institution : '').'.' : '');

        $edus = collect([[
            'degree' => $c->degree, 'institution' => $c->institution, 'major' => $c->major,
            'year' => $c->grad_year, 'result' => $c->cgpa ? 'CGPA: '.$c->cgpa : '',
        ]])->filter(fn ($e) => filled($e['degree']) || filled($e['institution']))
            ->concat(collect($c->educations ?? []))
            ->sortByDesc(fn ($e) => (int) ($e['year'] ?? 0))->values()->all();

        return [
            'skills'  => $c->skills ?? [],
            'exps'    => $c->experiences ?? [],
            'projs'   => $c->projects ?? [],
            'certs'   => collect(preg_split('/\r\n|\r|\n/', (string) $c->certifications))->map('trim')->filter()->values(),
            'links'   => collect(['LinkedIn' => $c->linkedin, 'GitHub' => $c->github, 'Portfolio' => $c->portfolio])->filter(),
            'contact' => collect([$c->phone, $c->email, $c->location])->filter()->all(),
            'role'    => collect([$c->category, $c->experience])->filter()->implode(' · '),
            'summary' => $summary,
            'realSummary' => $c->summary,
            'edus'    => $edus,
            'period'  => function ($e) {
                $s = $e['start'] ?? ''; $en = $e['end'] ?? '';
                return (! $s && ! $en) ? '' : trim($s.' – '.($en ?: 'Present'));
            },
        ];
    }
}
