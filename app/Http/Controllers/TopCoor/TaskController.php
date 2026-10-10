<?php

namespace App\Http\Controllers\TopCoor;

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

        // Top Coor can assign tasks to: Registry Officer
        $officersQuery = User::where('role', 'registry_officer')->where('status', 'active');
        if ($user->cluster) {
            $officersQuery->where(function($q) use ($user) {
                $q->where('cluster', $user->cluster)->orWhereNull('cluster');
            });
        }
        $assignableOfficers = $officersQuery->orderBy('name')->get();

        $viewMode = $request->get('view', 'assigned_by_me'); // 'assigned_by_me' or 'assigned_to_me'

        $query = StaffTask::with(['assignedTo', 'assignedBy']);

        if ($viewMode === 'assigned_to_me') {
            $query->where('assigned_to', $user->id);
        } else {
            $query->where('assigned_by', $user->id);
        }

        if ($request->filled('assigned_to') && $viewMode === 'assigned_by_me') {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->latest('due_datetime')->get();

        $counts = [
            'given_total' => StaffTask::where('assigned_by', $user->id)->count(),
            'given_completed' => StaffTask::where('assigned_by', $user->id)->where('status', 'completed')->count(),
            'my_tasks_total' => StaffTask::where('assigned_to', $user->id)->count(),
            'my_tasks_pending' => StaffTask::where('assigned_to', $user->id)->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        return view('top_coor.tasks.index', compact('tasks', 'assignableOfficers', 'viewMode', 'counts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'assign_mode' => ['required', 'in:single,all_cluster_officers'],
            'assigned_to' => ['nullable', 'required_if:assign_mode,single', 'exists:users,id'],
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

        $recipients = collect();
        if ($validated['assign_mode'] === 'single') {
            $recipients = User::where('id', $validated['assigned_to'])
                ->where('role', 'registry_officer')
                ->where('status', 'active')
                ->get();
        } elseif ($validated['assign_mode'] === 'all_cluster_officers') {
            $q = User::where('role', 'registry_officer')->where('status', 'active');
            if ($user->cluster) {
                $q->where('cluster', $user->cluster);
            }
            $recipients = $q->get();
        }

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No active registry officers found for the selection.');
        }

        $createdCount = 0;
        foreach ($recipients as $recipient) {
            $taskCode = 'TC-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $task = StaffTask::create([
                'task_code' => $taskCode,
                'title' => $validated['title'],
                'instructions' => $validated['instructions'] ?? null,
                'assigned_by' => $user->id,
                'assigned_to' => $recipient->id,
                'cluster' => $recipient->cluster ?? $user->cluster,
                'work_location' => $validated['work_location'] ?? null,
                'origin' => $validated['origin'] ?? ($user->cluster ?? 'Top Coordinator Desk'),
                'priority' => $validated['priority'],
                'status' => 'not_started',
                'start_datetime' => $validated['start_datetime'] ?? null,
                'due_datetime' => $validated['due_datetime'],
                'initial_attachment' => $attachmentPath,
            ]);

            TaskHistory::create([
                'staff_task_id' => $task->id,
                'user_id' => $user->id,
                'action' => 'Task Created & Assigned (Top Coor)',
                'remarks' => "Order from Top Coordinator ({$user->name}) to {$recipient->name}",
            ]);

            Notification::create([
                'user_id' => $recipient->id,
                'title' => 'New Order from Top Coor: ' . $task->title,
                'message' => "Top Coordinator ({$user->name}) assigned you task {$task->task_code}. Due: {$task->due_datetime->format('M d, Y h:i A')}.",
                'type' => 'task_assigned',
                'link_url' => "/tasks?open_task={$task->id}",
            ]);

            $createdCount++;
        }

        AuditLog::record('Top Coor Tasks', 'Issue Order', "Top Coor {$user->name} assigned {$createdCount} tasks for {$validated['title']}");

        $msg = $createdCount > 1 
            ? "Tasks assigned to {$createdCount} Registry Officers successfully!"
            : "Task assigned successfully to {$recipients->first()->name}!";

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('top_coor.tasks.index')->with('success', $msg);
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

    public function startTask(Request $request, $id)
    {
        $user = auth()->user();
        $task = StaffTask::findOrFail($id);

        if ($task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($task->status === 'not_started') {
            $task->update([
                'status' => 'in_progress',
                'start_datetime' => $task->start_datetime ?? now(),
            ]);

            TaskHistory::create([
                'staff_task_id' => $task->id,
                'user_id' => $user->id,
                'action' => 'Started Task',
                'remarks' => "Top Coordinator {$user->name} marked task as In Progress.",
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Task {$task->task_code} is now In Progress!"]);
        }

        return back()->with('success', "Task {$task->task_code} is now In Progress!");
    }

    public function submitProof(Request $request, $id)
    {
        $user = auth()->user();
        $task = StaffTask::findOrFail($id);

        if ($task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'completion_remarks' => ['required', 'string'],
            'evidence_files' => ['nullable', 'array'],
            'evidence_files.*' => ['file', 'mimes:jpeg,png,jpg,webp,pdf,doc,docx', 'max:5120'],
        ]);

        $uploadedEvidences = $task->evidence_attachments ?? [];

        if ($request->hasFile('evidence_files')) {
            $destination = public_path('uploads/tasks');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            foreach ($request->file('evidence_files') as $file) {
                $origName = $file->getClientOriginalName();
                $filename = 'proof_' . $task->id . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move($destination, $filename);
                $uploadedEvidences[] = [
                    'name' => $origName,
                    'path' => 'uploads/tasks/' . $filename,
                    'time' => now()->toDateTimeString(),
                    'uploader' => $user->name,
                ];
            }
        }

        // NO APPROVAL NEEDED: marked directly as completed!
        $task->update([
            'status' => 'completed',
            'completion_remarks' => $validated['completion_remarks'],
            'evidence_attachments' => $uploadedEvidences,
        ]);

        TaskHistory::create([
            'staff_task_id' => $task->id,
            'user_id' => $user->id,
            'action' => 'Task Completed (Submitted Proof)',
            'remarks' => $validated['completion_remarks'],
        ]);

        // Direct notification back to whoever assigned the task
        Notification::create([
            'user_id' => $task->assigned_by,
            'title' => 'Task Completed by Top Coor: ' . $task->title,
            'message' => "{$user->name} has completed task {$task->task_code} and uploaded proof/remarks.",
            'type' => 'task_submitted',
            'link_url' => "/tasks?open_task={$task->id}",
        ]);

        AuditLog::record('Tasks', 'Submit Proof', "Top Coor {$user->name} completed task {$task->task_code}", ['task_id' => $task->id]);

        $msg = "Task {$task->task_code} completed successfully!";

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('top_coor.tasks.index', ['view' => 'assigned_to_me'])->with('success', $msg);
    }

    public function calendar()
    {
        return view('top_coor.tasks.calendar');
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
