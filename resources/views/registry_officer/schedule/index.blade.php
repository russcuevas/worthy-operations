@extends('layouts.app')

@section('title', 'My Schedule Calendar')
@section('page-title', 'My Schedule Calendar')
@section('page-subtitle', 'Timeline of all duties and tasks assigned to you')

@push('styles')
<style>
    .fc-daygrid-day-frame { overflow: hidden !important; }
    .fc-daygrid-event-harness { max-width: 100% !important; overflow: hidden !important; }
    .fc-daygrid-event { max-width: 100% !important; overflow: hidden !important; box-sizing: border-box !important; }
    .fc-event-main { max-width: 100% !important; overflow: hidden !important; box-sizing: border-box !important; }
    .fc-event { border-radius: 6px !important; border: none !important; margin: 2px 0 !important; max-width: 100% !important; overflow: hidden !important; cursor: pointer; }
    .fc-cal-event-pill { display: flex; align-items: center; gap: 5px; width: 100%; max-width: 100%; box-sizing: border-box; padding: 3px 6px; border-radius: 6px; font-size: 11px; overflow: hidden; color: #fff; }
    .fc-cal-time-badge { background: rgba(0, 0, 0, 0.28); color: #fff; padding: 1px 4px; border-radius: 4px; font-weight: 700; font-size: 10px; white-space: nowrap; flex-shrink: 0; }
    .fc-cal-title-text { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; flex: 1 1 0%; }
</style>
@endpush

@section('content')
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-calendar-week-fill text-primary"></i>
                <span>My Duty Schedule</span>
            </div>
            <a href="{{ route('registry_officer.tasks.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">My Tasks List</a>
        </div>
        <div class="wa-card-body" style="padding: 20px;">
            <div id="officerCalendar" style="min-height: 650px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('officerCalendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            dayMaxEvents: 3,
            eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
            displayEventTime: true,
            displayEventEnd: true,
            events: '/registry-officer/schedule/events',
            eventContent: function(arg) {
                const timeText = arg.timeText || '';
                const title = arg.event.title || '';
                const bg = arg.event.backgroundColor || '#075998';

                const container = document.createElement('div');
                container.className = 'fc-cal-event-pill';
                container.style.backgroundColor = bg;

                let timeHtml = timeText ? `<span class="fc-cal-time-badge">${timeText}</span>` : '';
                let titleHtml = `<span class="fc-cal-title-text" title="${title}">${title}</span>`;

                container.innerHTML = `${timeHtml}${titleHtml}`;
                return { domNodes: [container] };
            },
            eventClick: function(info) {
                const props = info.event.extendedProps;
                alert(`Task: ${props.task_code}\nTitle: ${info.event.title}\nAssigned By: ${props.assigned_by || 'Superior'}\nStatus: ${props.status || 'Active'}`);
            }
        });
        calendar.render();
    });
</script>
@endpush
