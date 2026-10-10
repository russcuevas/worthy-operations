@extends('layouts.app')

@section('title', 'Top Coordinator Task Management')
@section('page-title', 'Task Coordination & Delegation')
@section('page-subtitle', 'Delegate duties to Registry Officers, submit completions for directives assigned to you, and inspect field photos')

@section('content')
    <!-- View Mode Tabs -->
    <div class="wa-card" style="margin-bottom: 20px;">
        <div class="wa-card-body" style="padding: 14px 20px;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('top_coor.tasks.index', ['view' => 'assigned_by_me']) }}" class="btn-wa {{ $viewMode !== 'assigned_to_me' ? 'btn-wa-primary' : 'btn-wa-outline' }}">
                        <i class="bi bi-send-fill"></i>
                        <span>Tasks Given to Officers ({{ $counts['given_total'] }})</span>
                    </a>
                    <a href="{{ route('top_coor.tasks.index', ['view' => 'assigned_to_me']) }}" class="btn-wa {{ $viewMode === 'assigned_to_me' ? 'btn-wa-primary' : 'btn-wa-outline' }}">
                        <i class="bi bi-inbox-fill"></i>
                        <span>Tasks Assigned to Me ({{ $counts['my_tasks_total'] }})</span>
                    </a>
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('top_coor.tasks.calendar') }}" class="btn-wa btn-wa-outline">
                        <i class="bi bi-calendar-week"></i>
                        <span>Calendar</span>
                    </a>
                    @if($viewMode !== 'assigned_to_me')
                        <button type="button" class="btn-wa btn-wa-primary" data-modal-open="createTopCoorTaskModal">
                            <i class="bi bi-plus-lg"></i>
                            <span>Assign Task to Officer</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks List Table -->
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-check2-square text-primary"></i>
                <span>{{ $viewMode === 'assigned_to_me' ? 'Directives Assigned to Me' : 'Tasks Assigned to Registry Officers' }} ({{ $tasks->count() }})</span>
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
                            <th style="text-align: right; width: 140px;">Action</th>
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
                                        <div style="font-size: 11px; color: #DC2626; font-weight: 600; margin-top: 2px;">
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
                                    <div style="display: inline-flex; gap: 4px;">
                                        <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="viewTopCoorTaskDetails({{ $task->id }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        @if($viewMode === 'assigned_to_me')
                                            @if($task->status === 'not_started')
                                                <form action="{{ route('top_coor.tasks.start', $task->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn-wa btn-wa-primary btn-wa-sm" title="Start Task">
                                                        <i class="bi bi-play-fill"></i> Start
                                                    </button>
                                                </form>
                                            @endif

                                            @if($task->status !== 'completed')
                                                <button type="button" class="btn-wa btn-wa-success btn-wa-sm" onclick="openTopCoorSubmitModal({{ $task->id }}, '{{ addslashes($task->title) }}')" title="Submit Remarks & Proof">
                                                    <i class="bi bi-cloud-arrow-up-fill"></i> Submit
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal-backdrop-custom" id="createTopCoorTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-send-plus-fill text-primary"></i>
                    <span>Assign Task to Registry Officer</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="{{ route('top_coor.tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="wa-modal-body">
                    <div class="form-group" style="background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 14px; margin-bottom: 16px;">
                        <label class="form-label required" style="font-weight: 700;">Assignee Target</label>
                        <div style="display: flex; gap: 16px; margin-top: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="assign_mode" value="single" checked onchange="toggleTcMode('single')">
                                <span>Specific Registry Officer</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="assign_mode" value="all_cluster_officers" onchange="toggleTcMode('all_cluster_officers')">
                                <span>All Registry Officers in My Cluster</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="tcSingleGroup">
                        <label for="tc_assigned_to" class="form-label required">Select Registry Officer</label>
                        <select name="assigned_to" id="tc_assigned_to" class="form-select">
                            <option value="">-- Choose Registry Officer --</option>
                            @foreach($assignableOfficers as $off)
                                <option value="{{ $off->id }}">{{ $off->name }} ({{ $off->cluster ?? 'General' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tc_title" class="form-label required">Task Title / Mission</label>
                        <input type="text" name="title" id="tc_title" class="form-control" required placeholder="e.g. Conduct house-to-house census & photo documentation">
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                        <div class="form-group">
                            <label for="tc_priority" class="form-label required">Priority Level</label>
                            <select name="priority" id="tc_priority" class="form-select" required>
                                <option value="low">Low Priority</option>
                                <option value="normal" selected>Normal Priority</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent Priority</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tc_work_location" class="form-label">Location / Area</label>
                            <input type="text" name="work_location" id="tc_work_location" class="form-control" placeholder="e.g. Barangay Alion">
                        </div>

                        <div class="form-group">
                            <label for="tc_due_datetime" class="form-label required">Deadline / Due Date</label>
                            <input type="datetime-local" name="due_datetime" id="tc_due_datetime" class="form-control" required value="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tc_instructions" class="form-label">Instructions & Requirements</label>
                        <textarea name="instructions" id="tc_instructions" class="form-control" rows="3" placeholder="Explain what the officer must accomplish and photos to capture..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tc_attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" id="tc_attachment" class="form-control">
                    </div>
                </div>
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-primary">
                        <i class="bi bi-send-fill"></i> Assign Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Submit Proof Modal for My Own Assigned Tasks -->
    <div class="modal-backdrop-custom" id="topCoorSubmitModal">
        <div class="wa-modal-dialog">
            <div class="wa-modal-header" style="background: #F0FDF4;">
                <div class="wa-modal-title" style="color: #166534;">
                    <i class="bi bi-cloud-arrow-up-fill text-success"></i>
                    <span>Submit Remarks & Evidence Photos</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="topCoorSubmitForm">
                @csrf
                <div class="wa-modal-body">
                    <div style="font-weight: 700; font-size: 14px; color: var(--color-deep-navy); margin-bottom: 12px;" id="tc_proof_task_title">
                        Task Title
                    </div>

                    <div class="form-group">
                        <label for="tc_completion_remarks" class="form-label required">Completion Remarks / Accomplishment Report</label>
                        <textarea name="completion_remarks" id="tc_completion_remarks" class="form-control" rows="3" required placeholder="Provide details of your completed coordination or directive..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tc_evidence_files" class="form-label">Attach Photo Evidence / Receipts</label>
                        <input type="file" name="evidence_files[]" id="tc_evidence_files" class="form-control" multiple accept="image/*,.pdf,.doc,.docx">
                        <span style="font-size: 11px; color: var(--text-light);">Select one or more photos or documents</span>
                    </div>
                </div>
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-success">
                        <i class="bi bi-check-circle-fill"></i> Complete Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal-backdrop-custom" id="viewTopCoorTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span id="tc_modal_title">Task Details</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <div class="wa-modal-body" id="tc_modal_body">
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
    function toggleTcMode(mode) {
        if (mode === 'single') {
            document.getElementById('tcSingleGroup').style.display = 'block';
            document.getElementById('tc_assigned_to').setAttribute('required', 'required');
        } else {
            document.getElementById('tcSingleGroup').style.display = 'none';
            document.getElementById('tc_assigned_to').removeAttribute('required');
        }
    }

    function openTopCoorSubmitModal(taskId, title) {
        document.getElementById('topCoorSubmitForm').action = `/top-coor/tasks/${taskId}/submit-proof`;
        document.getElementById('tc_proof_task_title').innerText = title;
        document.getElementById('tc_completion_remarks').value = '';
        openModal('topCoorSubmitModal');
    }

    function viewTopCoorTaskDetails(taskId) {
        openModal('viewTopCoorTaskModal');
        const body = document.getElementById('tc_modal_body');
        body.innerHTML = '<div style="text-align: center; padding: 30px;"><div class="spinner-border text-primary"></div></div>';

        fetch(`/top-coor/tasks/${taskId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const t = data.task;
                    document.getElementById('tc_modal_title').innerText = `${t.task_code} - ${t.title}`;

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
                                <strong style="color: #166534;"><i class="bi bi-check-circle-fill"></i> Remarks / Proof:</strong>
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
