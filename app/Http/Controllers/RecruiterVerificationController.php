<?php

namespace App\Http\Controllers;

use App\Services\CompanyVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecruiterVerificationController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ], [
            'document.mimes' => 'Upload a PDF, JPG or PNG file.',
            'document.max'   => 'The file must be 4 MB or smaller.',
        ]);

        $r = $request->user('recruiter');
        if ($r->verification_document) Storage::delete($r->verification_document);

        $r->forceFill([
            'verification_document'     => $request->file('document')->store('verification'),   // private disk, not public
            'verification_status'       => $r->verification_status === 'verified' ? 'verified' : 'pending',
            'verification_note'         => null,
            'verification_submitted_at' => now(),
        ])->save();

        return back()->with('status', 'Document received. Our team will review it soon.');
    }

    public function recheck(Request $request)
    {
        app(CompanyVerifier::class)->evaluate($request->user('recruiter'));
        return back()->with('status', 'Checks updated.');
    }
}
