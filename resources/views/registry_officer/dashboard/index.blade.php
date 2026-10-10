@extends('layouts.app')

@section('title', 'Registry Officer Dashboard')
@section('page-title', 'My Operations Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::user()->name . '! View your assigned duties, instructions, and upload proof photos')

@section('content')
    <!-- Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="metric-card">
            <div class="metric-info">
                <span class="metric-label">All My Assigned Tasks</span>
                <span class="metric-value">{{ $totalTasks }}</span>
            </div>
            <div class="metric-icon-box">
                <i class="bi bi-list-check"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #0284C7;">
            <div class="metric-info">
                <span class="metric-label" style="color: #0284C7;">In Progress</span>
                <span class="metric-value" style="color: #0284C7;">{{ $inProgressTasks }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #E0F2FE; --metric-color: #0284C7;">
                <i class="bi bi-play-circle-fill"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #64748B;">
            <div class="metric-info">
                <span class="metric-label" style="color: #64748B;">Not Started</span>
                <span class="metric-value" style="color: #64748B;">{{ $notStartedTasks }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #F1F5F9; --metric-color: #64748B;">
                <i class="bi bi-hourglass-top"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #10B981;">
            <div class="metric-info">
                <span class="metric-label" style="color: #166534;">Completed Tasks</span>
                <span class="metric-value" style="color: #166534;">{{ $completedTasks }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #D1FAE5; --metric-color: #166534;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Tasks List -->
        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-clipboard-check text-primary"></i>
                    <span>Upcoming & Active Assignments</span>
                </div>
                <a href="{{ route('registry_officer.tasks.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">View All My Tasks</a>
            </div>
            <div class="wa-card-body" style="padding: 16px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($recentTasks as $task)
                        <div style="background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                            <div style="flex: 1; min-width: 240px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <code>{{ $task->task_code }}</code>
                                    <span class="badge {{ $task->priority_badge }}">{{ ucfirst($task->priority) }}</span>
                                    <span class="badge {{ $task->status_badge }}">{{ $task->status_label }}</span>
                                </div>
                                <h4 style="font-size: 14.5px; font-weight: 700; color: var(--color-deep-navy); margin-bottom: 4px;">{{ $task->title }}</h4>
                                <div style="font-size: 11.5px; color: var(--text-muted);">
                                    <strong>Order From:</strong> {{ $task->assignedBy->name }} ({{ $task->assignedBy->role_label }}) &bull;
                                    <strong>Due:</strong> {{ $task->due_datetime->format('M d, Y h:i A') }}
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('registry_officer.tasks.index') }}" class="btn-wa btn-wa-primary btn-wa-sm">Open Task</a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 24px; color: var(--text-light);">No tasks currently assigned to you.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Links & Alerts -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="wa-card-title">
                        <i class="bi bi-calendar-event text-primary"></i>
                        <span>Schedule Quick Link</span>
                    </div>
                </div>
                <div class="wa-card-body" style="padding: 16px; display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('registry_officer.schedule.index') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                        <i class="bi bi-calendar-check-fill"></i> View My Task Calendar
                    </a>
                </div>
            </div>

            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="wa-card-title">
                        <i class="bi bi-bell-fill text-primary"></i>
                        <span>Alerts & Notifications</span>
                    </div>
                </div>
                <div class="wa-card-body" style="padding: 14px 16px;">
                    @forelse($recentNotifications as $notif)
                        <div style="padding: 8px 0; border-bottom: 1px solid #F1F5F9; font-size: 12.5px;">
                            <div style="font-weight: 700; color: var(--color-deep-navy);">{{ $notif->title }}</div>
                            <div style="color: var(--text-muted); margin-top: 2px;">{{ $notif->message }}</div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-light); padding: 14px;">No new alerts.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
