<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_code',
        'title',
        'instructions',
        'assigned_by',
        'assigned_to',
        'cluster',
        'work_location',
        'origin',
        'priority',
        'status',
        'start_datetime',
        'due_datetime',
        'completion_remarks',
        'initial_attachment',
        'evidence_attachments',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'due_datetime' => 'datetime',
        'evidence_attachments' => 'array',
    ];

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class)->latest();
    }

    public function scopeForUser($query, $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }
        return $query->where('assigned_to', $user->id);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }
        return $this->due_datetime->isPast();
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'badge-danger',
            'high' => 'badge-warning',
            'normal' => 'badge-primary',
            'low' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'badge-secondary',
            'in_progress' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-dark',
            default => 'badge-secondary'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }
}
