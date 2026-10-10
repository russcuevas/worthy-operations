<?php

namespace App\Http\Controllers\TopCoor;

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

        // Tasks assigned BY this Top Coor to Registry Officers
        $givenTasksQuery = StaffTask::with(['assignedTo'])
            ->where('assigned_by', $user->id);

        $totalGivenTasks = (clone $givenTasksQuery)->count();
        $completedGivenTasks = (clone $givenTasksQuery)->where('status', 'completed')->count();
        $inProgressGivenTasks = (clone $givenTasksQuery)->where('status', 'in_progress')->count();

        // Tasks assigned TO this Top Coor (by 627 / HQ / Cluster Head)
        $myAssignedTasksQuery = StaffTask::with(['assignedBy'])
            ->where('assigned_to', $user->id);

        $totalMyTasks = (clone $myAssignedTasksQuery)->count();
        $myPendingTasks = (clone $myAssignedTasksQuery)->whereNotIn('status', ['completed', 'cancelled'])->count();

        // Registry officers in cluster
        $officersQuery = User::where('role', 'registry_officer')->where('status', 'active');
        if ($user->cluster) {
            $officersQuery->where('cluster', $user->cluster);
        }
        $officersCount = $officersQuery->count();

        $recentTasks = StaffTask::with(['assignedTo', 'assignedBy'])
            ->where(function($q) use ($user) {
                $q->where('assigned_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            })
            ->latest()
            ->take(50)
            ->get();

        $recentNotifications = Notification::where('user_id', $user->id)->latest()->take(5)->get();

        return view('top_coor.dashboard.index', compact(
            'totalGivenTasks',
            'completedGivenTasks',
            'inProgressGivenTasks',
            'totalMyTasks',
            'myPendingTasks',
            'officersCount',
            'recentTasks',
            'recentNotifications'
        ));
    }
}
