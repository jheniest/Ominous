<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * Show the notification sending form.
     */
    public function index()
    {
        $users = User::where('is_suspended', false)
            ->orderBy('name')
            ->get(['id', 'name', 'nickname', 'email']);
            
        $recentNotifications = Notification::where('type', 'admin_message')
            ->with('user:id,name,nickname')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.notifications.index', compact('users', 'recentNotifications'));
    }

    /**
     * Send notification to user(s).
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipient' => ['required', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
            'nickname' => ['nullable', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ], [
            'recipient.required' => 'Selecione um destinatário.',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título deve ter no máximo 255 caracteres.',
            'message.required' => 'A mensagem é obrigatória.',
            'message.max' => 'A mensagem deve ter no máximo 1000 caracteres.',
        ]);

        $adminId = Auth::id();
        $count = 0;
        $targetDescription = '';

        if ($validated['recipient'] === 'all') {
            // Send to all users
            $count = Notification::sendAdminMessage(
                $validated['title'],
                $validated['message'],
                null,
                $adminId
            );
            $targetDescription = "todos os usuários ({$count})";
        } elseif ($validated['recipient'] === 'specific' && !empty($validated['user_id'])) {
            // Send to specific user by ID
            $user = User::find($validated['user_id']);
            if ($user) {
                $count = Notification::sendAdminMessage(
                    $validated['title'],
                    $validated['message'],
                    $user->id,
                    $adminId
                );
                $targetDescription = "@{$user->nickname}";
            }
        } elseif ($validated['recipient'] === 'nickname' && !empty($validated['nickname'])) {
            // Send to user by nickname
            $nickname = strtolower(ltrim($validated['nickname'], '@'));
            $user = User::where('nickname', $nickname)->first();
            
            if (!$user) {
                return back()
                    ->withInput()
                    ->withErrors(['nickname' => 'Usuário @' . $nickname . ' não encontrado.']);
            }
            
            $count = Notification::sendAdminMessage(
                $validated['title'],
                $validated['message'],
                $user->id,
                $adminId
            );
            $targetDescription = "@{$user->nickname}";
        }

        if ($count > 0) {
            // Log the action
            ActivityLog::create([
                'user_id' => $adminId,
                'action' => 'admin_notification_sent',
                'description' => "Notificação enviada para {$targetDescription}: {$validated['title']}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', "Notificação enviada com sucesso para {$targetDescription}!");
        }

        return back()
            ->withInput()
            ->withErrors(['recipient' => 'Não foi possível enviar a notificação.']);
    }

    /**
     * Search users by nickname (AJAX).
     */
    public function searchUsers(Request $request)
    {
        $query = strtolower(ltrim($request->input('q', ''), '@'));
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('nickname', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->where('is_suspended', false)
            ->limit(10)
            ->get(['id', 'name', 'nickname', 'avatar']);

        return response()->json($users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ];
        }));
    }
}
