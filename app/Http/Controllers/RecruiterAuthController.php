<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecruiterAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('recruiter')->attempt($credentials, $request->boolean('remember'))) {
            return response()->json(['message' => 'Incorrect email or password.'], 401);
        }

        $request->session()->regenerate();   // protects against session fixation

        $recruiter = Auth::guard('recruiter')->user();
        return response()->json(['name' => $recruiter->name, 'status' => $recruiter->verification_status]);
    }

    public function logout(Request $request)
    {
        Auth::guard('recruiter')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['ok' => true]);
    }
}