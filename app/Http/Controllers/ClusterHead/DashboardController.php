<?php

namespace App\Http\Controllers\ClusterHead;

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

        // Tasks assigned by Cluster Head or assigned to Cluster Head
        $tasksQuery = StaffTask::with(['assignedTo', 'assignedBy'])
            ->where(function($q) use ($user) {
                $q->where('assigned_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });

        $totalTasks = (clone $tasksQuery)->count();
        $completedTasks = (clone $tasksQuery)->where('status', 'completed')->count();
        $inProgressTasks = (clone $tasksQuery)->where('status', 'in_progress')->count();
        $overdueTasks = (clone $tasksQuery)->whereNotIn('status', ['completed', 'cancelled'])->where('due_datetime', '<', Carbon::now())->count();

        // Cluster subordinates (Top Coors and Registry Officers in their cluster)
        $subordinatesQuery = User::whereIn('role', ['top_coor', 'registry_officer'])->where('status', 'active');
        if ($user->cluster) {
            $subordinatesQuery->where('cluster', $user->cluster);
        }
        $subordinatesCount = $subordinatesQuery->count();

        $recentTasks = (clone $tasksQuery)->latest()->take(8)->get();
        $recentNotifications = Notification::where('user_id', $user->id)->latest()->take(5)->get();

        return view('cluster_head.dashboard.index', compact(
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'overdueTasks',
            'subordinatesCount',
            'recentTasks',
            'recentNotifications'
        ));
    }
}
