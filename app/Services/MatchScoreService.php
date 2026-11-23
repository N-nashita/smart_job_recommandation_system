<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\User;

/**
 * Compatibility score between a candidate and a job (0-100).
 * Uses MatchEngine (embeddings + skills + experience + location).
 */
class MatchScoreService
{
    public function __construct(private MatchEngine $engine) {}

    public function score(User $candidate, JobPost $job): int
    {
        return (int) $this->engine->breakdown($candidate, $job)['total'];
    }

    public function breakdown(User $candidate, JobPost $job): array
    {
        return $this->engine->breakdown($candidate, $job);
    }
}