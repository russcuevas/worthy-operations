<?php

namespace App\Http\Controllers\RegistryOfficer;

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

        // Registry officers can VIEW ONLY their own assigned tasks
        $query = StaffTask::with(['assignedBy'])
            ->where('assigned_to', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->latest('due_datetime')->get();

        $counts = [
            'all' => StaffTask::where('assigned_to', $user->id)->count(),
            'not_started' => StaffTask::where('assigned_to', $user->id)->where('status', 'not_started')->count(),
            'in_progress' => StaffTask::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
            'completed' => StaffTask::where('assigned_to', $user->id)->where('status', 'completed')->count(),
            'overdue' => StaffTask::where('assigned_to', $user->id)->whereNotIn('status', ['completed', 'cancelled'])->where('due_datetime', '<', Carbon::now())->count(),
        ];

        return view('registry_officer.tasks.index', compact('tasks', 'counts'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $task = StaffTask::with(['assignedBy', 'histories.user'])->findOrFail($id);

        // Security check: Registry officer can only view their own tasks
        if ($task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized access to this task.');
        }

        return response()->json([
            'success' => true,
            'task' => $task,
            'origin' => $task->origin ?? 'N/A',
            'work_location' => $task->work_location ?? 'N/A',
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
                'remarks' => 'Registry Officer marked task as In Progress.',
            ]);

            AuditLog::record('Tasks', 'Start Task', "Registry Officer {$user->name} started task {$task->task_code}", ['task_id' => $task->id]);
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

        // NO APPROVAL NEEDED: Auto-mark as COMPLETED!
        $task->update([
            'status' => 'completed',
            'completion_remarks' => $validated['completion_remarks'],
            'evidence_attachments' => $uploadedEvidences,
        ]);

        TaskHistory::create([
            'staff_task_id' => $task->id,
            'user_id' => $user->id,
            'action' => 'Task Completed (Submitted Remarks & Evidence)',
            'remarks' => $validated['completion_remarks'],
        ]);

        // Direct notification back to the person who gave the task
        Notification::create([
            'user_id' => $task->assigned_by,
            'title' => 'Task Completed: ' . $task->title,
            'message' => "Registry Officer {$user->name} completed task {$task->task_code} and uploaded photo evidence.",
            'type' => 'task_completed',
            'link_url' => "/tasks?open_task={$task->id}",
        ]);

        AuditLog::record('Tasks', 'Submit Proof', "Registry Officer {$user->name} completed task {$task->task_code}", ['task_id' => $task->id]);

        $msg = "Task {$task->task_code} completed and proof submitted successfully!";

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('registry_officer.tasks.index')->with('success', $msg);
    }
}
