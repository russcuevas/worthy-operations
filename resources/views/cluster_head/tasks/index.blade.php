@extends('layouts.app')

@section('title', 'Cluster Tasks Management')
@section('page-title', 'Cluster Task Assignments & Operations')
@section('page-subtitle', 'Assign tasks to Top Coordinators and Registry Officers; track real-time accomplishment and uploaded photo evidence')

@section('content')
    <!-- Metric Tabs -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px;">
        <a href="{{ route('cluster_head.tasks.index') }}" style="text-decoration: none;">
            <div class="metric-card" style="padding: 14px 18px; {{ !request()->has('status') && !request()->has('filter_overdue') ? 'border-color: var(--color-primary-blue); background: var(--color-mist-blue);' : '' }}">
                <div class="metric-info">
                    <span class="metric-label" style="font-size: 11px;">All Cluster Tasks</span>
                    <span class="metric-value" style="font-size: 20px;">{{ $counts['all'] }}</span>
                </div>
                <div class="metric-icon-box" style="width: 38px; height: 38px; font-size: 18px;">
                    <i class="bi bi-list-task"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('cluster_head.tasks.index', ['status' => 'in_progress']) }}" style="text-decoration: none;">
            <div class="metric-card" style="padding: 14px 18px; --card-accent: #0284C7; {{ request('status') === 'in_progress' ? 'border-color: #0284C7; background: #F0F9FF;' : '' }}">
                <div class="metric-info">
                    <span class="metric-label" style="font-size: 11px; color: #0284C7;">In Progress</span>
                    <span class="metric-value" style="font-size: 20px; color: #0284C7;">{{ $counts['in_progress'] }}</span>
                </div>
                <div class="metric-icon-box" style="width: 38px; height: 38px; font-size: 18px; --metric-bg: #E0F2FE; --metric-color: #0284C7;">
                    <i class="bi bi-play-circle"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('cluster_head.tasks.index', ['status' => 'not_started']) }}" style="text-decoration: none;">
            <div class="metric-card" style="padding: 14px 18px; --card-accent: #64748B; {{ request('status') === 'not_started' ? 'border-color: #64748B; background: #F8FAFC;' : '' }}">
                <div class="metric-info">
                    <span class="metric-label" style="font-size: 11px; color: #64748B;">Not Started</span>
                    <span class="metric-value" style="font-size: 20px; color: #64748B;">{{ $counts['not_started'] }}</span>
                </div>
                <div class="metric-icon-box" style="width: 38px; height: 38px; font-size: 18px; --metric-bg: #F1F5F9; --metric-color: #64748B;">
                    <i class="bi bi-hourglass-top"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('cluster_head.tasks.index', ['status' => 'completed']) }}" style="text-decoration: none;">
            <div class="metric-card" style="padding: 14px 18px; --card-accent: #10B981; {{ request('status') === 'completed' ? 'border-color: #10B981; background: #ECFDF5;' : '' }}">
                <div class="metric-info">
                    <span class="metric-label" style="font-size: 11px; color: #166534;">Completed</span>
                    <span class="metric-value" style="font-size: 20px; color: #166534;">{{ $counts['completed'] }}</span>
                </div>
                <div class="metric-icon-box" style="width: 38px; height: 38px; font-size: 18px; --metric-bg: #D1FAE5; --metric-color: #166534;">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('cluster_head.tasks.index', ['filter_overdue' => '1']) }}" style="text-decoration: none;">
            <div class="metric-card" style="padding: 14px 18px; --card-accent: #EF4444; {{ request('filter_overdue') === '1' ? 'border-color: #EF4444; background: #FEF2F2;' : '' }}">
                <div class="metric-info">
                    <span class="metric-label" style="font-size: 11px; color: #DC2626;">Overdue</span>
                    <span class="metric-value" style="font-size: 20px; color: #DC2626;">{{ $counts['overdue'] }}</span>
                </div>
                <div class="metric-icon-box" style="width: 38px; height: 38px; font-size: 18px; --metric-bg: #FEE2E2; --metric-color: #DC2626;">
                    <i class="bi bi-exclamation-octagon"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Filter and Action Bar -->
    <div class="wa-card">
        <div class="wa-card-body" style="padding: 16px 20px;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
                <form action="{{ route('cluster_head.tasks.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; flex: 1;">
                    <div style="min-width: 180px;">
                        <select name="assigned_to" class="form-select" onchange="this.form.submit()">
                            <option value="">Filter Assignee (All)</option>
                            @foreach($assignableUsers as $u)
                                <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->role_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="min-width: 140px;">
                        <select name="priority" class="form-select" onchange="this.form.submit()">
                            <option value="">All Priorities</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                            <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>🔵 Normal</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>⚪ Low</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['assigned_to', 'priority', 'status', 'filter_overdue']))
                        <a href="{{ route('cluster_head.tasks.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('cluster_head.tasks.calendar') }}" class="btn-wa btn-wa-outline">
                        <i class="bi bi-calendar-week"></i>
                        <span>Calendar</span>
                    </a>
                    <button type="button" class="btn-wa btn-wa-primary" data-modal-open="createClusterTaskModal">
                        <i class="bi bi-plus-lg"></i>
                        <span>Assign Task to Team</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks List Table -->
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-check2-square text-primary"></i>
                <span>Cluster Tasks Directory ({{ $tasks->count() }} Tasks)</span>
            </div>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Code & Priority</th>
                            <th>Task Title & Location</th>
                            <th>Order Given By (Tagged)</th>
                            <th>Assigned To</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Evidence</th>
                            <th style="text-align: right; width: 90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; font-size: 13px; color: var(--color-deep-navy);"><code>{{ $task->task_code }}</code></div>
                                    <span class="badge {{ $task->priority_badge }}" style="margin-top: 3px;">{{ ucfirst($task->priority) }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 14px; color: var(--color-deep-navy);">{{ $task->title }}</div>
                                    @if($task->work_location)
                                        <div style="font-size: 11.5px; color: #DC2626; font-weight: 600; margin-top: 3px;">
                                            <i class="bi bi-geo-alt-fill"></i> {{ $task->work_location }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 13px;">{{ $task->assignedBy->name }}</div>
                                    <span class="badge {{ $task->assignedBy->role_badge }}" style="font-size: 10px;">{{ $task->assignedBy->role_label }}</span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <img src="{{ $task->assignedTo->avatar_url }}" alt="{{ $task->assignedTo->name }}" class="user-avatar-sm" style="width: 28px; height: 28px;">
                                        <div>
                                            <div style="font-weight: 600; font-size: 13px;">{{ $task->assignedTo->name }}</div>
                                            <div style="font-size: 10.5px; color: var(--text-light);">{{ $task->assignedTo->role_label }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 12.5px; font-weight: 600; color: {{ $task->is_overdue ? '#DC2626' : 'var(--color-deep-navy)' }};">
                                        {{ $task->due_datetime->format('M d, Y h:i A') }}
                                    </div>
                                </td>
                                <td><span class="badge {{ $task->status_badge }}">{{ $task->status_label }}</span></td>
                                <td>
                                    @if(!empty($task->evidence_attachments))
                                        <span class="badge badge-success"><i class="bi bi-images"></i> {{ count($task->evidence_attachments) }} Photos</span>
                                    @else
                                        <span style="font-size: 11px; color: var(--text-light);">None</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="viewClusterTaskDetails({{ $task->id }})" title="View Details">
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

    <!-- Create Cluster Task Modal -->
    <div class="modal-backdrop-custom" id="createClusterTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-send-plus-fill text-primary"></i>
                    <span>Assign Task to Top Coordinator or Registry Officer</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="{{ route('cluster_head.tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="wa-modal-body">
                    <div class="form-group" style="background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 14px; margin-bottom: 16px;">
                        <label class="form-label required" style="font-weight: 700;">Assignment Mode</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 14px; margin-top: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="assign_mode" value="single" checked onchange="toggleClusterMode('single')">
                                <span>Specific Person</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="assign_mode" value="all_cluster_top_coor" onchange="toggleClusterMode('all_cluster_top_coor')">
                                <span>All Top Coordinators in Cluster</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="assign_mode" value="all_cluster_officers" onchange="toggleClusterMode('all_cluster_officers')">
                                <span>All Registry Officers in Cluster</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="clusterSingleUserGroup">
                        <label for="clu_assigned_to" class="form-label required">Select Personnel</label>
                        <select name="assigned_to" id="clu_assigned_to" class="form-select">
                            <option value="">-- Choose Top Coor or Registry Officer --</option>
                            @foreach($assignableUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->role_label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="clu_title" class="form-label required">Task Title / Mission</label>
                        <input type="text" name="title" id="clu_title" class="form-control" required placeholder="e.g. Coordinate with local barangay registry & submit validation photos">
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                        <div class="form-group">
                            <label for="clu_priority" class="form-label required">Priority Level</label>
                            <select name="priority" id="clu_priority" class="form-select" required>
                                <option value="low">Low Priority</option>
                                <option value="normal" selected>Normal Priority</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent Priority</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="clu_work_location" class="form-label">Location / Barangay</label>
                            <input type="text" name="work_location" id="clu_work_location" class="form-control" placeholder="e.g. Biaan, Mariveles">
                        </div>

                        <div class="form-group">
                            <label for="clu_due_datetime" class="form-label required">Deadline / Due Date</label>
                            <input type="datetime-local" name="due_datetime" id="clu_due_datetime" class="form-control" required value="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clu_instructions" class="form-label">Instructions & Special Notes</label>
                        <textarea name="instructions" id="clu_instructions" class="form-control" rows="3" placeholder="Provide clear task instructions..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="clu_attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" id="clu_attachment" class="form-control">
                    </div>
                </div>
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-primary">
                        <i class="bi bi-send-fill"></i> Issue Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal-backdrop-custom" id="viewClusterTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span id="clu_modal_title">Task Details</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <div class="wa-modal-body" id="clu_modal_body">
                <div style="text-align: center; padding: 30px;"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="wa-modal-footer">
                <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Close</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleClusterMode(mode) {
        if (mode === 'single') {
            document.getElementById('clusterSingleUserGroup').style.display = 'block';
            document.getElementById('clu_assigned_to').setAttribute('required', 'required');
        } else {
            document.getElementById('clusterSingleUserGroup').style.display = 'none';
            document.getElementById('clu_assigned_to').removeAttribute('required');
        }
    }

    function viewClusterTaskDetails(taskId) {
        openModal('viewClusterTaskModal');
        const body = document.getElementById('clu_modal_body');
        body.innerHTML = '<div style="text-align: center; padding: 30px;"><div class="spinner-border text-primary"></div></div>';

        fetch(`/cluster-head/tasks/${taskId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const t = data.task;
                    document.getElementById('clu_modal_title').innerText = `${t.task_code} - ${t.title}`;

                    let photosHtml = '';
                    if (data.evidence_attachments && data.evidence_attachments.length > 0) {
                        photosHtml = `
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
                    }

                    body.innerHTML = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; font-size: 13px;">
                            <div><strong>Order Given By (Tagged):</strong> <span class="badge badge-primary">${data.assigned_by_name}</span></div>
                            <div><strong>Assigned To:</strong> <span class="badge badge-info">${data.assigned_to_name}</span></div>
                            <div><strong>Cluster:</strong> ${data.cluster}</div>
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
                                <strong style="color: #166534;"><i class="bi bi-check-circle-fill"></i> Remarks by Assignee:</strong>
                                <div style="color: #166534; margin-top: 4px;">${t.completion_remarks}</div>
                            </div>
                        ` : ''}

                        ${photosHtml}
                    `;
                }
            });
    }
</script>
@endpush
