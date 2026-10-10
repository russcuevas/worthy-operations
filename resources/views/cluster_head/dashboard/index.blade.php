@extends('layouts.app')

@section('title', 'Cluster Head Dashboard')
@section('page-title', 'Cluster Command Dashboard')
@section('page-subtitle', 'Manage operations for ' . (Auth::user()->cluster ?? 'Assigned Cluster') . ', assign tasks to Top Coordinators & Registry Officers')

@section('content')
    <!-- Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="metric-card">
            <div class="metric-info">
                <span class="metric-label">Cluster Tasks Managed</span>
                <span class="metric-value">{{ $totalTasks }}</span>
            </div>
            <div class="metric-icon-box">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #10B981;">
            <div class="metric-info">
                <span class="metric-label" style="color: #166534;">Completed Tasks</span>
                <span class="metric-value" style="color: #166534;">{{ $completedTasks }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #D1FAE5; --metric-color: #166534;">
                <i class="bi bi-check2-all"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #0284C7;">
            <div class="metric-info">
                <span class="metric-label" style="color: #0284C7;">Active In Progress</span>
                <span class="metric-value" style="color: #0284C7;">{{ $inProgressTasks }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #E0F2FE; --metric-color: #0284C7;">
                <i class="bi bi-play-circle"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #F59E0B;">
            <div class="metric-info">
                <span class="metric-label" style="color: #B45309;">Cluster Coordinators & Officers</span>
                <span class="metric-value" style="color: #B45309;">{{ $subordinatesCount }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #FEF3C7; --metric-color: #B45309;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Recent Tasks -->
        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-check2-square text-primary"></i>
                    <span>Recent Cluster Tasks</span>
                </div>
                <a href="{{ route('cluster_head.tasks.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">View All</a>
            </div>
            <div class="wa-card-body" style="padding: 16px;">
                <div class="table-responsive">
                    <table class="table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Task Objective</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTasks as $task)
                                <tr>
                                    <td><code>{{ $task->task_code }}</code></td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 13.5px; color: var(--color-deep-navy);">{{ $task->title }}</div>
                                        <div style="font-size: 11px; color: var(--text-light);">{{ $task->work_location ?? 'Cluster Area' }}</div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600;">{{ $task->assignedTo->name }}</span>
                                        <div style="font-size: 10.5px; color: var(--text-light);">{{ $task->assignedTo->role_label }}</div>
                                    </td>
                                    <td>{{ $task->due_datetime->format('M d, Y h:i A') }}</td>
                                    <td><span class="badge {{ $task->status_badge }}">{{ $task->status_label }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-light); padding: 24px;">No tasks assigned yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="wa-card-title">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        <span>Cluster Commands</span>
                    </div>
                </div>
                <div class="wa-card-body" style="padding: 16px; display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('cluster_head.tasks.index') }}" class="btn-wa btn-wa-primary" style="justify-content: center;">
                        <i class="bi bi-plus-circle-fill"></i> Assign Order to Team
                    </a>
                    <a href="{{ route('cluster_head.tasks.calendar') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                        <i class="bi bi-calendar-event"></i> Cluster Schedule Calendar
                    </a>
                    <a href="{{ route('cluster_head.directory.index') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                        <i class="bi bi-people"></i> Cluster Personnel List
                    </a>
                </div>
            </div>

            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="wa-card-title">
                        <i class="bi bi-bell-fill text-primary"></i>
                        <span>Notifications</span>
                    </div>
                </div>
                <div class="wa-card-body" style="padding: 14px 16px;">
                    @forelse($recentNotifications as $notif)
                        <div style="padding: 10px 0; border-bottom: 1px solid #F1F5F9; font-size: 12.5px;">
                            <div style="font-weight: 700; color: var(--color-deep-navy);">{{ $notif->title }}</div>
                            <div style="color: var(--text-muted); margin-top: 2px;">{{ $notif->message }}</div>
                            <div style="font-size: 10.5px; color: var(--text-light); margin-top: 3px;">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-light); padding: 14px;">No new alerts.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
