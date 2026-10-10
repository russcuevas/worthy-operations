@extends('layouts.app')

@section('title', 'Top Coordinator Dashboard')
@section('page-title', 'Top Coordinator Command Dashboard')
@section('page-subtitle', 'Manage Registry Officers, delegate field tasks, and track tasks assigned to your desk')

@section('content')
    <!-- Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="metric-card">
            <div class="metric-info">
                <span class="metric-label">Tasks Given to Officers</span>
                <span class="metric-value">{{ $totalGivenTasks }}</span>
            </div>
            <div class="metric-icon-box">
                <i class="bi bi-send-check"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #10B981;">
            <div class="metric-info">
                <span class="metric-label" style="color: #166534;">Officers Completed Tasks</span>
                <span class="metric-value" style="color: #166534;">{{ $completedGivenTasks }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #D1FAE5; --metric-color: #166534;">
                <i class="bi bi-check-all"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #0284C7;">
            <div class="metric-info">
                <span class="metric-label" style="color: #0284C7;">My Assigned Tasks (from HQ/Admin)</span>
                <span class="metric-value" style="color: #0284C7;">{{ $totalMyTasks }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #E0F2FE; --metric-color: #0284C7;">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #8B5CF6;">
            <div class="metric-info">
                <span class="metric-label" style="color: #6D28D9;">Registry Officers Network</span>
                <span class="metric-value" style="color: #6D28D9;">{{ $officersCount }}</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #EDE9FE; --metric-color: #6D28D9;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <!-- Operations Tasks Interactive DataTable -->
    <div class="wa-card" style="margin-bottom: 24px;">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Operations Task Stream</span>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('top_coor.tasks.calendar') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                    <i class="bi bi-calendar-week"></i> Calendar
                </a>
                <a href="{{ route('top_coor.tasks.index') }}" class="btn-wa btn-wa-primary btn-wa-sm">
                    <i class="bi bi-send-plus-fill"></i> Tasks Hub & Delegate
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
                            <th>Order Flow (From &rarr; To)</th>
                            <th>Work Location</th>
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
                                    <div style="font-size: 11.5px; line-height: 1.4;">
                                        <div>
                                            <span style="color: var(--text-light); font-size: 10.5px;">From:</span>
                                            <strong>{{ $task->assignedBy->name ?? 'System' }}</strong>
                                            <span class="badge {{ $task->assignedBy->role_badge ?? 'badge-secondary' }}" style="font-size: 9px; padding: 1px 4px;">{{ $task->assignedBy->role_label ?? '' }}</span>
                                        </div>
                                        <div style="margin-top: 2px;">
                                            <span style="color: var(--text-light); font-size: 10.5px;">To:</span>
                                            <strong>{{ $task->assignedTo->name ?? 'Unassigned' }}</strong>
                                            <span class="badge {{ $task->assignedTo->role_badge ?? 'badge-secondary' }}" style="font-size: 9px; padding: 1px 4px;">{{ $task->assignedTo->role_label ?? '' }}</span>
                                        </div>
                                    </div>
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
                                    <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="viewTopCoorTaskDetails({{ $task->id }})" title="View Details">
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

    <!-- Quick Actions & Alerts Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    <span>Coordinator Fast Actions</span>
                </div>
            </div>
            <div class="wa-card-body" style="padding: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <a href="{{ route('top_coor.tasks.index') }}" class="btn-wa btn-wa-primary" style="justify-content: center;">
                    <i class="bi bi-plus-circle-fill"></i> Delegate to Officer
                </a>
                <a href="{{ route('top_coor.tasks.index', ['view' => 'assigned_to_me']) }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                    <i class="bi bi-check2-circle"></i> Tasks Assigned to Me ({{ $myPendingTasks }})
                </a>
                <a href="{{ route('top_coor.tasks.calendar') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                    <i class="bi bi-calendar-week"></i> Schedule Calendar
                </a>
                <a href="{{ route('top_coor.directory.index') }}" class="btn-wa btn-wa-outline" style="justify-content: center;">
                    <i class="bi bi-people"></i> Registry Officers Directory
                </a>
            </div>
        </div>

        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-bell-fill text-primary"></i>
                    <span>Recent Notifications & Alerts</span>
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

    <!-- View Task Modal -->
    <div class="modal-backdrop-custom" id="viewTopCoorDashboardTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span id="tc_db_modal_title">Task Details</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <div class="wa-modal-body" id="tc_db_modal_body">
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
    function viewTopCoorTaskDetails(taskId) {
        const modalBody = document.getElementById('tc_db_modal_body');
        modalBody.innerHTML = '<div style="text-align: center; padding: 30px;"><div class="spinner-border text-primary"></div><div style="margin-top: 10px; color: var(--text-light);">Loading task data...</div></div>';
        openModal('viewTopCoorDashboardTaskModal');

        fetch(`/top-coor/tasks/${taskId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const t = data.task;
                    document.getElementById('tc_db_modal_title').innerText = `${t.task_code} - ${t.title}`;

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
                            <div><strong>Assigned By:</strong> <span class="badge badge-primary">${data.assigned_by_name}</span></div>
                            <div><strong>Assigned To:</strong> <span class="badge badge-info">${data.assigned_to_name}</span></div>
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
                                <strong style="color: #166534;"><i class="bi bi-check-circle-fill"></i> Completion Remarks by Assignee:</strong>
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
                modalBody.innerHTML = '<div style="color: var(--danger); text-align: center; padding: 20px;">Failed to load task details.</div>';
            });
    }
</script>
@endpush
