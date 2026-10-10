<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\StaffTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Tasks assigned by HQ or involving HQ
        $createdTasksQuery = StaffTask::with(['assignedTo', 'assignedBy'])
            ->where('assigned_by', $user->id);

        $totalTasksCreated = (clone $createdTasksQuery)->count();
        $completedTasks = (clone $createdTasksQuery)->where('status', 'completed')->count();
        $inProgressTasks = (clone $createdTasksQuery)->where('status', 'in_progress')->count();
        $overdueTasks = (clone $createdTasksQuery)->whereNotIn('status', ['completed', 'cancelled'])->where('due_datetime', '<', Carbon::now())->count();

        $subordinatesCount = User::whereIn('role', ['cluster_head', 'top_coor', 'registry_officer'])->where('status', 'active')->count();

        $recentTasks = (clone $createdTasksQuery)->latest()->take(50)->get();
        $recentNotifications = Notification::where('user_id', $user->id)->latest()->take(5)->get();

        return view('hq.dashboard.index', compact(
            'totalTasksCreated',
            'completedTasks',
            'inProgressTasks',
            'overdueTasks',
            'subordinatesCount',
            'recentTasks',
            'recentNotifications'
        ));
    }
}
