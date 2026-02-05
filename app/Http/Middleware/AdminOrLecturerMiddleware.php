<?php
// app/Http/Middleware/AdminOrLecturerMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrLecturerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if ($user->hasRole('admin') || $user->hasRole('lecturer')) {
            return $next($request);
        }
        
        abort(403, 'Unauthorized access.');
    }
}