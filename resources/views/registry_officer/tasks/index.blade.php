@extends('layouts.app')

@section('title', 'My Assigned Tasks')
@section('page-title', 'My Field Tasks & Submissions')
@section('page-subtitle', 'View instructions, update status, and submit completion remarks along with photo evidence')

@section('content')
    <!-- Filter Tabs -->
    <div class="wa-card" style="margin-bottom: 20px;">
        <div class="wa-card-body" style="padding: 14px 20px;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <a href="{{ route('registry_officer.tasks.index') }}" class="btn-wa {{ !request()->has('status') ? 'btn-wa-primary' : 'btn-wa-outline' }} btn-wa-sm">
                        All ({{ $counts['all'] }})
                    </a>
                    <a href="{{ route('registry_officer.tasks.index', ['status' => 'not_started']) }}" class="btn-wa {{ request('status') === 'not_started' ? 'btn-wa-primary' : 'btn-wa-outline' }} btn-wa-sm">
                        Not Started ({{ $counts['not_started'] }})
                    </a>
                    <a href="{{ route('registry_officer.tasks.index', ['status' => 'in_progress']) }}" class="btn-wa {{ request('status') === 'in_progress' ? 'btn-wa-primary' : 'btn-wa-outline' }} btn-wa-sm">
                        In Progress ({{ $counts['in_progress'] }})
                    </a>
                    <a href="{{ route('registry_officer.tasks.index', ['status' => 'completed']) }}" class="btn-wa {{ request('status') === 'completed' ? 'btn-wa-success' : 'btn-wa-outline' }} btn-wa-sm">
                        Completed ({{ $counts['completed'] }})
                    </a>
                </div>

                <a href="{{ route('registry_officer.schedule.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                    <i class="bi bi-calendar-check"></i> My Calendar
                </a>
            </div>
        </div>
    </div>

    <!-- Tasks List Cards -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @forelse($tasks as $task)
            <div class="wa-card" style="margin-bottom: 0;">
                <div class="wa-card-body" style="padding: 20px;">
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 16px;">
                        <div style="flex: 1; min-width: 280px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <code>{{ $task->task_code }}</code>
                                <span class="badge {{ $task->priority_badge }}">{{ ucfirst($task->priority) }} Priority</span>
                                <span class="badge {{ $task->status_badge }}">{{ $task->status_label }}</span>
                                @if($task->is_overdue)
                                    <span class="badge badge-danger">Overdue</span>
                                @endif
                            </div>

                            <h3 style="font-size: 16px; font-weight: 800; color: var(--color-deep-navy); margin-bottom: 6px;">
                                {{ $task->title }}
                            </h3>

                            <div style="display: flex; flex-wrap: wrap; gap: 16px; font-size: 12.5px; color: var(--text-muted); margin-bottom: 12px;">
                                <div><i class="bi bi-person-badge-fill text-primary"></i> <strong>Order Given By (Tagged):</strong> <span class="badge {{ $task->assignedBy->role_badge }}">{{ $task->assignedBy->name }} ({{ $task->assignedBy->role_label }})</span></div>
                                @if($task->work_location)
                                    <div><i class="bi bi-geo-alt-fill text-danger"></i> <strong>Location:</strong> {{ $task->work_location }}</div>
                                @endif
                                <div><i class="bi bi-clock-history"></i> <strong>Deadline:</strong> {{ $task->due_datetime->format('M d, Y h:i A') }} ({{ $task->due_datetime->diffForHumans() }})</div>
                            </div>

                            @if($task->instructions)
                                <div style="background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 12px 14px; font-size: 13px; color: var(--text-main); margin-bottom: 12px;">
                                    <strong><i class="bi bi-info-circle-fill text-primary"></i> Instructions:</strong>
                                    <div style="margin-top: 4px;">{{ $task->instructions }}</div>
                                </div>
                            @endif

                            @if($task->completion_remarks)
                                <div style="background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: var(--radius-sm); padding: 12px 14px; font-size: 13px; color: #166534; margin-bottom: 12px;">
                                    <strong><i class="bi bi-check-circle-fill"></i> My Submitted Completion Remarks:</strong>
                                    <div style="margin-top: 4px;">{{ $task->completion_remarks }}</div>
                                </div>
                            @endif

                            @if(!empty($task->evidence_attachments))
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #166534; font-weight: 600;">
                                    <i class="bi bi-images"></i> {{ count($task->evidence_attachments) }} Photo Proof(s) Uploaded
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; flex-direction: column; gap: 8px; min-width: 180px;">
                            <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="viewOfficerTaskDetails({{ $task->id }})">
                                <i class="bi bi-eye"></i> View Full Details
                            </button>

                            @if($task->status === 'not_started')
                                <form action="{{ route('registry_officer.tasks.start', $task->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn-wa btn-wa-primary btn-wa-sm" style="width: 100%;">
                                        <i class="bi bi-play-circle-fill"></i> Start Task
                                    </button>
                                </form>
                            @endif

                            @if($task->status !== 'completed')
                                <button type="button" class="btn-wa btn-wa-success btn-wa-sm" onclick="openOfficerSubmitModal({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                    <i class="bi bi-cloud-arrow-up-fill"></i> Submit Remarks & Photos
                                </button>
                            @else
                                <div style="font-size: 12px; color: #166534; background: #F0FDF4; border: 1px solid #DCFCE7; padding: 8px 12px; border-radius: 6px; text-align: center; font-weight: 700;">
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="wa-card">
                <div class="wa-card-body" style="text-align: center; padding: 40px 20px; color: var(--text-light);">
                    <i class="bi bi-card-checklist" style="font-size: 42px; color: var(--color-wave-cyan); display: block; margin-bottom: 10px;"></i>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--color-deep-navy);">No tasks found in this section</h3>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">You have no pending assignments!</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Submit Remarks & Photos Modal -->
    <div class="modal-backdrop-custom" id="officerSubmitModal">
        <div class="wa-modal-dialog">
            <div class="wa-modal-header" style="background: #F0FDF4;">
                <div class="wa-modal-title" style="color: #166534;">
                    <i class="bi bi-cloud-arrow-up-fill text-success"></i>
                    <span>Submit Remarks & Evidence Photos</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="officerSubmitForm">
                @csrf
                <div class="wa-modal-body">
                    <div style="font-weight: 700; font-size: 14px; color: var(--color-deep-navy); margin-bottom: 12px;" id="officer_task_title">
                        Task Title
                    </div>

                    <div class="form-group">
                        <label for="off_completion_remarks" class="form-label required">Completion Remarks / Accomplishment Details</label>
                        <textarea name="completion_remarks" id="off_completion_remarks" class="form-control" rows="4" required placeholder="Describe the outcome of your task, observations, numbers recorded, etc..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="off_evidence_files" class="form-label">Upload Evidence Photos / Documents</label>
                        <input type="file" name="evidence_files[]" id="off_evidence_files" class="form-control" multiple accept="image/*,.pdf,.doc,.docx">
                        <span style="font-size: 11px; color: var(--text-light);">Take photos or select from gallery (JPG, PNG, PDF)</span>
                    </div>

                    <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: var(--radius-sm); padding: 10px 12px; font-size: 12px; color: #1E40AF;">
                        <i class="bi bi-info-circle-fill me-1"></i> Submitting will automatically mark this task as Completed and notify whoever assigned it to you.
                    </div>
                </div>
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-success">
                        <i class="bi bi-check-circle-fill"></i> Complete & Submit Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal-backdrop-custom" id="viewOfficerTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span id="off_modal_title">Task Details</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <div class="wa-modal-body" id="off_modal_body">
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
    function openOfficerSubmitModal(taskId, title) {
        document.getElementById('officerSubmitForm').action = `/registry-officer/tasks/${taskId}/submit-proof`;
        document.getElementById('officer_task_title').innerText = title;
        document.getElementById('off_completion_remarks').value = '';
        openModal('officerSubmitModal');
    }

    function viewOfficerTaskDetails(taskId) {
        openModal('viewOfficerTaskModal');
        const body = document.getElementById('off_modal_body');
        body.innerHTML = '<div style="text-align: center; padding: 30px;"><div class="spinner-border text-primary"></div></div>';

        fetch(`/registry-officer/tasks/${taskId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const t = data.task;
                    document.getElementById('off_modal_title').innerText = `${t.task_code} - ${t.title}`;

                    let photosHtml = '';
                    if (data.evidence_attachments && data.evidence_attachments.length > 0) {
                        photosHtml = `
                            <div style="margin-top: 16px;">
                                <h4 style="font-size: 14px; font-weight: 700; color: var(--color-deep-navy); margin-bottom: 10px;">
                                    <i class="bi bi-images text-success"></i> Uploaded Photos (${data.evidence_attachments.length})
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
                            <div><strong>Order Given By:</strong> <span class="badge badge-primary">${data.assigned_by_name}</span></div>
                            <div><strong>Status:</strong> <span class="badge ${t.status === 'completed' ? 'badge-success' : 'badge-info'}">${t.status.toUpperCase()}</span></div>
                            <div><strong>Location:</strong> ${data.work_location}</div>
                            <div><strong>Origin:</strong> ${data.origin}</div>
                            <div><strong>Deadline:</strong> ${data.due_formatted}</div>
                        </div>

                        ${t.instructions ? `
                            <div style="background: #F8FAFC; border: 1px solid var(--card-border); border-radius: var(--radius-sm); padding: 12px; font-size: 13px; margin-bottom: 16px;">
                                <strong>Instructions:</strong>
                                <div>${t.instructions}</div>
                            </div>
                        ` : ''}

                        ${t.completion_remarks ? `
                            <div style="background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: var(--radius-sm); padding: 12px; font-size: 13px; margin-bottom: 16px;">
                                <strong style="color: #166534;"><i class="bi bi-check-circle-fill"></i> My Remarks:</strong>
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
