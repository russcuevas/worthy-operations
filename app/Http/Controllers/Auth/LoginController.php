<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->redirectToUserDashboard(Auth::user()));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authData = [
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($authData, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Your account has been deactivated. Please contact the administrator.',
                ])->onlyInput('login');
            }

            AuditLog::record('Auth', 'Login', "User {$user->name} logged in successfully.", ['username' => $user->username]);

            $targetUrl = $this->redirectToUserDashboard($user);
            return redirect()->intended($targetUrl)->with('success', "Welcome back, {$user->name}!");
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::record('Auth', 'Logout', 'User ' . Auth::user()->name . ' logged out.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }

    protected function redirectToUserDashboard($user): string
    {
        return match($user->role) {
            'admin' => route('admin.dashboard'),
            'hq' => route('hq.dashboard'),
            'cluster_head' => route('cluster_head.dashboard'),
            'top_coor' => route('top_coor.dashboard'),
            'registry_officer' => route('registry_officer.dashboard'),
            default => route('admin.dashboard'),
        };
    }
}
