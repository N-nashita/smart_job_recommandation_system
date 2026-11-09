<?php

namespace App\Support;

use App\Models\User;

/**
 * One place that reads candidate details from the teammate's `users` table.
 * If their column names differ, change ONLY this file.
 */
class CandidateInfo
{
    private const COLORS = ['sage', 'blush', 'mint', 'sand'];

    public static function name(User $u): string      { return (string) $u->name; }
    public static function title(User $u): string     { return (string) ($u->headline ?? $u->job_title ?? $u->title ?? ''); }
    public static function location(User $u): string  { return (string) ($u->location ?? $u->city ?? ''); }
    public static function color(User $u): string     { return self::COLORS[$u->id % 4]; }

    public static function initials(User $u): string
    {
        return collect(preg_split('/\s+/', trim($u->name)))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
    }

    public static function skills(User $u): array
    {
        $s = $u->skills ?? null;
        if (is_string($s)) {
            $d = json_decode($s, true);
            $s = is_array($d) ? $d : explode(',', $s);
        }
        if (is_iterable($s)) {
            return collect($s)->map(fn ($x) => trim(is_object($x) ? ($x->name ?? '') : (string) $x))->filter()->values()->take(6)->all();
        }
        return [];
    }
}
