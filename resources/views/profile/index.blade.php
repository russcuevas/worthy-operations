@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'Account Profile')
@section('page-subtitle', 'Manage your personal details, profile picture, and account security')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    @if(session('success'))
        <div class="alert alert-success" style="background: var(--success-bg); color: var(--success); border: 1px solid var(--success); border-radius: var(--radius-md); padding: 14px 18px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill" style="font-size: 18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger); border-radius: var(--radius-md); padding: 14px 18px; margin-bottom: 20px; font-weight: 500;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; margin-bottom: 6px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- User Profile Card -->
        <div class="wa-card mb-4" style="overflow: hidden;">
            <!-- Profile Banner -->
            <div style="background: linear-gradient(135deg, var(--color-deep-navy) 0%, var(--color-primary-blue) 100%); padding: 32px 28px 24px; color: #ffffff; position: relative;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 24px;">
                    <!-- Avatar with upload hover trigger -->
                    <div style="position: relative; width: 96px; height: 96px;">
                        <img id="profileAvatarPreview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                             style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 4px solid #ffffff; box-shadow: var(--shadow-md); background: #ffffff;">
                        <label for="avatarInput" style="position: absolute; bottom: 0; right: 0; background: var(--color-wave-cyan); color: #ffffff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.2);" title="Change Photo">
                            <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
                        </label>
                    </div>

                    <div style="flex: 1; min-width: 240px;">
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <h2 style="font-size: 22px; font-weight: 800; margin: 0; color: #ffffff;">{{ $user->name }}</h2>
                            <span class="badge" style="background: rgba(81, 184, 229, 0.25); color: #ffffff; border: 1px solid var(--color-wave-cyan); font-weight: 700; font-size: 11px; text-transform: uppercase; padding: 4px 10px; border-radius: 20px;">
                                {{ $user->role_label }}
                            </span>
                        </div>
                        <div style="font-size: 13.5px; opacity: 0.9; margin-top: 4px; display: flex; flex-wrap: wrap; gap: 14px;">
                            <span><i class="bi bi-person-fill me-1"></i> {{ '@' . $user->username }}</span>
                            @if($user->department)
                                <span><i class="bi bi-building me-1"></i> {{ $user->department }}</span>
                            @endif
                            @if($user->position)
                                <span><i class="bi bi-briefcase-fill me-1"></i> {{ $user->position }}</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; background: rgba(255,255,255,0.15); padding: 6px 14px; border-radius: 20px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                            Account Active
                        </span>
                    </div>
                </div>
            </div>

            <div class="wa-card-body" style="padding: 28px;">
                <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" style="display: none;">
                
                <h4 style="font-size: 15px; font-weight: 700; color: var(--color-deep-navy); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    <span>Personal & Contact Information</span>
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                    <div class="form-group">
                        <label for="name" class="form-label required">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $user->name) }}" placeholder="e.g. Worthy Acosta">
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" value="{{ $user->username }}" disabled style="background-color: var(--color-mist-blue); cursor: not-allowed; opacity: 0.85;">
                        <small style="font-size: 11px; color: var(--text-muted);">Username cannot be modified.</small>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="e.g. user@acostainventory.com">
                    </div>

                    <div class="form-group">
                        <label for="contact_number" class="form-label">Contact Number / Phone</label>
                        <input type="text" name="contact_number" id="contact_number" class="form-control" value="{{ old('contact_number', $user->contact_number) }}" placeholder="e.g. +63 912 345 6789">
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">System Role</label>
                        <input type="text" id="role" class="form-control" value="{{ $user->role_label }}" disabled style="background-color: var(--color-mist-blue); cursor: not-allowed; opacity: 0.85;">
                    </div>

                    <div class="form-group">
                        <label for="cluster" class="form-label">Assigned Cluster</label>
                        <input type="text" id="cluster" class="form-control" value="{{ $user->cluster ?? 'No Cluster Assigned' }}" disabled style="background-color: var(--color-mist-blue); cursor: not-allowed; opacity: 0.85;">
                    </div>

                    <div class="form-group">
                        <label for="position" class="form-label">Position / Title</label>
                        <input type="text" id="position" class="form-control" value="{{ $user->position ?? 'Not Assigned' }}" disabled style="background-color: var(--color-mist-blue); cursor: not-allowed; opacity: 0.85;">
                    </div>
                </div>

                <div style="margin-top: 10px;">
                    <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="document.getElementById('avatarInput').click();">
                        <i class="bi bi-image"></i> Select Profile Photo
                    </button>
                    <span id="fileNameDisplay" style="font-size: 12px; color: var(--text-muted); margin-left: 10px;">Max size: 3MB (JPG, PNG, WEBP)</span>
                </div>
            </div>
        </div>

        <!-- Password / Security Card -->
        <div class="wa-card mb-4">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-shield-lock-fill text-primary"></i>
                    <span>Account Security & Password</span>
                </div>
            </div>
            <div class="wa-card-body" style="padding: 24px;">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">
                    Leave password fields blank if you do not want to change your current password.
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Required to change password">
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" placeholder="Minimum 6 characters">
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Repeat new password">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
            <a href="{{ role_route('dashboard') }}" class="btn-wa btn-wa-outline">Cancel</a>
            <button type="submit" class="btn-wa btn-wa-primary" style="padding: 10px 24px; font-size: 14px; font-weight: 700;">
                <i class="bi bi-check2-circle me-1"></i> Save Changes
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            document.getElementById('fileNameDisplay').textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('profileAvatarPreview').src = evt.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
