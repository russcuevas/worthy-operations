<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->to(self::getDashboardUrl(Auth::user()))->with('error', 'Access denied. You do not have 627 (Admin) privileges.');
        }

        return $next($request);
    }

    public static function getDashboardUrl($user): string
    {
        return match($user->role) {
            'admin' => route('admin.dashboard'),
            'hq' => route('hq.dashboard'),
            'cluster_head' => route('cluster_head.dashboard'),
            'top_coor' => route('top_coor.dashboard'),
            'registry_officer' => route('registry_officer.dashboard'),
            default => route('login'),
        };
    }
}
