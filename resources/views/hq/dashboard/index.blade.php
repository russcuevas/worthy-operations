@extends('layouts.app')

@section('title', 'HQ Operations Dashboard')
@section('page-title', 'HQ Central Command Dashboard')
@section('page-subtitle', 'Monitor field operations, issue directives to Cluster Heads, Top Coordinators, and Registry Officers')

@section('content')
    <!-- Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="metric-card">
            <div class="metric-info">
                <span class="metric-label">HQ Orders Issued</span>
                <span class="metric-value">{{ $totalTasksCreated }}</span>
            </div>
            <div class="metric-icon-box">
                <i class="bi bi-send-check-fill"></i>
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

        <div class="metric-card" style="--card-accent: #8B5CF6;">
            <div class="metric-info">
                <span class="metric-label" style="color: #6D28D9;">Field Personnel Network</span>
                <span class="metric-value" style="color: #6D28D9;">{{ $subordinatesCount }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #EDE9FE; --metric-color: #6D28D9;">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <!-- HQ Directives Interactive DataTable -->
    <div class="wa-card" style="margin-bottom: 24px;">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Recent HQ Directives & Status</span>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('hq.tasks.calendar') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                    <i class="bi bi-calendar-event"></i> Calendar View
                </a>
                <a href="{{ route('hq.tasks.index') }}" class="btn-wa btn-wa-primary btn-wa-sm">
                    <i class="bi bi-plus-circle-fill"></i> Issue New Directives
                </a>
            </div>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Code & Priority</th>
                            <th>Directive Objective</th>
                            <th>Assigned Subordinate</th>
                            <th>Cluster</th>
                            <th>Location</th>
                            <th>Schedule Due</th>
                            <th>Status</th>
                            <th>Evidence</th>
                            <th style="text-align: right; width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTasks as $task)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; font-size: 13px; color: var(--color-deep-navy);"><code>{{ $task->task_code }}</code></div>
                                    <span class="badge {{ $task->priority_badge }}" style="margin-top: 3px; font-size: 10px;">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 13.5px; color: var(--color-deep-navy);">{{ $task->title }}</div>
                                    @if($task->origin)
                                        <div style="font-size: 11px; color: var(--color-primary-blue); font-weight: 600; margin-top: 2px;">
                                            <i class="bi bi-box-arrow-up-right"></i> {{ $task->origin }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <img src="{{ $task->assignedTo->avatar_url ?? asset('images/logo.png') }}" alt="{{ $task->assignedTo->name ?? 'Personnel' }}" class="user-avatar-sm" style="width: 26px; height: 26px;">
                                        <div>
                                            <div style="font-weight: 600; font-size: 12.5px; color: var(--color-deep-navy);">{{ $task->assignedTo->name ?? 'Unassigned' }}</div>
                                            <div style="font-size: 10px; color: var(--text-light);">{{ $task->assignedTo->role_label ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($task->cluster)
                                        <span class="badge badge-info" style="font-size: 10.5px;">{{ $task->cluster }}</span>
                                    @else
                                        <span style="font-size: 11px; color: var(--text-light);">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->work_location)
                                        <span style="color: #DC2626; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-geo-alt-fill"></i> {{ $task->work_location }}
                                        </span>
                                    @else
                                        <span style="color: var(--text-light); font-size: 11px;">General</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 12px; font-weight: 600; color: {{ $task->is_overdue ? '#DC2626' : 'var(--color-deep-navy)' }};">
                                        {{ $task->due_datetime ? $task->due_datetime->format('M d, Y h:i A') : 'N/A' }}
                                    </div>
                                    @if($task->is_overdue)
                                        <span class="badge badge-danger" style="font-size: 9.5px;">Overdue</span>
                                    @elseif($task->due_datetime)
                                        <div style="font-size: 10.5px; color: var(--text-light);">{{ $task->due_datetime->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $task->status_badge }}">
                                        {{ $task->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($task->evidence_attachments))
                                        <span class="badge badge-success" style="font-size: 10px;">
                                            <i class="bi bi-images"></i> {{ count($task->evidence_attachments) }} Photos
                                        </span>
                                    @else
                                        <span style="font-size: 11px; color: var(--text-light);">None</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="viewHqTaskDetails({{ $task->id }})" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Notifications Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    <span>HQ Quick Commands</span>
                </div>
            </div>
            <div class="wa-card-body" style="padding: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <a href="{{ route('hq.tasks.index') }}" class="btn-wa btn-wa-primary" style="justify-content: center;">
                    <i class="bi bi-plus-circle-fill"></i> Issue Directives to Field
                </a>
                <a href="{{ route('hq.tasks.calendar') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                    <i class="bi bi-calendar-event"></i> HQ Operations Calendar
                </a>
                <a href="{{ route('hq.directory.index') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                    <i class="bi bi-person-lines-fill"></i> Field Personnel Directory
                </a>
                <a href="{{ route('hq.reports.index') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                    <i class="bi bi-file-earmark-bar-graph"></i> Accomplishment Reports
                </a>
            </div>
        </div>

        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-bell-fill text-primary"></i>
                    <span>Recent Notifications</span>
                </div>
            </div>
            <div class="wa-card-body" style="padding: 14px 16px;">
                @forelse($recentNotifications as $notif)
                    <div style="padding: 8px 0; border-bottom: 1px solid #F1F5F9; font-size: 12px;">
                        <div style="font-weight: 700; color: var(--color-deep-navy);">{{ $notif->title }}</div>
                        <div style="color: var(--text-muted); margin-top: 2px;">{{ $notif->message }}</div>
                        <div style="font-size: 10.5px; color: var(--text-light); margin-top: 2px;">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-light); padding: 14px;">No new alerts.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- View Task Details Modal -->
    <div class="modal-backdrop-custom" id="viewHqDashboardTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span id="hq_db_modal_title">Task Details</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <div class="wa-modal-body" id="hq_db_modal_body">
                <div style="text-align: center; padding: 30px;">
                    <div class="spinner-border text-primary"></div>
                    <div style="margin-top: 10px; color: var(--text-light);">Loading task data...</div>
                </div>
            </div>
            <div class="wa-modal-footer">
                <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Close</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function viewHqTaskDetails(taskId) {
        const modalBody = document.getElementById('hq_db_modal_body');
        modalBody.innerHTML = '<div style="text-align: center; padding: 30px;"><div class="spinner-border text-primary"></div><div style="margin-top: 10px; color: var(--text-light);">Loading task data...</div></div>';
        openModal('viewHqDashboardTaskModal');

        fetch(`/hq/tasks/${taskId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const t = data.task;
                    document.getElementById('hq_db_modal_title').innerText = `${t.task_code} - ${t.title}`;

                    let evidenceHtml = '';
                    if (data.evidence_attachments && data.evidence_attachments.length > 0) {
                        evidenceHtml = `
                            <div style="margin-top: 16px;">
                                <h4 style="font-size: 14px; font-weight: 700; color: var(--color-deep-navy); margin-bottom: 10px;">
                                    <i class="bi bi-images text-success"></i> Uploaded Evidence & Photos (${data.evidence_attachments.length})
                                </h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px;">
                                    ${data.evidence_attachments.map(ev => `
                                        <div style="border: 1px solid var(--card-border); border-radius: var(--radius-sm); overflow: hidden; background: #fff;">
                                            <a href="${ev.url}" target="_blank">
                                                <img src="${ev.url}" alt="${ev.name}" style="width: 100%; height: 130px; object-fit: cover; display: block;" onerror="this.src='{{ asset('images/logo.png') }}';">
                                            </a>
                                            <div style="padding: 6px 8px; font-size: 11px;">
                                                <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${ev.name}</div>
                                                <div style="color: var(--text-light); font-size: 10px;">${ev.time}</div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    let historyHtml = (data.histories || []).map(h => `
                        <div style="display: flex; gap: 12px; font-size: 12px; padding: 6px 0; border-bottom: 1px dashed #E2E8F0;">
                            <span style="color: var(--text-light); min-width: 120px;">${h.date}</span>
                            <strong style="color: var(--color-deep-navy);">${h.user}:</strong>
                            <span style="color: var(--color-primary-blue);">${h.action}</span>
                            <span style="color: var(--text-main); margin-left: auto;">${h.remarks || ''}</span>
                        </div>
                    `).join('');

                    modalBody.innerHTML = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; font-size: 13px;">
                            <div><strong>Assigned By (HQ):</strong> <span class="badge badge-primary">${data.assigned_by_name}</span></div>
                            <div><strong>Assigned Subordinate:</strong> <span class="badge badge-info">${data.assigned_to_name}</span></div>
                            <div><strong>Cluster:</strong> ${data.cluster}</div>
                            <div><strong>Location:</strong> ${data.work_location}</div>
                            <div><strong>Origin:</strong> ${data.origin}</div>
                            <div><strong>Status:</strong> <span class="badge ${t.status === 'completed' ? 'badge-success' : 'badge-info'}">${t.status.toUpperCase()}</span></div>
                            <div><strong>Due Date:</strong> ${data.due_formatted}</div>
                        </div>

                        ${t.instructions ? `
                            <div style="background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 12px; font-size: 13px; margin-bottom: 16px;">
                                <strong>Instructions:</strong>
                                <div>${t.instructions}</div>
                            </div>
                        ` : ''}

                        ${t.completion_remarks ? `
                            <div style="background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: var(--radius-sm); padding: 12px; font-size: 13px; margin-bottom: 16px;">
                                <strong style="color: #166534;"><i class="bi bi-check-circle-fill"></i> Field Accomplishment Remarks:</strong>
                                <div style="color: #166534; margin-top: 4px;">${t.completion_remarks}</div>
                            </div>
                        ` : ''}

                        ${evidenceHtml}

                        <div style="margin-top: 20px;">
                            <h4 style="font-size: 13px; font-weight: 700; color: var(--color-deep-navy); margin-bottom: 8px;">Activity Log</h4>
                            <div style="background: #FAFAFA; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 10px 14px;">
                                ${historyHtml || '<div style="color: var(--text-light); font-size: 12px;">No activity logged.</div>'}
                            </div>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = '<div style="color: var(--danger); text-align: center; padding: 20px;">Failed to load directive details.</div>';
            });
    }
</script>
@endpush
