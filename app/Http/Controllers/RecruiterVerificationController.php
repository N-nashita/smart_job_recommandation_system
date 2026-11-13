<?php

namespace App\Http\Controllers;

use App\Services\CompanyVerifier;
use Illuminate\Http\Request;

class RecruiterVerificationController extends Controller
{
    /** Fix the details the checks use, then run the checks again. */
    public function update(Request $request)
    {
        $r = $request->user('recruiter');
        abort_if($r->verification_status === 'verified', 403);

        $v = $request->validate([
            'website'         => ['required', 'string', 'max:255'],
            'registration_no' => ['required', 'regex:/^[A-Za-z0-9][A-Za-z0-9\-\/\. ]{3,29}$/'],
        ], ['registration_no.regex' => 'Use 4 to 30 letters, numbers, dashes or slashes.']);

        $r->forceFill($v)->save();
        $status = app(CompanyVerifier::class)->evaluate($r);

        return back()->with('status', $status === 'verified' ? 'Your company is verified.' : 'Details saved. Some checks still need attention.');
    }

    public function recheck(Request $request)
    {
        $status = app(CompanyVerifier::class)->evaluate($request->user('recruiter'));
        return back()->with('status', $status === 'verified' ? 'Your company is verified.' : 'Checks updated.');
    }
}
