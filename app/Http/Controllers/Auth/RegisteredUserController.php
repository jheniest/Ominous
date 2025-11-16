<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required', 'string'],
            'terms' => ['accepted'],
        ]);

        $validation = $this->inviteService->validateInviteCode($request->invite_code);

        if (!$validation['valid']) {
            return back()->withErrors(['invite_code' => $validation['message']]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $this->inviteService->redeemInvite($request->invite_code, $user);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->forget('validated_invite_code');

        return redirect()->route('videos.index')->with('success', 'Bem-vindo ao Ominous! Sua conta foi criada com sucesso.');
    }
}
