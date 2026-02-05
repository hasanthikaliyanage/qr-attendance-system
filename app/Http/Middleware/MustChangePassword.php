<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MustChangePassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return $next($request);
        }
        
        // Admin doesn't need to change password (unless you want to enforce)
        if ($user->role === 'admin') {
            return $next($request);
        }
        
        // Check if user must change password
        if ($user->must_change_password == 1) {
            // Allow access to password change page and logout
            if (!$request->is('change-password') && !$request->is('logout')) {
                return redirect()->route('password.change');
            }
        }
        
        return $next($request);
    }
}