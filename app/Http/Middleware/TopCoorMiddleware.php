<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TopCoorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!$user->isTopCoor() && !$user->isAdmin() && !$user->isHQ() && !$user->isClusterHead()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->to(AdminMiddleware::getDashboardUrl($user))->with('error', 'Access denied. You do not have Top Coordinator privileges.');
        }

        return $next($request);
    }
}
