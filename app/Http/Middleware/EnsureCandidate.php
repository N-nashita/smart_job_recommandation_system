<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Candidate-only gate: keeps recruiters (and guests) out of /candidate/*.
 */
class EnsureCandidate
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('web');

        if (! $user || $user->role !== 'candidate') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Candidate account required.'], 403);
            }
            abort(403, 'This area is for candidates only.');
        }

        return $next($request);
    }
}