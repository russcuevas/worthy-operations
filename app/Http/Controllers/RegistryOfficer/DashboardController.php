<?php

namespace App\Http\Controllers\RegistryOfficer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\StaffTask;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $tasksQuery = StaffTask::with(['assignedBy'])
            ->where('assigned_to', $user->id);

        $totalTasks = (clone $tasksQuery)->count();
        $completedTasks = (clone $tasksQuery)->where('status', 'completed')->count();
        $inProgressTasks = (clone $tasksQuery)->where('status', 'in_progress')->count();
        $notStartedTasks = (clone $tasksQuery)->where('status', 'not_started')->count();
        $overdueTasks = (clone $tasksQuery)->whereNotIn('status', ['completed', 'cancelled'])->where('due_datetime', '<', Carbon::now())->count();

        $recentTasks = (clone $tasksQuery)->latest('due_datetime')->take(6)->get();
        $recentNotifications = Notification::where('user_id', $user->id)->latest()->take(5)->get();

        return view('registry_officer.dashboard.index', compact(
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'notStartedTasks',
            'overdueTasks',
            'recentTasks',
            'recentNotifications'
        ));
    }
}
