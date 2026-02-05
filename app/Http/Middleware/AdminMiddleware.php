<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin()) {
            // Redirect to their own dashboard
            return redirect()->route(auth()->user()->getDashboardRoute())
                ->with('error', 'You do not have permission to access the admin area.');
        }

        return $next($request);
    }
}