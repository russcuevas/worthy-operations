@extends('layouts.app')

@section('title', '627 Admin Command Center')
@section('page-title', '627 Overview Dashboard')
@section('page-subtitle', 'Operations Command, Task Monitoring, Field Personnel, and Real-time Accomplishments')

@section('content')
    <!-- Top Statistics Metrics -->
    <div class="metrics-grid">
        <div class="metric-card" style="--card-accent: #075998;">
            <div class="metric-info">
                <span class="metric-label">Total Operations Tasks</span>
                <span class="metric-value">{{ number_format($totalTasks) }}</span>
                <span class="metric-sub">Issued Across All Roles</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #E9F5FC; --metric-color: #075998;">
                <i class="bi bi-list-task"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #10B981;">
            <div class="metric-info">
                <span class="metric-label" style="color: #166534;">Accomplished / Completed</span>
                <span class="metric-value" style="color: #166534;">{{ number_format($completedTasks) }}</span>
                <span class="metric-sub">With Submitted Proof Photos</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #ECFDF5; --metric-color: #10B981;">
                <i class="bi bi-check2-all"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #0284C7;">
            <div class="metric-info">
                <span class="metric-label" style="color: #0284C7;">Active In Progress</span>
                <span class="metric-value" style="color: #0284C7;">{{ number_format($inProgressTasks) }}</span>
                <span class="metric-sub">Ongoing Field Operations</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #EFF9FD; --metric-color: #0284C7;">
                <i class="bi bi-play-circle"></i>
            </div>
        </div>

        <div class="metric-card" style="--card-accent: #8B5CF6;">
            <div class="metric-info">
                <span class="metric-label" style="color: #6D28D9;">Field Personnel Network</span>
                <span class="metric-value" style="color: #6D28D9;">{{ number_format($totalUsers) }}</span>
                <span class="metric-sub">HQ, Cluster Heads, Coors & Officers</span>
            </div>
            <div class="metric-icon-box" style="--metric-bg: #EDE9FE; --metric-color: #6D28D9;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <!-- Personnel Breakdown & Cluster Stats Row -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        <!-- Hierarchy Breakdown -->
        <div class="wa-card" style="margin-bottom: 0;">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-diagram-3-fill text-primary"></i>
                    <span>Operations Hierarchy Breakdown</span>
                </div>
                <a href="{{ route('admin.staff.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">Manage Users</a>
            </div>
            <div class="wa-card-body" style="padding: 16px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; text-align: center;">
                    <div style="background: #F8FAFC; border: 1px solid var(--card-border); padding: 12px 8px; border-radius: 6px;">
                        <div style="font-size: 11px; color: var(--text-light); font-weight: 700;">627 ADMIN</div>
                        <div style="font-size: 22px; font-weight: 800; color: #DC2626;">{{ $roleBreakdown['admin'] }}</div>
                    </div>
                    <div style="background: #F8FAFC; border: 1px solid var(--card-border); padding: 12px 8px; border-radius: 6px;">
                        <div style="font-size: 11px; color: var(--text-light); font-weight: 700;">HQ COMMAND</div>
                        <div style="font-size: 22px; font-weight: 800; color: #1D4ED8;">{{ $roleBreakdown['hq'] }}</div>
                    </div>
                    <div style="background: #F8FAFC; border: 1px solid var(--card-border); padding: 12px 8px; border-radius: 6px;">
                        <div style="font-size: 11px; color: var(--text-light); font-weight: 700;">CLUSTER HEADS</div>
                        <div style="font-size: 22px; font-weight: 800; color: #0284C7;">{{ $roleBreakdown['cluster_head'] }}</div>
                    </div>
                    <div style="background: #F8FAFC; border: 1px solid var(--card-border); padding: 12px 8px; border-radius: 6px;">
                        <div style="font-size: 11px; color: var(--text-light); font-weight: 700;">TOP COORS</div>
                        <div style="font-size: 22px; font-weight: 800; color: #D97706;">{{ $roleBreakdown['top_coor'] }}</div>
                    </div>
                    <div style="background: #F8FAFC; border: 1px solid var(--card-border); padding: 12px 8px; border-radius: 6px;">
                        <div style="font-size: 11px; color: var(--text-light); font-weight: 700;">REGISTRY OFFICERS</div>
                        <div style="font-size: 22px; font-weight: 800; color: #166534;">{{ $roleBreakdown['registry_officer'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cluster Performance -->
        <div class="wa-card" style="margin-bottom: 0;">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span>Cluster Operations Progress</span>
                </div>
            </div>
            <div class="wa-card-body" style="padding: 16px;">
                @forelse($clusterStats as $stat)
                    @php
                        $percent = $stat->total > 0 ? round(($stat->completed / $stat->total) * 100) : 0;
                    @endphp
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12.5px; font-weight: 600; margin-bottom: 4px;">
                            <span>{{ $stat->cluster }}</span>
                            <span style="color: var(--color-primary-blue);">{{ $stat->completed }} / {{ $stat->total }} Tasks ({{ $percent }}%)</span>
                        </div>
                        <div style="background: #E2E8F0; border-radius: 10px; height: 8px; overflow: hidden;">
                            <div style="background: #10B981; height: 100%; width: {{ $percent }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-light); padding: 14px;">No cluster tasks assigned yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Tasks & Directives Interactive DataTable -->
    <div class="wa-card" style="margin-bottom: 24px;">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Recent Tasks & Directives</span>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.tasks.calendar') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                    <i class="bi bi-calendar-week"></i> Calendar
                </a>
                <a href="{{ route('admin.tasks.index') }}" class="btn-wa btn-wa-primary btn-wa-sm">
                    <i class="bi bi-send-plus-fill"></i> Manage & Assign Tasks
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
                            <th>Order Given By</th>
                            <th>Assigned To</th>
                            <th>Cluster</th>
                            <th>Due Schedule</th>
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
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 3px; font-size: 11px;">
                                        @if($task->work_location)
                                            <span style="color: #DC2626; font-weight: 600;">
                                                <i class="bi bi-geo-alt-fill"></i> {{ $task->work_location }}
                                            </span>
                                        @endif
                                        @if($task->origin)
                                            <span style="color: var(--color-primary-blue); font-weight: 600;">
                                                <i class="bi bi-box-arrow-up-right"></i> {{ $task->origin }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 12.5px; color: var(--color-deep-navy);">
                                        {{ $task->assignedBy->name ?? 'System' }}
                                    </div>
                                    <span class="badge {{ $task->assignedBy->role_badge ?? 'badge-secondary' }}" style="font-size: 9.5px;">
                                        {{ $task->assignedBy->role_label ?? 'Admin' }}
                                    </span>
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
                                    <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="viewTaskDetails({{ $task->id }})" title="View Details & Evidence">
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

    <!-- Recent Audit Trail Row -->
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-shield-check text-primary"></i>
                <span>Operations Activity & Audit Trail</span>
            </div>
            <a href="{{ route('admin.audit.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">All Logs</a>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
                @forelse($recentLogs as $log)
                    <div style="padding: 12px; background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); font-size: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span class="badge badge-secondary" style="font-size: 9.5px;">{{ $log->module }}</span>
                            <span style="color: var(--text-light); font-size: 10.5px;">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-weight: 700; color: var(--color-deep-navy);">{{ $log->action }}</div>
                        <div style="color: var(--text-muted); font-size: 11px; margin-top: 2px;">{{ Str::limit($log->description, 80) }}</div>
                        @if($log->user)
                            <div style="margin-top: 6px; font-size: 10.5px; color: var(--color-primary-blue); font-weight: 600;">
                                <i class="bi bi-person"></i> {{ $log->user->name }} ({{ $log->user->role_label }})
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-light); padding: 14px; grid-column: 1 / -1;">No activity logged.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- View Task Details Modal -->
    <div class="modal-backdrop-custom" id="viewTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span id="detail_modal_title">Task Details</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <div class="wa-modal-body" id="detail_modal_body">
                <div style="text-align: center; padding: 30px;">
                    <div class="spinner-border text-primary" role="status"></div>
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
    function viewTaskDetails(taskId) {
        const modal = document.getElementById('viewTaskModal');
        const modalBody = document.getElementById('detail_modal_body');
        modalBody.innerHTML = '<div style="text-align: center; padding: 30px;"><div class="spinner-border text-primary"></div><div style="margin-top: 10px; color: var(--text-light);">Loading task data...</div></div>';
        openModal('viewTaskModal');

        fetch(`/admin/tasks/${taskId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const t = data.task;
                    document.getElementById('detail_modal_title').innerText = `${t.task_code} - ${t.title}`;

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
                                                <img src="${ev.url}" alt="${ev.name}" style="width: 100%; height: 140px; object-fit: cover; display: block;" onerror="this.src='{{ asset('images/logo.png') }}';">
                                            </a>
                                            <div style="padding: 8px 10px; font-size: 11px;">
                                                <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${ev.name}</div>
                                                <div style="color: var(--text-light); font-size: 10px;">${ev.time}</div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    } else {
                        evidenceHtml = `<div style="font-size: 12px; color: var(--text-light); margin-top: 10px;">No photo evidence uploaded yet.</div>`;
                    }

                    let historyHtml = data.histories.map(h => `
                        <div style="display: flex; gap: 12px; font-size: 12px; padding: 6px 0; border-bottom: 1px dashed #E2E8F0;">
                            <span style="color: var(--text-light); min-width: 120px;">${h.date}</span>
                            <strong style="color: var(--color-deep-navy);">${h.user}:</strong>
                            <span style="color: var(--color-primary-blue);">${h.action}</span>
                            <span style="color: var(--text-main); margin-left: auto;">${h.remarks || ''}</span>
                        </div>
                    `).join('');

                    modalBody.innerHTML = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; font-size: 13px;">
                            <div><strong>Assigned By (Tagged):</strong> <span class="badge badge-primary">${data.assigned_by_name}</span></div>
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
            });
    }
</script>
@endpush
