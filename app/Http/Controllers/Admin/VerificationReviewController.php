<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recruiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationReviewController extends Controller
{
    public function index()
    {
        return view('admin.verifications', [
            'pending'  => Recruiter::where('verification_status', 'pending')->orderBy('verification_submitted_at')->get(),
            'reviewed' => Recruiter::whereIn('verification_status', ['verified', 'rejected'])->latest('reviewed_at')->limit(10)->get(),
        ]);
    }

    public function document(Recruiter $recruiter)
    {
        abort_unless($recruiter->verification_document && Storage::exists($recruiter->verification_document), 404);
        return Storage::download($recruiter->verification_document);
    }

    public function approve(Recruiter $recruiter)
    {
        $recruiter->forceFill([
            'verification_status' => 'verified',
            'verified_at'         => now(),
            'reviewed_at'         => now(),
            'verification_note'   => null,
        ])->save();

        return back()->with('status', $recruiter->company_name . ' is now verified.');
    }

    public function reject(Request $request, Recruiter $recruiter)
    {
        $data = $request->validate(['note' => ['required', 'string', 'min:5', 'max:500']]);

        $recruiter->forceFill([
            'verification_status' => 'rejected',
            'reviewed_at'         => now(),
            'verification_note'   => $data['note'],
        ])->save();

        return back()->with('status', $recruiter->company_name . ' was rejected.');
    }
}
