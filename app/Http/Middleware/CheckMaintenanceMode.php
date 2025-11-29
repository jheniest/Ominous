<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request (Secure).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow admins to bypass maintenance mode
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }

        // Check if maintenance mode is enabled (with caching for performance)
        $maintenanceMode = \Cache::remember('maintenance_mode_status', 300, function () {
            return SiteSetting::get('maintenance_mode', false);
        });

        if ($maintenanceMode) {
            // Whitelist routes that should work during maintenance
            $allowedRoutes = [
                'maintenance',
                'logout',
                'login',
                'password.request',
                'password.email',
                'password.reset',
                'password.update',
            ];
            
            $currentRoute = $request->route()?->getName();
            
            // Allow whitelisted named routes
            if ($currentRoute && in_array($currentRoute, $allowedRoutes, true)) {
                return $next($request);
            }

            // Allow direct access to maintenance page URL
            if ($request->is('maintenance')) {
                return $next($request);
            }

            return redirect()->route('maintenance');
        }

        return $next($request);
    }
}
