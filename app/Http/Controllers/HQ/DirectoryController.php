<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['cluster_head', 'top_coor', 'registry_officer'])
            ->withCount(['assignedTasks', 'createdTasks'])
            ->withCount(['assignedTasks as completed_tasks_count' => function($q) {
                $q->where('status', 'completed');
            }]);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        $personnel = $query->orderBy('name')->get();
        $clusters = User::whereNotNull('cluster')->where('cluster', '!=', '')->distinct()->pluck('cluster')->sort()->values();

        return view('hq.directory.index', compact('personnel', 'clusters'));
    }
}
