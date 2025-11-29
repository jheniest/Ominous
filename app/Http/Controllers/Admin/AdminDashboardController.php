<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invite;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoReport;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // User Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_suspended', false)->count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'suspended_users' => User::where('is_suspended', true)->count(),
            'online_users' => $this->getOnlineUsersCount(),
        ];

        // Video Statistics
        $videoStats = [
            'total_videos' => Video::count(),
            'pending_videos' => Video::where('status', 'pending')->count(),
            'approved_videos' => Video::where('status', 'approved')->count(),
            'rejected_videos' => Video::where('status', 'rejected')->count(),
            'videos_today' => Video::whereDate('created_at', today())->count(),
        ];

        // Report Statistics
        $reportStats = [
            'total_reports' => VideoReport::count(),
            'pending_reports' => VideoReport::where('status', 'pending')->count(),
            'reviewed_reports' => VideoReport::where('status', 'reviewed')->count(),
            'dismissed_reports' => VideoReport::where('status', 'dismissed')->count(),
            'reports_today' => VideoReport::whereDate('created_at', today())->count(),
        ];

        // Report breakdown by reason
        $reportsByReason = VideoReport::select('reason', DB::raw('count(*) as count'))
            ->where('status', 'pending')
            ->groupBy('reason')
            ->get()
            ->pluck('count', 'reason')
            ->toArray();

        // Site Settings
        $siteSettings = [
            'public_uploads_enabled' => SiteSetting::get('public_uploads_enabled', true),
            'maintenance_mode' => SiteSetting::get('maintenance_mode', false),
        ];

        // Recent Activity
        $recentReports = VideoReport::with(['video', 'reporter'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $recentVideos = Video::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $recentActivity = ActivityLog::with('user')->latest()->take(15)->get();

        return view('admin.dashboard', compact(
            'stats',
            'videoStats',
            'reportStats',
            'reportsByReason',
            'siteSettings',
            'recentReports',
            'recentVideos',
            'recentActivity'
        ));
    }

    /**
     * Get count of users online in the last 5 minutes
     */
    private function getOnlineUsersCount(): int
    {
        return Cache::remember('online_users_count', 60, function () {
            return User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
        });
    }

    /**
     * Toggle site setting (Secure Implementation)
     * Protected against CSRF, SQL Injection, XSS, and unauthorized access
     */
    public function toggleSetting(Request $request)
    {
        // 1. Authorization Check - Ensure user is admin
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acesso negado. Apenas administradores podem alterar configurações.');
        }

        // 2. Validate and Sanitize Input
        $validated = $request->validate([
            'key' => ['required', 'string', 'in:public_uploads_enabled,maintenance_mode'],
            'value' => ['required', 'boolean'],
        ]);

        // 3. Whitelist allowed settings (defense in depth)
        $allowedSettings = ['public_uploads_enabled', 'maintenance_mode'];
        if (!in_array($validated['key'], $allowedSettings, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Configuração inválida.',
            ], 400);
        }

        // 4. Type casting for extra safety
        $settingKey = (string) $validated['key'];
        $settingValue = (bool) $validated['value'];

        try {
            // 5. Update setting using model method (prevents SQL injection)
            SiteSetting::set($settingKey, $settingValue, 'boolean');

            // 6. Log the change with sanitized data
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'site_setting_changed',
                'description' => htmlspecialchars(
                    "Changed {$settingKey} to " . ($settingValue ? 'enabled' : 'disabled'),
                    ENT_QUOTES,
                    'UTF-8'
                ),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // 7. Clear relevant caches
            Cache::forget('site_settings');
            if ($settingKey === 'maintenance_mode') {
                Cache::forget('maintenance_mode');
            }

            // 8. Prepare safe response message
            $message = match($settingKey) {
                'public_uploads_enabled' => $settingValue 
                    ? 'Uploads públicos foram habilitados' 
                    : 'Uploads públicos foram desabilitados. Apenas admins podem fazer upload.',
                'maintenance_mode' => $settingValue 
                    ? 'Modo de manutenção ativado. Site restrito a admins.' 
                    : 'Modo de manutenção desativado. Site público novamente.',
                default => 'Configuração atualizada com sucesso.',
            };

            return response()->json([
                'success' => true,
                'message' => $message,
                'setting' => [
                    'key' => $settingKey,
                    'value' => $settingValue,
                ],
            ]);

        } catch (\Exception $e) {
            // 9. Log error without exposing sensitive information
            \Log::error('Failed to toggle site setting', [
                'user_id' => auth()->id(),
                'key' => $settingKey,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar configuração. Tente novamente.',
            ], 500);
        }
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
        
        $stats = [
            'total_revenue' => Purchase::where('status', 'completed')->sum('amount_paid'),
            'completed_purchases' => Purchase::where('status', 'completed')->count(),
            'pending_purchases' => Purchase::where('status', 'pending')->count(),
        ];

        return view('admin.purchases.index', compact('purchases', 'stats'));
    }

    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $activities = $query->latest()->paginate(50);

        return view('admin.activity.index', compact('activities'));
    }
}
