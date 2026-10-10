<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:3072'],
        ], [
            'current_password.current_password' => 'The current password provided is incorrect.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.min' => 'New password must be at least 6 characters.',
            'avatar.max' => 'Profile image cannot exceed 3MB in size.',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists on disk and is inside uploads/users
            if ($user->avatar && file_exists(public_path($user->avatar)) && str_starts_with($user->avatar, 'uploads/users/')) {
                @unlink(public_path($user->avatar));
            }

            $file = $request->file('avatar');
            $filename = 'user_' . $user->id . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/users');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);
            $user->avatar = 'uploads/users/' . $filename;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'] ?? null;
        $user->contact_number = $validated['contact_number'] ?? null;

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        AuditLog::record('Profile', 'Profile Update', "User {$user->name} ({$user->role}) updated profile credentials and details.");

        return redirect()->route('profile.index')->with('success', 'Profile and account settings updated successfully!');
    }
}
