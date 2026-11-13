<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class CandidateNotificationsController extends Controller
{
    public function index(Request $request)
    {
        $c = $request->user();

        $notifications = Notification::where('user_id', $c->id)
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('candidate.notifications.index', compact('notifications'));
    }

    public function read(Request $request, Notification $notification)
    {
        $c = $request->user();
        abort_unless($notification->user_id === $c->id, 403);

        $notification->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}