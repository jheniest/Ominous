<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invite;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_suspended', false)->count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'suspended_users' => User::where('is_suspended', true)->count(),
            'total_invites' => Invite::count(),
            'active_invites' => Invite::where('status', 'active')->count(),
            'consumed_invites' => Invite::where('status', 'consumed')->count(),
            'expired_invites' => Invite::where('status', 'expired')->count(),
            'total_purchases' => Purchase::count(),
            'completed_purchases' => Purchase::where('status', 'completed')->count(),
            'total_revenue' => Purchase::where('status', 'completed')->sum('amount_paid'),
            'pending_purchases' => Purchase::where('status', 'pending')->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();
        $recentPurchases = Purchase::with('user')->latest()->take(10)->get();
        $recentActivity = ActivityLog::with('user')->latest()->take(20)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPurchases', 'recentActivity'));
    }

    public function users(Request $request)
    {
        $query = User::query()->withCount(['createdInvites', 'invitedUsers', 'purchases']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filter === 'admin') {
            $query->where('is_admin', true);
        } elseif ($request->filter === 'suspended') {
            $query->where('is_suspended', true);
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function userShow(int $id)
    {
        $user = User::with(['createdInvites', 'invitedUsers', 'invitedBy', 'purchases', 'redemptions', 'activityLogs'])
            ->findOrFail($id);

        $inviteTree = $user->invite_tree;

        return view('admin.users.show', compact('user', 'inviteTree'));
    }

    public function userSuspend(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->is_admin) {
            return back()->with('error', 'Não é possível suspender um Guardião.');
        }

        $user->suspend($request->reason);

        return back()->with('success', 'Usuário banido para as sombras.');
    }

    public function userUnsuspend(int $id)
    {
        $user = User::findOrFail($id);
        $user->unsuspend();

        return back()->with('success', 'Usuário libertado.');
    }

    public function invites(Request $request)
    {
        $query = Invite::with('createdBy')->withCount('redemptions');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->source) {
            $query->where('source', $request->source);
        }

        $invites = $query->latest()->paginate(30);

        return view('admin.invites.index', compact('invites'));
    }

    public function purchases(Request $request)
    {
        $query = Purchase::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $purchases = $query->latest()->paginate(30);

        return view('admin.purchases.index', compact('purchases'));
    }

    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->latest()->paginate(50);

        return view('admin.activity.index', compact('logs'));
    }
}
