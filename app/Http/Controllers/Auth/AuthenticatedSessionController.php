<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('As credenciais fornecidas não correspondem aos nossos registros.'),
            ]);
        }

        $request->session()->regenerate();

        // Check if maintenance mode is active and user is not admin
        $maintenanceMode = \Cache::remember('maintenance_mode_status', 300, function () {
            return SiteSetting::get('maintenance_mode', false);
        });

        if ($maintenanceMode && !Auth::user()->is_admin) {
            // Log out the non-admin user during maintenance
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('maintenance')
                ->with('error', 'O site está em manutenção. Apenas administradores podem acessar no momento.');
        }

        return redirect()->intended(route('news.index'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('news.index');
    }
}
