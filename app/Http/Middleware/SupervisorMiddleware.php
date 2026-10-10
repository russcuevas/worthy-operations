<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupervisorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is deactivated. Please contact an administrator.');
        }

        if (!$user->isSupervisor() && !$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->route('staff.dashboard')->with('error', 'Access denied. You do not have supervisor privileges.');
        }

        return $next($request);
    }
}
