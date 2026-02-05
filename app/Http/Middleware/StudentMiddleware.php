<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isStudent()) {
            return redirect()->route(auth()->user()->getDashboardRoute())
                ->with('error', 'You do not have permission to access the student area.');
        }

        return $next($request);
    }
}