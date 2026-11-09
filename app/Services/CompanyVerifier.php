<?php

namespace App\Services;

use App\Models\Recruiter;
use Illuminate\Support\Facades\Http;

class CompanyVerifier
{
    /**
     * Run every automatic check, save the result and return the new status.
     * Only a registry confirmation can verify a company automatically.
     * Everything else stays "pending" until an admin reviews the uploaded document.
     */
    public function evaluate(Recruiter $r): string
    {
        $checks = [
            $this->registrationFormat($r),
            $this->workEmail($r),
            $this->emailMatchesWebsite($r),
            $this->websiteReachable($r),
            $this->registryLookup($r),
        ];

        $by      = collect($checks)->keyBy('key');
        $failed  = $by->contains('status', 'fail');
        $auto    = ! $failed
                   && $by['registry']['status'] === 'pass'
                   && $by['email_domain']['status'] === 'pass';

        $status = match (true) {
            $r->verification_status === 'verified' => 'verified',      // never downgrade automatically
            $auto                                  => 'verified',
            $r->verification_status === 'rejected' => 'rejected',      // stays rejected until new document
            default                                => 'pending',
        };

        $r->forceFill([
            'verification_checks'       => json_encode($checks),
            'verification_status'       => $status,
            'verified_at'               => $status === 'verified' ? ($r->verified_at ?? now()) : $r->verified_at,
            'verification_submitted_at' => $r->verification_submitted_at ?? now(),
        ])->save();

        return $status;
    }

    // ---------- individual checks ----------

    protected function registrationFormat(Recruiter $r): array
    {
        $ok = (bool) preg_match('/^[A-Za-z0-9][A-Za-z0-9\-\/\. ]{3,29}$/', trim((string) $r->registration_no));
        return $this->check('reg_format', 'Registration number format', $ok ? 'pass' : 'fail',
            $ok ? 'Looks like a valid number (not checked against a registry here).' : 'This does not look like a registration number.');
    }

    protected function workEmail(Recruiter $r): array
    {
        $domain = self::emailDomain($r->email);
        $free   = in_array($domain, config('verification.free_email_domains', []), true);
        return $this->check('work_email', 'Work email', $free ? 'warn' : 'pass',
            $free ? "$domain is a personal email provider. A company email is more trustworthy." : 'Uses a company email address.');
    }

    protected function emailMatchesWebsite(Recruiter $r): array
    {
        $host = self::hostOf($r->website);
        if (! $host) return $this->check('email_domain', 'Email matches website', 'skip', 'No website was given.');
        $domain = self::emailDomain($r->email);
        $ok = self::domainMatches($domain, $host);
        return $this->check('email_domain', 'Email matches website', $ok ? 'pass' : 'warn',
            $ok ? "$domain matches $host." : "The email domain ($domain) is different from the website ($host).");
    }

    protected function websiteReachable(Recruiter $r): array
    {
        $host = self::hostOf($r->website);
        if (! $host) return $this->check('website', 'Website is online', 'skip', 'No website was given.');
        if (! self::isPublicHost($host)) return $this->check('website', 'Website is online', 'fail', 'The website address could not be resolved to a public server.');

        try {
            $resp = Http::timeout(5)->withOptions(['allow_redirects' => false])
                ->withHeaders(['User-Agent' => 'JobSphereVerifier/1.0'])->get('https://' . $host);
            $ok = $resp->status() < 400;
            return $this->check('website', 'Website is online', $ok ? 'pass' : 'warn',
                $ok ? 'The website responds over HTTPS.' : 'The website answered with an error (' . $resp->status() . ').');
        } catch (\Throwable $e) {
            return $this->check('website', 'Website is online', 'warn', 'The website could not be reached right now.');
        }
    }

    protected function registryLookup(Recruiter $r): array
    {
        if (config('verification.registry_driver') !== 'opencorporates' || ! config('verification.opencorporates.token')) {
            return $this->check('registry', 'Company registry', 'skip', 'No registry is connected. A reviewer checks the certificate instead.');
        }

        try {
            $jur  = config('verification.opencorporates.jurisdiction');
            $resp = Http::timeout(8)->acceptJson()->get(
                'https://api.opencorporates.com/v0.4/companies/' . $jur . '/' . rawurlencode(trim($r->registration_no)),
                ['api_token' => config('verification.opencorporates.token')]
            );

            if ($resp->status() === 404) {
                return $this->check('registry', 'Company registry', 'warn', 'Number not found in the registry (it may not be covered). A reviewer will check.');
            }
            if (! $resp->ok()) {
                return $this->check('registry', 'Company registry', 'warn', 'The registry could not be queried right now.');
            }

            $c = $resp->json('results.company');
            if (! empty($c['inactive'])) {
                return $this->check('registry', 'Company registry', 'fail', 'The registry lists this company as inactive.');
            }
            if (! self::nameSimilar($c['name'] ?? '', $r->company_name)) {
                return $this->check('registry', 'Company registry', 'fail', 'The registered name (' . ($c['name'] ?? '?') . ') does not match the company name given.');
            }
            return $this->check('registry', 'Company registry', 'pass', 'Found and active: ' . $c['name'] . '.');
        } catch (\Throwable $e) {
            return $this->check('registry', 'Company registry', 'warn', 'The registry could not be queried right now.');
        }
    }

    protected function check(string $key, string $label, string $status, string $detail): array
    {
        return compact('key', 'label', 'status', 'detail');
    }

    // ---------- pure helpers (also unit-testable without Laravel) ----------

    public static function emailDomain(?string $email): string
    {
        return strtolower(substr(strrchr((string) $email, '@') ?: '', 1));
    }

    public static function hostOf(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') return null;
        if (! preg_match('#^https?://#i', $url)) $url = 'https://' . $url;
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host);
        return filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) && str_contains($host, '.') ? $host : null;
    }

    public static function domainMatches(string $emailDomain, string $host): bool
    {
        if ($emailDomain === '' || $host === '') return false;
        return $emailDomain === $host
            || str_ends_with($emailDomain, '.' . $host)
            || str_ends_with($host, '.' . $emailDomain);
    }

    public static function isPublicHost(string $host): bool
    {
        $ips = gethostbynamel($host) ?: [];
        if (! $ips) return false;
        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return false;
        }
        return true;
    }

    public static function normalizeName(string $name): string
    {
        $n = strtolower($name);
        $n = preg_replace('/\b(ltd|limited|pvt|private|plc|inc|llc|co|company|corp|corporation)\b\.?/', '', $n);
        return trim(preg_replace('/[^a-z0-9]+/', ' ', $n));
    }

    public static function nameSimilar(string $a, string $b): bool
    {
        $a = self::normalizeName($a);
        $b = self::normalizeName($b);
        if ($a === '' || $b === '') return false;
        similar_text($a, $b, $pct);
        return $pct >= 75;
    }
}
