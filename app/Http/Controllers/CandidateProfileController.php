<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\CandidateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CandidateProfileController extends Controller
{
    public function edit(Request $request)
    {
        $c = $request->user();
        $completeness = $this->completeness($c);

        return view('candidate.profile.edit', compact('c', 'completeness'));
    }

    public function update(CandidateProfileRequest $request)
    {
        $c = $request->user();
        $v = $request->validated();

        // skills: "PHP, Laravel, MySQL" -> ['PHP','Laravel','MySQL']
        $v['skills'] = collect(explode(',', $v['skills'] ?? ''))
            ->map(fn ($s) => trim($s))->filter()->unique()->take(30)->values()->all();

        // faka row baad dao
        $v['experiences'] = collect($v['experiences'] ?? [])
            ->filter(fn ($e) => filled($e['company'] ?? null) || filled($e['title'] ?? null))
            ->values()->all();

        $v['projects'] = collect($v['projects'] ?? [])
            ->filter(fn ($p) => filled($p['title'] ?? null))
            ->values()->all();

        $v['educations'] = collect($v['educations'] ?? [])
            ->filter(fn ($e) => filled($e['degree'] ?? null) || filled($e['institution'] ?? null))
            ->values()->all();

        // Photo upload / remove
        if ($request->boolean('remove_photo') && $c->photo) {
            Storage::disk('public')->delete($c->photo);
            $v['photo'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($c->photo) {
                Storage::disk('public')->delete($c->photo);
            }
            $v['photo'] = $request->file('photo')->store('candidates', 'public');
        }

        unset($v['remove_photo']);

        $c->update([
            'name'       => $v['name'],
            'email'      => $v['email'],
            'phone'      => $v['phone'] ?? null,
            'category'   => $v['category'] ?? null,
            'experience' => $v['experience'] ?? null,
            'degree'     => $v['degree'] ?? null,
            'institution'=> $v['institution'] ?? null,
            'major'      => $v['major'] ?? null,
            'grad_year'  => $v['grad_year'] ?? null,
            'cgpa'       => $v['cgpa'] ?? null,
            'summary'        => $v['summary'] ?? null,
            'location'       => $v['location'] ?? null,
            'linkedin'       => $v['linkedin'] ?? null,
            'github'         => $v['github'] ?? null,
            'portfolio'      => $v['portfolio'] ?? null,
            'languages'      => $v['languages'] ?? null,
            'certifications' => $v['certifications'] ?? null,
            'skills'         => $v['skills'],
            'experiences'    => $v['experiences'],
            'projects'       => $v['projects'],
            'educations'     => $v['educations'],
            'photo'          => $v['photo'] ?? $c->photo,
        ]);

        return back()->with('success', 'Profile updated.');
    }

    public function cv(Request $request)
    {
        $c = $request->user()->load(['applications.job.recruiter']);
        $completeness = $this->completeness($c);

        $template = $request->query('template', $c->cv_template ?: 'classic');
        if (! in_array($template, ['classic', 'modern', 'europass'], true)) {
            $template = 'classic';
        }
        if ($c->cv_template !== $template) {
            $c->update(['cv_template' => $template]);
        }

        return view('candidate.profile.cv', compact('c', 'completeness', 'template'));
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