<?php

namespace App\Services;

/** Rule-based first pass. Swap/extend later with the fake-job model (EMSCAD) call. */
class JobAuthenticityChecker
{
    /** @return array{0: bool, 1: array<int, array{label:string, ok:bool}>} */
    public static function run(string $title, string $description, ?int $salaryMax, bool $companyVerified): array
    {
        $text = mb_strtolower($title.' '.$description);
        $checks = [
            ['label' => 'Company is verified', 'ok' => $companyVerified],
            ['label' => 'No upfront fee requested', 'ok' => ! preg_match('/(registration|processing|training|security) (fee|deposit)|pay (to|before)|send money/', $text)],
            ['label' => 'Contact stays on JobSphere', 'ok' => ! preg_match('/(whatsapp|telegram) (only|number)|contact me on|gmail\.com|yahoo\.com/', $text)],
            ['label' => 'Salary looks realistic', 'ok' => ! ($salaryMax && $salaryMax > 400000) && ! preg_match('/guaranteed|earn \d+ ?(k|000) (daily|per day)|easy money/', $text)],
        ];
        return [collect($checks)->every(fn ($c) => $c['ok']), $checks];
    }
}
