<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\User;

/**
 * Compatibility score between a candidate and a job (0-100).
 *
 * TODO: this is a rule-based placeholder. Replace with the trained
 * match model (skills vs requirements, experience band, location, etc.).
 */
class MatchScoreService
{
    public function score(User $candidate, JobPost $job): int
    {
        $score = 0;
        $title = mb_strtolower((string) $job->title);
        $category = mb_strtolower((string) $candidate->category);

        if ($category) {
            if (str_contains($title, $category) || str_contains($category, $title)) {
                $score += 55;   // direct category match
            } else {
                $score += 15;   // same general area
            }
        }

        if ($candidate->experience) {
            $score += 25;       // has experience stated
        }
        if ($candidate->degree && $candidate->institution) {
            $score += 20;       // education stated
        }

        return (int) min(100, max(0, $score));
    }
}