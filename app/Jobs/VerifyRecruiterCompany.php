<?php

namespace App\Jobs;

use App\Models\Recruiter;
use App\Services\CompanyVerifier;
use Illuminate\Foundation\Bus\Dispatchable;

/** Runs the automatic checks after the registration response has been sent, so signup stays fast. */
class VerifyRecruiterCompany
{
    use Dispatchable;

    public function __construct(public int $recruiterId) {}

    public function handle(CompanyVerifier $verifier): void
    {
        if ($recruiter = Recruiter::find($this->recruiterId)) {
            $verifier->evaluate($recruiter);
        }
    }
}
