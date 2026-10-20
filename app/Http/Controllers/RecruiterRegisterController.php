<?php

namespace App\Http\Controllers;

use App\Models\Recruiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecruiterRegisterController extends Controller
{
    private const FREE_DOMAINS = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com',
        'icloud.com', 'proton.me', 'protonmail.com', 'live.com', 'aol.com'];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'min:2', 'max:100'],
            'job_title' => ['required', 'string', 'min:2', 'max:100'],
            'email'     => ['required', 'email', 'max:150', 'unique:recruiters,email',
                function ($attr, $value, $fail) {           // company email only
                    if (in_array(strtolower(substr(strrchr($value, '@'), 1)), self::FREE_DOMAINS, true)) {
                        $fail('Please use your company email, not a personal one.');
                    }
                }],
            'phone'           => ['nullable', 'string', 'max:25'],
            'password'        => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Za-z]/', 'regex:/\d/'],
            'company_name'    => ['required', 'string', 'min:2', 'max:150'],
            'registration_no' => ['required', 'string', 'regex:/^[A-Za-z0-9\-\/ ]{4,30}$/'],
            'website'         => ['required', 'string', 'max:200'],
            'industry'        => ['required', 'string', 'max:80'],
            'hq_location'     => ['required', 'string', 'min:2', 'max:120'],
            'agree'           => ['accepted'],
        ]);
        unset($data['agree']);

        $recruiter = Recruiter::create($data);

        $status = $this->verifyCompany($recruiter);
        $recruiter->update([
            'verification_status' => $status,
            'verified_at'         => $status === 'verified' ? now() : null,
        ]);

        return response()->json(['status' => $status], 201);   // the page shows this status
    }

    private function verifyCompany(Recruiter $r): string
    {
        // TODO: call the company registry API here, for example:
        // $res = Http::timeout(10)->get(config('services.registry.url'), ['reg_no' => $r->registration_no]);
        // if ($res->successful() && strcasecmp($res->json('name'), $r->company_name) === 0) return 'verified';
        // if ($res->status() === 404) return 'rejected';
        return 'pending';   // until the API is connected, you can approve manually in the database
    }
}