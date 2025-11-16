<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_suspended) {
            auth()->logout();
            
            return redirect()->route('login')->with('error', 'Sua conta foi suspensa nas trevas.');
        }

        return $next($request);
    }
}
