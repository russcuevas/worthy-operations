<?php

namespace App\Http\Controllers\TopCoor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = User::where('role', 'registry_officer')
            ->withCount(['assignedTasks'])
            ->withCount(['assignedTasks as completed_tasks_count' => function($q) {
                $q->where('status', 'completed');
            }]);

        if ($user->cluster) {
            $query->where('cluster', $user->cluster);
        }

        $officers = $query->orderBy('name')->get();

        return view('top_coor.directory.index', compact('officers'));
    }
}
