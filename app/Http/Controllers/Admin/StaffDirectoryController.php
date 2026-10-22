<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['assignedTasks', 'createdTasks'])
            ->withCount(['assignedTasks as completed_tasks_count' => function($q) {
                $q->where('status', 'completed');
            }]);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->get();
        $clusters = User::whereNotNull('cluster')->where('cluster', '!=', '')->distinct()->pluck('cluster')->sort()->values();

        return view('admin.staff.index', compact('users', 'clusters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,hq,cluster_head,top_coor,registry_officer'],
            'cluster' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'user_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/users');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $avatarPath = 'uploads/users/' . $filename;
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => trim(strtolower($validated['username'])),
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'cluster' => $validated['cluster'] ?? null,
            'position' => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'avatar' => $avatarPath,
            'status' => $validated['status'],
        ]);

        AuditLog::record('Users', 'Create Staff Account', "Created user account for {$user->name} (@{$user->username}, {$user->role_label})");

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "User account for {$user->name} created successfully!"]);
        }

        return redirect()->route('admin.staff.index')->with('success', "User account for {$user->name} created successfully!");
    }

    public function show($id)
    {
        $user = User::withCount(['assignedTasks', 'createdTasks'])
            ->withCount(['assignedTasks as completed_tasks_count' => function($q) {
                $q->where('status', 'completed');
            }])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'user' => $user,
            'role_label' => $user->role_label,
            'avatar_url' => $user->avatar_url,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,hq,cluster_head,top_coor,registry_officer'],
            'cluster' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }
            $file = $request->file('avatar');
            $filename = 'user_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/users');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $validated['avatar'] = 'uploads/users/' . $filename;
        }

        $updateData = [
            'name' => $validated['name'],
            'username' => trim(strtolower($validated['username'])),
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'],
            'cluster' => $validated['cluster'] ?? null,
            'position' => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if (isset($validated['avatar'])) {
            $updateData['avatar'] = $validated['avatar'];
        }

        $user->update($updateData);

        AuditLog::record('Users', 'Update Staff Account', "Updated user account for {$user->name} (@{$user->username})");

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "User {$user->name} updated successfully!"]);
        }

        return redirect()->route('admin.staff.index')->with('success', "User {$user->name} updated successfully!");
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
            }
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete the only remaining Admin account.'], 403);
            }
            return redirect()->back()->with('error', 'Cannot delete the only remaining Admin account.');
        }

        $name = $user->name;
        $username = $user->username;
        $roleLabel = $user->role_label;

        if ($user->avatar && file_exists(public_path($user->avatar))) {
            @unlink(public_path($user->avatar));
        }

        $user->delete();

        AuditLog::record('Users', 'Delete Staff Account', "Deleted user account for {$name} (@{$username}, {$roleLabel})");

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "User account for {$name} was deleted successfully."]);
        }

        return redirect()->route('admin.staff.index')->with('success', "User account for {$name} was deleted successfully.");
    }
}
