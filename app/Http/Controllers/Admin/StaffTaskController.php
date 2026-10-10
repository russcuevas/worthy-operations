<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\StaffTask;
use App\Models\TaskHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffTaskController extends Controller
{
    public function index(Request $request)
    {
        $allUsers = User::where('status', 'active')->orderBy('name')->get();
        $staffMembers = $allUsers;
        $clusters = User::whereNotNull('cluster')->where('cluster', '!=', '')->distinct()->pluck('cluster')->sort()->values();

        $query = StaffTask::with(['assignedTo', 'assignedBy']);

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('assigned_by')) {
            $query->where('assigned_by', $request->assigned_by);
        }

        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('filter_overdue') && $request->filter_overdue == '1') {
            $query->whereNotIn('status', ['completed', 'cancelled'])
                  ->where('due_datetime', '<', Carbon::now());
        }

        $tasks = $query->latest('due_datetime')->get();

        $counts = [
            'all' => StaffTask::count(),
            'in_progress' => StaffTask::where('status', 'in_progress')->count(),
            'not_started' => StaffTask::where('status', 'not_started')->count(),
            'completed' => StaffTask::where('status', 'completed')->count(),
            'overdue' => StaffTask::whereNotIn('status', ['completed', 'cancelled'])->where('due_datetime', '<', Carbon::now())->count(),
        ];

        return view('admin.tasks.index', compact('tasks', 'staffMembers', 'allUsers', 'clusters', 'counts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'assign_mode' => ['required', 'in:single,all_top_coor,all_clusters,single_cluster'],
            'assigned_to' => ['nullable', 'required_if:assign_mode,single', 'exists:users,id'],
            'target_cluster' => ['nullable', 'required_if:assign_mode,single_cluster', 'string'],
            'target_role_filter' => ['nullable', 'string'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'origin' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'start_datetime' => ['nullable', 'date'],
            'due_datetime' => ['required', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,webp,doc,docx', 'max:5120'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'task_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/tasks');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $attachmentPath = 'uploads/tasks/' . $filename;
        }

        $workLoc = $validated['work_location'] ?? null;
        $origin = $validated['origin'] ?? '627 Central Command';

        // Determine recipient users based on assign_mode
        $assignMode = $validated['assign_mode'];
        $recipients = collect();

        if ($assignMode === 'single') {
            $recipients = User::where('id', $validated['assigned_to'])->where('status', 'active')->get();
        } elseif ($assignMode === 'all_top_coor') {
            $recipients = User::where('role', 'top_coor')->where('status', 'active')->get();
        } elseif ($assignMode === 'all_clusters') {
            $recipients = User::whereIn('role', ['cluster_head', 'top_coor', 'registry_officer'])
                              ->whereNotNull('cluster')
                              ->where('status', 'active')
                              ->get();
        } elseif ($assignMode === 'single_cluster') {
            $q = User::where('cluster', $validated['target_cluster'])->where('status', 'active');
            if (!empty($validated['target_role_filter']) && $validated['target_role_filter'] !== 'all') {
                $q->where('role', $validated['target_role_filter']);
            }
            $recipients = $q->get();
        }

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No active users found for the selected assignment criteria.');
        }

        $createdTasksCount = 0;
        foreach ($recipients as $recipient) {
            $taskCode = 'TSK-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $task = StaffTask::create([
                'task_code' => $taskCode,
                'title' => $validated['title'],
                'instructions' => $validated['instructions'] ?? null,
                'assigned_by' => auth()->id(),
                'assigned_to' => $recipient->id,
                'cluster' => $recipient->cluster ?? ($validated['target_cluster'] ?? null),
                'work_location' => $workLoc,
                'origin' => $origin,
                'priority' => $validated['priority'],
                'status' => 'not_started',
                'start_datetime' => $validated['start_datetime'] ?? null,
                'due_datetime' => $validated['due_datetime'],
                'initial_attachment' => $attachmentPath,
            ]);

            TaskHistory::create([
                'staff_task_id' => $task->id,
                'user_id' => auth()->id(),
                'action' => 'Task Created & Assigned (627 Admin)',
                'remarks' => "Assigned by 627 Admin (" . auth()->user()->name . ") to {$recipient->name} ({$recipient->role_label})" . ($task->cluster ? " in [{$task->cluster}]" : ""),
            ]);

            Notification::create([
                'user_id' => $recipient->id,
                'title' => 'New Order from 627 (Admin): ' . $task->title,
                'message' => "627 Admin (" . auth()->user()->name . ") assigned you task {$task->task_code}. Due on {$task->due_datetime->format('M d, Y h:i A')}.",
                'type' => 'task_assigned',
                'link_url' => "/tasks?open_task={$task->id}",
            ]);

            $createdTasksCount++;
        }

        AuditLog::record('Tasks', 'Bulk/Create Task', "627 Admin created {$createdTasksCount} tasks: {$validated['title']} (Mode: {$assignMode})");

        $successMsg = $createdTasksCount > 1 
            ? "Bulk assignment successful! {$createdTasksCount} tasks assigned to personnel."
            : "Task assigned successfully to {$recipients->first()->name}!";

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $successMsg]);
        }

        return redirect()->route('admin.tasks.index')->with('success', $successMsg);
    }

    public function show($id)
    {
        $task = StaffTask::with(['assignedTo', 'assignedBy', 'histories.user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'task' => $task,
            'origin' => $task->origin ?? 'N/A',
            'work_location' => $task->work_location ?? 'N/A',
            'cluster' => $task->cluster ?? 'N/A',
            'assigned_to_name' => $task->assignedTo->name . ' (' . $task->assignedTo->role_label . ')',
            'assigned_by_name' => $task->assignedBy->name . ' (' . $task->assignedBy->role_label . ')',
            'due_formatted' => $task->due_datetime->format('M d, Y h:i A'),
            'start_formatted' => $task->start_datetime ? $task->start_datetime->format('M d, Y h:i A') : 'N/A',
            'initial_attachment_url' => $task->initial_attachment ? asset($task->initial_attachment) : null,
            'evidence_attachments' => $task->evidence_attachments ? array_map(function($ev) {
                return [
                    'url' => asset($ev['path']),
                    'name' => $ev['name'] ?? 'Evidence File',
                    'time' => $ev['time'] ?? '',
                    'uploader' => $ev['uploader'] ?? 'Staff',
                ];
            }, $task->evidence_attachments) : [],
            'histories' => $task->histories->map(function($h) {
                return [
                    'action' => $h->action,
                    'user' => $h->user ? $h->user->name : 'System',
                    'remarks' => $h->remarks,
                    'date' => $h->created_at->format('M d, Y h:i A'),
                ];
            }),
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $task = StaffTask::findOrFail($id);

        $task->update([
            'status' => 'cancelled',
            'completion_remarks' => $request->input('reason', 'Cancelled by 627 Admin'),
        ]);

        TaskHistory::create([
            'staff_task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'Cancelled',
            'remarks' => $request->input('reason', 'Cancelled by 627 Admin'),
        ]);

        AuditLog::record('Tasks', 'Cancel Task', "Cancelled task {$task->task_code}");

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Task {$task->task_code} has been cancelled."]);
        }

        return redirect()->route('admin.tasks.index')->with('success', "Task {$task->task_code} has been cancelled.");
    }

    public function calendar(Request $request)
    {
        $staffMembers = User::where('status', 'active')->orderBy('name')->get();
        $clusters = User::whereNotNull('cluster')->where('cluster', '!=', '')->distinct()->pluck('cluster')->sort()->values();
        return view('admin.tasks.calendar', compact('staffMembers', 'clusters'));
    }

    public function calendarEvents(Request $request)
    {
        $query = StaffTask::with(['assignedTo', 'assignedBy']);

        if ($request->filled('assigned_to') && $request->assigned_to !== 'all') {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('cluster') && $request->cluster !== 'all') {
            $query->where('cluster', $request->cluster);
        }

        $tasks = $query->get();

        $events = $tasks->map(function($task) {
            $color = match($task->status) {
                'completed' => '#10B981',
                'in_progress' => '#075998',
                'cancelled' => '#6B7280',
                default => '#0284C7',
            };

            $start = $task->start_datetime 
                ? $task->start_datetime->toIso8601String() 
                : ($task->due_datetime ? $task->due_datetime->copy()->subHours(2)->toIso8601String() : now()->toIso8601String());

            return [
                'id' => $task->id,
                'title' => "{$task->assignedTo->name}: {$task->title}",
                'start' => $start,
                'end' => $task->due_datetime ? $task->due_datetime->toIso8601String() : null,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'task_code' => $task->task_code,
                    'task_title' => $task->title,
                    'staff_name' => $task->assignedTo->name . ' (' . $task->assignedTo->role_label . ')',
                    'assigned_by_name' => $task->assignedBy->name . ' (' . $task->assignedBy->role_label . ')',
                    'cluster' => $task->cluster ?? 'N/A',
                    'location' => $task->work_location ?? 'General',
                    'origin' => $task->origin ?? '627 Operations Desk',
                    'priority' => ucfirst($task->priority),
                    'status' => $task->status_label,
                    'start_formatted' => $task->start_datetime ? $task->start_datetime->format('M d, Y h:i A') : 'N/A',
                    'due_formatted' => $task->due_datetime ? $task->due_datetime->format('M d, Y h:i A') : 'N/A',
                    'instructions' => $task->instructions,
                    'completion_remarks' => $task->completion_remarks,
                    'evidence_count' => is_array($task->evidence_attachments) ? count($task->evidence_attachments) : 0,
                ]
            ];
        });

        return response()->json($events);
    }
}
