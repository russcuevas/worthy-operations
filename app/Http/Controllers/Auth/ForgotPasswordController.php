<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form / request view.
     */
    public function showLinkRequestForm()
    {
        return view('auth.login', ['openForgotModal' => true]);
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
        ], [
            'email.required' => 'Please enter your account email address or username.',
        ]);

        $input = trim($request->input('email'));

        // Allow entering either email or username
        $user = User::where('email', $input)
            ->orWhere('username', $input)
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('forgot_error', 'We could not find an active account matching that email address or username.');
        }

        if (empty($user->email)) {
            return back()
                ->withInput()
                ->with('forgot_error', "The account '{$user->username}' does not have a registered email address. Please contact your system administrator.");
        }

        // Send reset link using Laravel Password broker
        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            AuditLog::record(
                'Auth',
                'Password Reset Requested',
                "Password reset link requested for user {$user->username} ({$user->email}).",
                ['email' => $user->email],
                $user->id
            );

            return back()->with('forgot_success', "Password reset link has been dispatched to {$user->email}! Please check your inbox and spam folder.");
        }

        return back()
            ->withInput()
            ->with('forgot_error', __($status));
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                AuditLog::record(
                    'Auth',
                    'Password Reset Completed',
                    "User {$user->username} successfully reset their password via email verification token.",
                    ['email' => $user->email],
                    $user->id
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Your password has been successfully reset! You can now sign in with your new password.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
