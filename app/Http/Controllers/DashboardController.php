<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load(['createdInvites', 'invitedUsers', 'purchases']);

        $stats = [
            'total_invites' => $user->createdInvites->count(),
            'active_invites' => $user->createdInvites->where('status', 'active')->count(),
            'consumed_invites' => $user->createdInvites->where('status', 'consumed')->count(),
            'total_invited' => $user->invitedUsers->count(),
            'total_purchases' => $user->purchases->count(),
            'total_spent' => $user->purchases->where('status', 'completed')->sum('amount_paid'),
        ];

        $recentInvites = $user->createdInvites()->latest()->take(5)->get();
        $recentInvited = $user->invitedUsers()->latest('invited_at')->take(5)->get();

        return view('dashboard.index', compact('stats', 'recentInvites', 'recentInvited'));
    }
}
