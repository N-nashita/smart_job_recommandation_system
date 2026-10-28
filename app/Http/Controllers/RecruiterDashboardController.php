<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecruiterDashboardController extends Controller
{
    public function data(Request $request)
    {
        $r = $request->user('recruiter');

        return response()->json([
            'name'         => $r->name,
            'first_name'   => explode(' ', trim($r->name))[0],
            'company_name' => $r->company_name,
            'industry'     => $r->industry,
            'hq_location'  => $r->hq_location,
            'status'       => $r->verification_status,   // pending | verified | rejected
            // TODO: add applicants, jobs and interviews once your teammate's tables exist
        ]);
    }
}