<?php

if (!function_exists('role_route')) {
    /**
     * Dynamically resolve route name based on current route prefix or user role
     */
    function role_route(string $name, array $params = [], bool $absolute = true): string
    {
        $prefix = 'admin.';

        if (request()->is('admin*')) {
            $prefix = 'admin.';
        } elseif (request()->is('hq*')) {
            $prefix = 'hq.';
        } elseif (request()->is('cluster-head*') || request()->is('cluster_head*')) {
            $prefix = 'cluster_head.';
        } elseif (request()->is('top-coor*') || request()->is('top_coor*')) {
            $prefix = 'top_coor.';
        } elseif (request()->is('registry-officer*') || request()->is('registry_officer*')) {
            $prefix = 'registry_officer.';
        } elseif (auth()->check()) {
            $user = auth()->user();
            $prefix = match($user->role) {
                'admin' => 'admin.',
                'hq' => 'hq.',
                'cluster_head' => 'cluster_head.',
                'top_coor' => 'top_coor.',
                'registry_officer' => 'registry_officer.',
                default => 'admin.',
            };
        }

        $fullRouteName = $prefix . $name;
        if (\Illuminate\Support\Facades\Route::has($fullRouteName)) {
            return route($fullRouteName, $params, $absolute);
        }

        // Fallback to admin route or direct name
        if (\Illuminate\Support\Facades\Route::has('admin.' . $name)) {
            return route('admin.' . $name, $params, $absolute);
        }

        return route($name, $params, $absolute);
    }
}
