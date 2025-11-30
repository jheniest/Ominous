<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     * 
     * During maintenance mode:
     * - Admins can access everything (full bypass)
     * - Non-admin users are redirected to maintenance page
     * - Login page is accessible to everyone
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get maintenance mode status
        $maintenanceMode = SiteSetting::get('maintenance_mode', false);

        // If not in maintenance mode, proceed normally
        if (!$maintenanceMode) {
            return $next($request);
        }

        // Admins always have full access during maintenance
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }

        // Whitelist routes that should work during maintenance (for login flow)
        $allowedRoutes = [
            'maintenance',
            'login',
            'logout',
        ];
        
        $currentRoute = $request->route()?->getName();
        
        // Allow whitelisted named routes
        if ($currentRoute && in_array($currentRoute, $allowedRoutes, true)) {
            return $next($request);
        }

        // Allow direct access to maintenance and login URLs
        if ($request->is('maintenance') || $request->is('login')) {
            return $next($request);
        }

        // If user is logged in but NOT admin, redirect to maintenance
        // This handles the case where a regular user logs in during maintenance
        if (auth()->check() && !auth()->user()->is_admin) {
            // Log them out and redirect to maintenance
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('maintenance')
                ->with('error', 'O site está em manutenção. Apenas administradores podem acessar.');
        }

        // Guest users go to maintenance page
        return redirect()->route('maintenance');
    }
}
