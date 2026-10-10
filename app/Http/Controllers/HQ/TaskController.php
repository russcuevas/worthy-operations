<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\StaffTask;
use App\Models\TaskHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // HQ can assign to: Cluster Head, Top Coor, and Registry Officer
        $assignableUsers = User::whereIn('role', ['cluster_head', 'top_coor', 'registry_officer'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $clusters = User::whereNotNull('cluster')->where('cluster', '!=', '')->distinct()->pluck('cluster')->sort()->values();

        // Tasks assigned by this HQ user, or assigned to this HQ user, or involving their subordinates
        $query = StaffTask::with(['assignedTo', 'assignedBy'])
            ->where(function($q) use ($user) {
                $q->where('assigned_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
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
            'all' => (clone $query)->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'not_started' => (clone $query)->where('status', 'not_started')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'overdue' => (clone $query)->whereNotIn('status', ['completed', 'cancelled'])->where('due_datetime', '<', Carbon::now())->count(),
        ];

        return view('hq.tasks.index', compact('tasks', 'assignableUsers', 'clusters', 'counts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'assign_mode' => ['required', 'in:single,cluster,role_group'],
            'assigned_to' => ['nullable', 'required_if:assign_mode,single', 'exists:users,id'],
            'target_cluster' => ['nullable', 'required_if:assign_mode,cluster', 'string'],
            'target_role' => ['nullable', 'in:cluster_head,top_coor,registry_officer'],
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

        // Determine recipients
        $recipients = collect();
        if ($validated['assign_mode'] === 'single') {
            $recipients = User::where('id', $validated['assigned_to'])
                ->whereIn('role', ['cluster_head', 'top_coor', 'registry_officer'])
                ->where('status', 'active')
                ->get();
        } elseif ($validated['assign_mode'] === 'cluster') {
            $q = User::where('cluster', $validated['target_cluster'])
                ->whereIn('role', ['cluster_head', 'top_coor', 'registry_officer'])
                ->where('status', 'active');
            if (!empty($validated['target_role'])) {
                $q->where('role', $validated['target_role']);
            }
            $recipients = $q->get();
        } elseif ($validated['assign_mode'] === 'role_group') {
            $roleToAssign = $validated['target_role'] ?? 'top_coor';
            $recipients = User::where('role', $roleToAssign)->where('status', 'active')->get();
        }

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No eligible active personnel found for the selected criteria.');
        }

        $createdCount = 0;
        foreach ($recipients as $recipient) {
            $taskCode = 'HQ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $task = StaffTask::create([
                'task_code' => $taskCode,
                'title' => $validated['title'],
                'instructions' => $validated['instructions'] ?? null,
                'assigned_by' => $user->id,
                'assigned_to' => $recipient->id,
                'cluster' => $recipient->cluster ?? ($validated['target_cluster'] ?? null),
                'work_location' => $validated['work_location'] ?? null,
                'origin' => $validated['origin'] ?? 'HQ Operations',
                'priority' => $validated['priority'],
                'status' => 'not_started',
                'start_datetime' => $validated['start_datetime'] ?? null,
                'due_datetime' => $validated['due_datetime'],
                'initial_attachment' => $attachmentPath,
            ]);

            TaskHistory::create([
                'staff_task_id' => $task->id,
                'user_id' => $user->id,
                'action' => 'Task Created & Assigned (HQ)',
                'remarks' => "HQ Order issued by {$user->name} to {$recipient->name} ({$recipient->role_label})",
            ]);

            Notification::create([
                'user_id' => $recipient->id,
                'title' => 'New Order from HQ: ' . $task->title,
                'message' => "HQ ({$user->name}) has assigned you task {$task->task_code}. Due: {$task->due_datetime->format('M d, Y h:i A')}.",
                'type' => 'task_assigned',
                'link_url' => "/tasks?open_task={$task->id}",
            ]);

            $createdCount++;
        }

        AuditLog::record('HQ Tasks', 'Issue Order', "HQ {$user->name} issued {$createdCount} tasks for {$validated['title']}");

        $msg = $createdCount > 1 
            ? "HQ Orders successfully distributed to {$createdCount} personnel!"
            : "HQ Task successfully assigned to {$recipients->first()->name}!";

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('hq.tasks.index')->with('success', $msg);
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
        $user = auth()->user();

        if ($task->assigned_by !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized to cancel this task.');
        }

        $task->update([
            'status' => 'cancelled',
            'completion_remarks' => $request->input('reason', 'Cancelled by HQ (' . $user->name . ')'),
        ]);

        TaskHistory::create([
            'staff_task_id' => $task->id,
            'user_id' => $user->id,
            'action' => 'Cancelled',
            'remarks' => $request->input('reason', 'Cancelled by HQ (' . $user->name . ')'),
        ]);

        AuditLog::record('HQ Tasks', 'Cancel Task', "HQ cancelled task {$task->task_code}");

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Task {$task->task_code} has been cancelled."]);
        }

        return redirect()->route('hq.tasks.index')->with('success', "Task {$task->task_code} has been cancelled.");
    }

    public function calendar()
    {
        return view('hq.tasks.calendar');
    }

    public function calendarEvents(Request $request)
    {
        $user = auth()->user();
        $query = StaffTask::with(['assignedTo', 'assignedBy'])
            ->where(function($q) use ($user) {
                $q->where('assigned_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });

        $tasks = $query->get();

        $events = $tasks->map(function($task) {
            $color = match($task->status) {
                'completed' => '#10B981',
                'in_progress' => '#075998',
                'cancelled' => '#6B7280',
                default => '#51B8E5',
            };

            return [
                'id' => $task->id,
                'title' => "{$task->assignedTo->name}: {$task->title}",
                'start' => ($task->start_datetime ?? $task->due_datetime->copy()->subHours(2))->toIso8601String(),
                'end' => $task->due_datetime->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'task_code' => $task->task_code,
                    'staff_name' => $task->assignedTo->name,
                    'assigned_by_name' => $task->assignedBy->name . ' (' . $task->assignedBy->role_label . ')',
                    'cluster' => $task->cluster ?? 'N/A',
                    'location' => $task->work_location ?? 'N/A',
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
