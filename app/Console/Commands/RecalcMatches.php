<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Services\MatchEngine;
use Illuminate\Console\Command;

class RecalcMatches extends Command
{
    protected $signature = 'match:recalc';
    protected $description = 'Recompute match scores for all applications';

    public function handle(MatchEngine $engine): int
    {
        Application::with('candidate', 'job')->get()->each(function ($a) use ($engine) {
            if (!$a->candidate || !$a->job) return;
            $b = $engine->breakdown($a->candidate, $a->job);
            $a->update([
                'match_score' => $b['total'], 'skills_score' => $b['skills'],
                'experience_score' => $b['experience'], 'location_score' => $b['location'],
            ]);
            $this->line("#{$a->id} → {$b['total']}");
        });
        return self::SUCCESS;
    }
}