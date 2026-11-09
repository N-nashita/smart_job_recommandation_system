<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Server-side lock: unverified recruiters cannot post jobs, change applicant status or call candidates. */
class EnsureRecruiterVerified
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user('recruiter')?->verification_status === 'verified') {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your company must be verified before you can do this.'], 403);
        }
        return redirect()->route('recruiter.verification');
    }
}
