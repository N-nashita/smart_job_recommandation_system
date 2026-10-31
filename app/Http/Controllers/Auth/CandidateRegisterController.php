<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CandidateRegisterController extends Controller
{
    public function create()
    {
        return view('auth.candidate-register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'category' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:30'],
            'degree' => ['nullable', 'string', 'max:50'],
            'institution' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'grad_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'cgpa' => ['nullable', 'numeric', 'min:0', 'max:9.99'],
        ]);

        $candidate = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'category' => $validated['category'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'degree' => $validated['degree'] ?? null,
            'institution' => $validated['institution'] ?? null,
            'major' => $validated['major'] ?? null,
            'grad_year' => $validated['grad_year'] ?? null,
            'cgpa' => $validated['cgpa'] ?? null,
        ]);

        Auth::guard('web')->login($candidate);

        return redirect('/dashboard');
    }
}