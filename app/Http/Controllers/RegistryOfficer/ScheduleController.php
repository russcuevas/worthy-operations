<?php

namespace App\Http\Controllers\RegistryOfficer;

use App\Http\Controllers\Controller;
use App\Models\StaffTask;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('registry_officer.schedule.index');
    }

    public function calendarEvents(Request $request)
    {
        $user = auth()->user();

        $tasks = StaffTask::with(['assignedBy'])
            ->where('assigned_to', $user->id)
            ->get();

        $events = $tasks->map(function($task) {
            $color = match($task->status) {
                'completed' => '#10B981',
                'in_progress' => '#075998',
                'cancelled' => '#6B7280',
                default => '#0284C7',
            };

            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => ($task->start_datetime ?? $task->due_datetime->copy()->subHours(2))->toIso8601String(),
                'end' => $task->due_datetime->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'task_code' => $task->task_code,
                    'assigned_by' => $task->assignedBy->name . ' (' . $task->assignedBy->role_label . ')',
                    'location' => $task->work_location ?? 'General',
                    'origin' => $task->origin ?? 'N/A',
                    'priority' => ucfirst($task->priority),
                    'status' => $task->status_label,
                    'instructions' => $task->instructions,
                ]
            ];
        });

        return response()->json($events);
    }
}
