<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Hybrid matching: asks the Python service (embeddings + skill overlap).
 * If the service is down, falls back to a local PHP calculation so nothing breaks.
 * Returns ['total','semantic','skills','experience','location'] as 0-100 ints.
 */
class MatchEngine
{
    public function breakdown($c, $job): array
    {
        $payload = [
            'candidate'       => ['text' => $this->candidateText($c), 'skills' => $this->arr($c->skills), 'location' => (string) ($c->location ?? $c->city ?? '')],
            'job'             => ['text' => trim($job->title.' '.$job->description.' '.implode(' ', $this->arr($job->skills))), 'skills' => $this->arr($job->skills), 'location' => (string) $job->location],
            'candidate_years' => $this->years($c),
            'job_years'       => $this->jobYears($job),
            'remote'          => stripos((string) $job->location, 'remote') !== false || stripos((string) ($job->type ?? ''), 'remote') !== false,
        ];

        if ($url = config('services.matcher.url')) {
            try {
                $r = Http::timeout(3)->post(rtrim($url, '/').'/match', $payload);
                if ($r->ok()) return $r->json();
            } catch (\Throwable $e) {
                Log::warning('Matcher service down, using local fallback: '.$e->getMessage());
            }
        }
        return $this->local($payload);
    }

    /** Same idea without ML: skill overlap + word overlap. */
    private function local(array $p): array
    {
        $norm = fn ($a) => array_values(array_unique(array_map(fn ($s) => strtolower(trim($s)), array_filter($a))));
        $js = $norm($p['job']['skills']); $cs = $norm($p['candidate']['skills']);
        $sk = $js ? count(array_intersect($js, $cs)) / count($js) : 0.7;

        $w = fn ($t) => array_unique(str_word_count(strtolower($t), 1));
        $a = $w($p['candidate']['text']); $b = $w($p['job']['text']);
        $sem = ($a && $b) ? count(array_intersect($a, $b)) / max(1, min(count($a), count($b))) : 0;

        $need = $p['job_years'];
        $ex = (!$need) ? 0.8 : min(1, $p['candidate_years'] / $need);
        $cl = strtolower($p['candidate']['location']); $jl = strtolower($p['job']['location']);
        $lo = $p['remote'] ? 1 : (($cl && $jl) ? ((str_contains($jl, $cl) || str_contains($cl, $jl)) ? 1 : 0.35) : 0.5);

        $t = 0.45 * min(1, $sem) + 0.35 * $sk + 0.10 * $ex + 0.10 * $lo;
        $pc = fn ($x) => (int) round(max(0, min(1, $x)) * 100);
        return ['total' => $pc($t), 'semantic' => $pc($sem), 'skills' => $pc($sk), 'experience' => $pc($ex), 'location' => $pc($lo)];
    }

    private function arr($v): array
    {
        if (is_string($v)) $v = json_decode($v, true) ?: preg_split('/\s*,\s*/', $v);
        return array_values(array_filter(array_map('strval', (array) $v)));
    }

    private function candidateText($c): string
    {
        $parts = [$c->headline ?? '', $c->summary ?? $c->bio ?? '', implode(' ', $this->arr($c->skills))];
        foreach ((array) ($c->experiences ?? []) as $e) $parts[] = ($e['title'] ?? $e['role'] ?? '').' '.($e['description'] ?? '');
        foreach ((array) ($c->projects ?? []) as $p) $parts[] = ($p['title'] ?? $p['name'] ?? '').' '.($p['description'] ?? '');
        return trim(implode(' ', array_filter($parts)));
    }

    /** Rough total years from experiences[].start/end (year or date strings). */
    private function years($c): float
    {
        $sum = 0;
        foreach ((array) ($c->experiences ?? []) as $e) {
            $s = (int) substr((string) ($e['start'] ?? $e['from'] ?? ''), 0, 4);
            $en = strtolower((string) ($e['end'] ?? $e['to'] ?? ''));
            $en = ($en === '' || str_contains($en, 'present')) ? (int) date('Y') : (int) substr($en, 0, 4);
            if ($s > 1980 && $en >= $s) $sum += $en - $s;
        }
        return (float) $sum;
    }

    private function jobYears($job): ?float
    {
        $t = ($job->experience ?? '').' '.$job->description;
        return preg_match('/(\d+)\s*\+?\s*(?:years|yrs)/i', $t, $m) ? (float) $m[1] : null;
    }
}
