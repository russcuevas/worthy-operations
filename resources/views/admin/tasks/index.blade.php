@extends('layouts.app')

@section('title', '627 Task Operations')
@section('page-title', '627 Task Management & Bulk Operations')
@section('page-subtitle', 'Assign duties to individuals, top coordinators, or entire clusters; inspect proofs; no approval required')

@section('content')
    <!-- Metric Tabs -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px;">
        <a href="{{ route('admin.tasks.index') }}" style="text-decoration: none;">
            <div class="metric-card" style="padding: 14px 18px; {{ !request()->has('status') && !request()->has('filter_overdue') ? 'border-color: var(--color-primary-blue); background: var(--color-mist-blue);' : '' }}">
                <div class="metric-info">
                    <span class="metric-label" style="font-size: 11px;">All Tasks</span>
                    <span class="metric-value" style="font-size: 20px;">{{ $counts['all'] }}</span>
                </div>
                <div class="metric-icon-box" style="width: 38px; height: 38px; font-size: 18px;">
                    <i class="bi bi-list-task"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.tasks.index', ['status' => 'in_progress']) }}" style="text-decoration: none;">
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

        <a href="{{ route('admin.tasks.index', ['status' => 'not_started']) }}" style="text-decoration: none;">
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

        <a href="{{ route('admin.tasks.index', ['status' => 'completed']) }}" style="text-decoration: none;">
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

        <a href="{{ route('admin.tasks.index', ['filter_overdue' => '1']) }}" style="text-decoration: none;">
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

    <!-- Filter and Create Task Action Bar -->
    <div class="wa-card">
        <div class="wa-card-body" style="padding: 16px 20px;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
                <form action="{{ route('admin.tasks.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; flex: 1;">
                    <div style="min-width: 170px;">
                        <select name="assigned_to" class="form-select" onchange="this.form.submit()">
                            <option value="">Filter Assignee (All)</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ request('assigned_to') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->role_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="min-width: 160px;">
                        <select name="cluster" class="form-select" onchange="this.form.submit()">
                            <option value="">Filter Cluster (All)</option>
                            @foreach($clusters as $c)
                                <option value="{{ $c }}" {{ request('cluster') === $c ? 'selected' : '' }}>{{ $c }}</option>
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

                    @if(request()->hasAny(['assigned_to', 'cluster', 'priority', 'status', 'filter_overdue']))
                        <a href="{{ route('admin.tasks.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.tasks.calendar') }}" class="btn-wa btn-wa-outline">
                        <i class="bi bi-calendar-week"></i>
                        <span>Calendar View</span>
                    </a>
                    <button type="button" class="btn-wa btn-wa-primary" data-modal-open="createTaskModal">
                        <i class="bi bi-send-plus-fill"></i>
                        <span>Assign Task / Bulk Order</span>
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
                <span>All Operations Tasks ({{ $tasks->count() }} Tasks)</span>
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
                            <th>Cluster</th>
                            <th>Schedule Due</th>
                            <th>Status</th>
                            <th>Evidence</th>
                            <th style="text-align: right; width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; font-size: 13px; color: var(--color-deep-navy);"><code>{{ $task->task_code }}</code></div>
                                    <span class="badge {{ $task->priority_badge }}" style="margin-top: 3px;">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 14px; color: var(--color-deep-navy);">{{ $task->title }}</div>
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; font-size: 11.5px;">
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
                                    @if($task->instructions)
                                        <div style="font-size: 11px; color: var(--text-light); margin-top: 2px;">{{ Str::limit($task->instructions, 45) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 13px; color: var(--color-deep-navy);">
                                        {{ $task->assignedBy->name }}
                                    </div>
                                    <span class="badge {{ $task->assignedBy->role_badge }}" style="font-size: 10px;">
                                        {{ $task->assignedBy->role_label }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <img src="{{ $task->assignedTo->avatar_url }}" alt="{{ $task->assignedTo->name }}" class="user-avatar-sm" style="width: 28px; height: 28px;">
                                        <div>
                                            <div style="font-weight: 600; font-size: 13px; color: var(--color-deep-navy);">{{ $task->assignedTo->name }}</div>
                                            <div style="font-size: 10.5px; color: var(--text-light);">{{ $task->assignedTo->role_label }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($task->cluster)
                                        <span class="badge badge-info">{{ $task->cluster }}</span>
                                    @else
                                        <span style="font-size: 11px; color: var(--text-light);">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 12.5px; font-weight: 600; color: {{ $task->is_overdue ? '#DC2626' : 'var(--color-deep-navy)' }};">
                                        {{ $task->due_datetime->format('M d, Y h:i A') }}
                                    </div>
                                    @if($task->is_overdue)
                                        <span class="badge badge-danger" style="font-size: 10px;">Overdue</span>
                                    @else
                                        <div style="font-size: 11px; color: var(--text-light);">{{ $task->due_datetime->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $task->status_badge }}">
                                        {{ $task->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($task->evidence_attachments))
                                        <span class="badge badge-success">
                                            <i class="bi bi-images"></i> {{ count($task->evidence_attachments) }} Photos
                                        </span>
                                    @else
                                        <span style="font-size: 11.5px; color: var(--text-light);">None</span>
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

    <!-- ================= MODALS ================= -->

    <!-- 1. Create Task Modal (with Bulk Features) -->
    <div class="modal-backdrop-custom" id="createTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-send-plus-fill text-primary"></i>
                    <span>627 Admin Task Assignment & Bulk Orders</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data" id="createTaskForm">
                @csrf
                <div class="wa-modal-body">
                    <!-- Assignment Mode Selection -->
                    <div class="form-group" style="background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 14px; margin-bottom: 16px;">
                        <label class="form-label required" style="font-weight: 700;">Target Assignment Mode</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-top: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; padding: 10px; border: 1px solid var(--card-border); border-radius: var(--radius-sm);">
                                <input type="radio" name="assign_mode" value="single" checked onchange="toggleAssignMode('single')">
                                <span>👤 Specific Person</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; padding: 10px; border: 1px solid var(--card-border); border-radius: var(--radius-sm);">
                                <input type="radio" name="assign_mode" value="all_top_coor" onchange="toggleAssignMode('all_top_coor')">
                                <span>🌟 Lahat ng Top Coor (Bulk)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; padding: 10px; border: 1px solid var(--card-border); border-radius: var(--radius-sm);">
                                <input type="radio" name="assign_mode" value="single_cluster" onchange="toggleAssignMode('single_cluster')">
                                <span>📍 One Cluster Lang</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; background: #fff; padding: 10px; border: 1px solid var(--card-border); border-radius: var(--radius-sm);">
                                <input type="radio" name="assign_mode" value="all_clusters" onchange="toggleAssignMode('all_clusters')">
                                <span>🏢 Lahat ng Clusters (Bulk)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Single User Select -->
                    <div class="form-group" id="singleUserGroup">
                        <label for="task_assigned_to" class="form-label required">Assign To Specific Personnel</label>
                        <select name="assigned_to" id="task_assigned_to" class="form-select">
                            <option value="">-- Choose Personnel --</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} — {{ $staff->role_label }} {{ $staff->cluster ? '[' . $staff->cluster . ']' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Single Cluster Select -->
                    <div id="singleClusterGroup" style="display: none; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="task_target_cluster" class="form-label required">Choose Target Cluster</label>
                            <select name="target_cluster" id="task_target_cluster" class="form-select">
                                <option value="">-- Select Cluster --</option>
                                @foreach($clusters as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="task_target_role_filter" class="form-label">Target Role Inside Cluster</label>
                            <select name="target_role_filter" id="task_target_role_filter" class="form-select">
                                <option value="all">Everyone in Cluster (Cluster Head, Top Coor, Officers)</option>
                                <option value="top_coor">Top Coordinators Only</option>
                                <option value="registry_officer">Registry Officers Only</option>
                                <option value="cluster_head">Cluster Head Only</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="task_title" class="form-label required">Task Title / Order Objective</label>
                        <input type="text" name="title" id="task_title" class="form-control" required placeholder="e.g. Conduct cluster-wide census validation or deliver field logistics">
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                        <div class="form-group">
                            <label for="task_priority" class="form-label required">Priority Level</label>
                            <select name="priority" id="task_priority" class="form-select" required>
                                <option value="low">Low Priority</option>
                                <option value="normal" selected>Normal Priority</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent Priority</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="task_work_location" class="form-label">Work Location / Barangay</label>
                            <input type="text" name="work_location" id="task_work_location" class="form-control" placeholder="e.g. Mariveles, Cabcaben, Baseco">
                        </div>

                        <div class="form-group">
                            <label for="task_origin" class="form-label">Origin</label>
                            <input type="text" name="origin" id="task_origin" class="form-control" placeholder="e.g. 627 Central HQ" value="627 Operations Desk">
                        </div>

                        <div class="form-group">
                            <label for="task_start_datetime" class="form-label">Start Date & Time</label>
                            <input type="datetime-local" name="start_datetime" id="task_start_datetime" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                        </div>

                        <div class="form-group">
                            <label for="task_due_datetime" class="form-label required">Deadline / Due Date & Time</label>
                            <input type="datetime-local" name="due_datetime" id="task_due_datetime" class="form-control" required value="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="task_instructions" class="form-label">Detailed Instructions & Scope</label>
                        <textarea name="instructions" id="task_instructions" class="form-control" rows="3" placeholder="Provide full step-by-step guidance for the assigned personnel..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="task_attachment" class="form-label">Optional Reference Document / Memo</label>
                        <input type="file" name="attachment" id="task_attachment" class="form-control" accept=".jpeg,.png,.jpg,.pdf,.webp,.doc,.docx">
                        <span style="font-size: 11px; color: var(--text-light);">Max size: 5MB (JPG, PNG, PDF, DOCX)</span>
                    </div>
                </div>
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-primary" id="saveTaskBtn">
                        <i class="bi bi-send-fill"></i> Assign Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. View Task Details Modal -->
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
    function toggleAssignMode(mode) {
        if (mode === 'single') {
            document.getElementById('singleUserGroup').style.display = 'block';
            document.getElementById('task_assigned_to').setAttribute('required', 'required');
            document.getElementById('singleClusterGroup').style.display = 'none';
            document.getElementById('task_target_cluster').removeAttribute('required');
        } else if (mode === 'single_cluster') {
            document.getElementById('singleUserGroup').style.display = 'none';
            document.getElementById('task_assigned_to').removeAttribute('required');
            document.getElementById('singleClusterGroup').style.display = 'grid';
            document.getElementById('task_target_cluster').setAttribute('required', 'required');
        } else {
            document.getElementById('singleUserGroup').style.display = 'none';
            document.getElementById('task_assigned_to').removeAttribute('required');
            document.getElementById('singleClusterGroup').style.display = 'none';
            document.getElementById('task_target_cluster').removeAttribute('required');
        }
    }

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
