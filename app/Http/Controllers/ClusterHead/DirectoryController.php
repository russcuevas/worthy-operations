<?php

namespace App\Http\Controllers\ClusterHead;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = User::whereIn('role', ['top_coor', 'registry_officer'])
            ->withCount(['assignedTasks', 'createdTasks'])
            ->withCount(['assignedTasks as completed_tasks_count' => function($q) {
                $q->where('status', 'completed');
            }]);

        if ($user->cluster) {
            $query->where('cluster', $user->cluster);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $personnel = $query->orderBy('name')->get();

        return view('cluster_head.directory.index', compact('personnel'));
    }
}
