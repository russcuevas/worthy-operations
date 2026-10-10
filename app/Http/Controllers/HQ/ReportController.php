<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\StaffTask;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = StaffTask::with(['assignedTo', 'assignedBy'])
            ->where(function($q) use ($user) {
                $q->where('assigned_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        $tasks = $query->latest()->get();

        return view('hq.reports.index', compact('tasks'));
    }
}
