<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidNickname;
use App\Services\InviteService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    private InviteService $inviteService;

    public function __construct(InviteService $inviteService)
    {
        $this->inviteService = $inviteService;
    }

    public function create(Request $request)
    {
        $inviteCode = $request->session()->get('validated_invite_code');

        if (!$inviteCode) {
            return redirect()->route('invite.validate')->with('error', 'Você precisa de um convite válido.');
        }

        $validation = $this->inviteService->validateInviteCode($inviteCode);

        if (!$validation['valid']) {
            $request->session()->forget('validated_invite_code');
            return redirect()->route('invite.validate')->with('error', $validation['message']);
        }

        return view('auth.register', [
            'invite_code' => $inviteCode,
            'invited_by' => $validation['invite']->createdBy?->name ?? 'O Guardião',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', new ValidNickname()],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required', 'string'],
            'terms' => ['accepted'],
        ], [
            'nickname.required' => 'O nickname é obrigatório.',
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas não conferem.',
            'terms.accepted' => 'Você deve aceitar os termos.',
        ]);

        $validation = $this->inviteService->validateInviteCode($request->invite_code);

        if (!$validation['valid']) {
            return back()->withErrors(['invite_code' => $validation['message']]);
        }

        $user = User::create([
            'name' => $request->name,
            'nickname' => strtolower(ltrim($request->nickname, '@')),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $this->inviteService->redeemInvite($request->invite_code, $user);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->forget('validated_invite_code');

        return redirect()->route('news.index')->with('success', 'Bem-vindo ao Atrocidades! Sua conta foi criada com sucesso.');
    }
    
    /**
     * Check if a nickname is available (AJAX endpoint).
     */
    public function checkNickname(Request $request)
    {
        $nickname = strtolower(ltrim($request->input('nickname', ''), '@'));
        
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
        
        $exists = User::where('nickname', $nickname)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Nickname já em uso' : 'Disponível!'
        ]);
    }
}
