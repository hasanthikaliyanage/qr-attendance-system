<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LecturerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isLecturer()) {
            return redirect()->route(auth()->user()->getDashboardRoute())
                ->with('error', 'You do not have permission to access the lecturer area.');
        }

        return $next($request);
    }
}