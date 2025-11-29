<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Update last_seen_at every 5 minutes to avoid too many DB writes
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 5) {
                $user->update(['last_seen_at' => now()]);
            }
        }

        return $next($request);
    }
}
