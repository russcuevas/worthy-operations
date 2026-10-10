@extends('layouts.app')

@section('title', 'Schedule Calendar')
@section('page-title', 'Operations Schedules & Calendar')
@section('page-subtitle', 'Interactive daily, weekly, and monthly view of field operations, assignments, and personnel schedules')

@push('styles')
<style>
    .fc-theme-standard th {
        background: #F8FAFC;
        padding: 10px 0;
        font-size: 13px;
        font-weight: 700;
        color: var(--color-deep-navy);
    }
    .fc .fc-daygrid-day-top {
        font-weight: 700;
        padding: 4px 8px;
    }
    .fc-daygrid-day-frame {
        overflow: hidden !important;
    }
    .fc-daygrid-event-harness {
        max-width: 100% !important;
        overflow: hidden !important;
    }
    .fc-daygrid-event {
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }
    .fc-event-main {
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }
    .fc-event {
        border-radius: 6px !important;
        border: none !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin: 2px 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        transition: transform 0.15s ease;
    }
    .fc-event:hover {
        transform: scale(1.02);
    }
    .fc-cal-event-pill {
        display: flex;
        align-items: center;
        gap: 5px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 3px 6px;
        border-radius: 6px;
        font-size: 11px;
        line-height: 1.3;
        overflow: hidden;
        cursor: pointer;
        color: #fff;
    }
    .fc-cal-time-badge {
        background: rgba(0, 0, 0, 0.28);
        color: #fff;
        padding: 1px 4px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 10px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .fc-cal-title-text {
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
        flex: 1 1 0%;
    }
</style>
@endpush

@section('content')
    <div class="wa-card">
        <div class="wa-card-body" style="padding: 18px 24px;">
            <!-- Filter Bar -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="filter_employee" class="form-label" style="margin-bottom: 0; font-weight: 700; font-size: 13px;">Filter Staff:</label>
                        <select id="filter_employee" class="form-select" style="min-width: 200px;" onchange="calendar.refetchEvents()">
                            <option value="all">All Personnel</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->role_label }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="filter_cluster" class="form-label" style="margin-bottom: 0; font-weight: 700; font-size: 13px;">Cluster:</label>
                        <select id="filter_cluster" class="form-select" style="min-width: 170px;" onchange="calendar.refetchEvents()">
                            <option value="all">All Clusters</option>
                            @foreach($clusters as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 14px; font-size: 12px; align-items: center; flex-wrap: wrap;">
                    <span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 600;">
                        <span style="width: 12px; height: 12px; border-radius: 3px; background: #0284C7; display: inline-block;"></span> Not Started
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 600;">
                        <span style="width: 12px; height: 12px; border-radius: 3px; background: #075998; display: inline-block;"></span> In Progress
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 600;">
                        <span style="width: 12px; height: 12px; border-radius: 3px; background: #10B981; display: inline-block;"></span> Completed
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 600;">
                        <span style="width: 12px; height: 12px; border-radius: 3px; background: #6B7280; display: inline-block;"></span> Cancelled
                    </span>
                </div>
            </div>

            <!-- FullCalendar Container -->
            <div id="calendar" style="min-height: 700px;"></div>
        </div>
    </div>

    <!-- Task Detail Modal for Calendar -->
    <div class="modal-backdrop-custom" id="calendarTaskModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header" style="background: var(--color-mist-blue);">
                <div class="wa-modal-title">
                    <i class="bi bi-calendar-event text-primary"></i>
                    <span id="calTaskTitle">Task Details</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <div class="wa-modal-body">
                <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                    <span class="badge" id="calTaskStatus">Status</span>
                    <span class="badge badge-primary" id="calTaskPriority">Priority</span>
                    <span class="badge badge-info" id="calTaskCluster">Cluster</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 16px; font-size: 13px;">
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Task Code</div>
                        <div style="font-weight: 700; color: var(--color-primary-blue);" id="calTaskCode">-</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Assigned Personnel</div>
                        <div style="font-weight: 700; color: var(--color-deep-navy);" id="calTaskStaff">-</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Directive Given By</div>
                        <div style="font-weight: 700; color: var(--color-deep-navy);" id="calTaskAssignedBy">-</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Work Location</div>
                        <div id="calTaskLoc" style="font-weight: 600; color: #DC2626;">-</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Scheduled Start</div>
                        <div id="calTaskStart" style="font-weight: 600;">-</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Schedule Due / Deadline</div>
                        <div id="calTaskDue" style="font-weight: 600; color: var(--color-primary-blue);">-</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: 700;">Instructions / Directives</label>
                    <div id="calTaskDesc" style="padding: 12px 14px; background: #F8FAFC; border: 1px solid var(--card-border); border-radius: 6px; font-size: 13px; color: var(--text-main); line-height: 1.5;">-</div>
                </div>

                <div id="calRemarksBox" style="display: none; margin-top: 14px;">
                    <label class="form-label" style="font-weight: 700; color: #166534;">
                        <i class="bi bi-check-circle-fill"></i> Field Accomplishment Remarks
                    </label>
                    <div id="calTaskRemarks" style="padding: 12px 14px; background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: 6px; font-size: 13px; color: #166534; line-height: 1.5;"></div>
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
    let calendar;
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const eventsApiUrl = "{{ role_route('tasks.calendar.events') }}";

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            themeSystem: 'standard',
            dayMaxEvents: 3,
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            displayEventTime: true,
            displayEventEnd: true,
            events: function(fetchInfo, successCallback, failureCallback) {
                const staffId = document.getElementById('filter_employee').value;
                const cluster = document.getElementById('filter_cluster').value;
                fetch(`${eventsApiUrl}?assigned_to=${staffId}&cluster=${encodeURIComponent(cluster)}`)
                    .then(res => res.json())
                    .then(data => successCallback(data))
                    .catch(err => failureCallback(err));
            },
            eventContent: function(arg) {
                const timeText = arg.timeText || '';
                const title = arg.event.title || '';
                const bg = arg.event.backgroundColor || '#075998';
                const p = arg.event.extendedProps || {};

                const container = document.createElement('div');
                container.className = 'fc-cal-event-pill';
                container.style.backgroundColor = bg;

                let timeHtml = timeText ? `<span class="fc-cal-time-badge">${timeText}</span>` : '';
                let titleHtml = `<span class="fc-cal-title-text" title="${title}">${title}</span>`;

                container.innerHTML = `${timeHtml}${titleHtml}`;
                return { domNodes: [container] };
            },
            eventClick: function(info) {
                const p = info.event.extendedProps;
                document.getElementById('calTaskTitle').textContent = `${p.task_code} - ${p.task_title || info.event.title}`;
                document.getElementById('calTaskCode').textContent = p.task_code || 'N/A';
                document.getElementById('calTaskStaff').textContent = p.staff_name || 'N/A';
                document.getElementById('calTaskAssignedBy').textContent = p.assigned_by_name || 'N/A';
                document.getElementById('calTaskCluster').textContent = p.cluster || 'No Cluster';
                document.getElementById('calTaskLoc').textContent = p.location || 'General';
                document.getElementById('calTaskPriority').textContent = p.priority || 'Normal';
                
                const statusBadge = document.getElementById('calTaskStatus');
                statusBadge.textContent = p.status || 'Active';
                statusBadge.className = 'badge ' + (p.status === 'Completed' ? 'badge-success' : 'badge-info');

                document.getElementById('calTaskStart').textContent = p.start_formatted || 'N/A';
                document.getElementById('calTaskDue').textContent = p.due_formatted || 'N/A';
                document.getElementById('calTaskDesc').textContent = p.instructions || 'No specific instructions provided.';
                
                const remarksBox = document.getElementById('calRemarksBox');
                if (p.completion_remarks) {
                    document.getElementById('calTaskRemarks').textContent = p.completion_remarks;
                    remarksBox.style.display = 'block';
                } else {
                    remarksBox.style.display = 'none';
                }

                openModal('calendarTaskModal');
            }
        });
        calendar.render();
    });
</script>
@endpush
