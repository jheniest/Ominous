<?php

namespace App\Http\Controllers;

use App\Services\InviteService;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    private InviteService $inviteService;

    public function __construct(InviteService $inviteService)
    {
        $this->inviteService = $inviteService;
    }

    public function showValidationForm()
    {
        return view('invite.validate');
    }

    public function validate(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $validation = $this->inviteService->validateInviteCode($request->code);

        if (!$validation['valid']) {
            return back()->with('error_type', $validation['error'])->with('error', $validation['message']);
        }

        $request->session()->put('validated_invite_code', $request->code);

        return redirect()->route('register')->with('success', 'Convite válido. Prossiga com o ritual.');
    }

    public function index()
    {
        $invites = auth()->user()->createdInvites()
            ->withCount('redemptions')
            ->latest()
            ->paginate(15);

        return view('dashboard.invites.index', compact('invites'));
    }

    public function create()
    {
        return view('dashboard.invites.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Limit non-admin users to 3 active invites
        if (!$user->is_admin) {
            $activeInvitesCount = $user->createdInvites()
                ->where('status', '!=', 'consumed')
                ->count();
            
            if ($activeInvitesCount >= 3) {
                return back()->with('error', 'Você atingiu o limite de 3 convites ativos. Delete um convite existente para criar um novo.');
            }
        }

        $request->validate([
            'max_uses' => ['required', 'integer', 'min:1', 'max:10'],
            'days_valid' => ['required', 'integer', 'min:1', 'max:365'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $invite = $this->inviteService->createInvite(
            auth()->user(),
            $request->max_uses,
            $request->days_valid
        );

        if ($request->notes) {
            $invite->update(['notes' => $request->notes]);
        }

        return back()->with('success', "Convite {$invite->code} criado com sucesso.");
    }

    public function destroy(Request $request, int $id)
    {
        $invite = auth()->user()->createdInvites()->findOrFail($id);

        $this->inviteService->suspendInvite($invite, 'Suspenso pelo criador');

        return back()->with('success', 'Convite suspenso.');
    }
}
