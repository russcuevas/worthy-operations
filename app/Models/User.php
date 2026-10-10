<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'cluster',
        'position',
        'department',
        'contact_number',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHQ(): bool
    {
        return $this->role === 'hq';
    }

    public function isClusterHead(): bool
    {
        return $this->role === 'cluster_head';
    }

    public function isTopCoor(): bool
    {
        return $this->role === 'top_coor';
    }

    public function isRegistryOfficer(): bool
    {
        return $this->role === 'registry_officer';
    }

    public function hasAdminAccess(): bool
    {
        return $this->role === 'admin';
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => '627 (Admin)',
            'hq' => 'HQ',
            'cluster_head' => 'Cluster Head',
            'top_coor' => 'Top Coor',
            'registry_officer' => 'Registry Officer',
            default => ucfirst(str_replace('_', ' ', $this->role))
        };
    }

    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'admin' => 'badge-danger',
            'hq' => 'badge-primary',
            'cluster_head' => 'badge-info',
            'top_coor' => 'badge-warning',
            'registry_officer' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    public function assignedTasks()
    {
        return $this->hasMany(StaffTask::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(StaffTask::class, 'assigned_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class)->latest();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path($this->avatar))) {
            return asset($this->avatar);
        }
        $cleanName = trim(preg_replace('/\s*\(.*?\)\s*/', ' ', $this->name)) ?: $this->name;
        return 'https://ui-avatars.com/api/?name=' . urlencode($cleanName) . '&background=102A4E&color=ffffff&bold=true&size=128';
    }
}
