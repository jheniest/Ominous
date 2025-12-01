<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\ValidNickname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display user profile by nickname.
     */
    public function show(User $user)
    {
        $videos = $user->videos()
            ->when(!Auth::user()?->is_admin, function($query) {
                $query->where('status', 'approved');
            })
            ->withCount(['comments', 'reports'])
            ->latest()
            ->paginate(12);

        return view('profile.show', compact('user', 'videos'));
    }

    public function edit()
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    public function invites()
    {
        return view('profile.invites');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', new ValidNickname($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'nickname.required' => 'O nickname é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está em uso.',
        ]);

        // Ensure nickname is lowercase without @
        $validated['nickname'] = strtolower(ltrim($validated['nickname'], '@'));

        $user->update($validated);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto de perfil atualizada com sucesso.');
    }

    public function destroyAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Foto de perfil removida com sucesso.');
    }
    
    /**
     * Check if a nickname is available (AJAX endpoint).
     */
    public function checkNickname(Request $request)
    {
        $nickname = strtolower(ltrim($request->input('nickname', ''), '@'));
        $userId = Auth::id();
        
        if (strlen($nickname) < 3) {
            return response()->json([
                'available' => false,
                'message' => 'Mínimo 3 caracteres'
            ]);
        }
        
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $nickname)) {
            return response()->json([
                'available' => false,
                'message' => 'Apenas letras, números, _ e -'
            ]);
        }
        
        $reserved = ['admin', 'administrator', 'moderator', 'mod', 'root', 'system', 'support', 'help', 'atrocidades'];
        if (in_array($nickname, $reserved)) {
            return response()->json([
                'available' => false,
                'message' => 'Nickname reservado'
            ]);
        }
        
        $exists = User::where('nickname', $nickname)
            ->where('id', '!=', $userId)
            ->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Nickname já em uso' : 'Disponível!'
        ]);
    }
}
