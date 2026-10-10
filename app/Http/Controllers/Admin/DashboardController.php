<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\StaffTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        $totalTasks = StaffTask::count();
        $completedTasks = StaffTask::where('status', 'completed')->count();
        $inProgressTasks = StaffTask::where('status', 'in_progress')->count();
        $notStartedTasks = StaffTask::where('status', 'not_started')->count();
        $overdueTasks = StaffTask::whereNotIn('status', ['completed', 'cancelled'])
            ->where('due_datetime', '<', Carbon::now())
            ->count();

        // Personnel Metrics
        $totalUsers = User::where('status', 'active')->count();
        $roleBreakdown = [
            'admin' => User::where('role', 'admin')->where('status', 'active')->count(),
            'hq' => User::where('role', 'hq')->where('status', 'active')->count(),
            'cluster_head' => User::where('role', 'cluster_head')->where('status', 'active')->count(),
            'top_coor' => User::where('role', 'top_coor')->where('status', 'active')->count(),
            'registry_officer' => User::where('role', 'registry_officer')->where('status', 'active')->count(),
        ];

        // Cluster Tasks Distribution
        $clusterStats = StaffTask::select('cluster', DB::raw('count(*) as total'), DB::raw('sum(case when status = "completed" then 1 else 0 end) as completed'))
            ->whereNotNull('cluster')
            ->groupBy('cluster')
            ->get();

        // Recent Task Stream
        $recentTasks = StaffTask::with(['assignedTo', 'assignedBy'])
            ->latest('updated_at')
            ->take(50)
            ->get();

        // Recent Audit Logs
        $recentLogs = AuditLog::with('user')->latest()->take(6)->get();

        return view('admin.dashboard.index', compact(
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'notStartedTasks',
            'overdueTasks',
            'totalUsers',
            'roleBreakdown',
            'clusterStats',
            'recentTasks',
            'recentLogs'
        ));
    }
}
