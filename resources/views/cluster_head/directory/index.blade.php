@extends('layouts.app')

@section('title', 'Cluster Personnel')
@section('page-title', 'Cluster Personnel Directory')
@section('page-subtitle', 'Active Top Coordinators and Registry Officers in ' . (Auth::user()->cluster ?? 'Cluster Area'))

@section('content')
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-people-fill text-primary"></i>
                <span>Team Members ({{ $personnel->count() }})</span>
            </div>
            <form action="{{ route('cluster_head.directory.index') }}" method="GET" style="display: flex; gap: 10px;">
                <select name="role" class="form-select" onchange="this.form.submit()" style="min-width: 150px;">
                    <option value="">All Roles</option>
                    <option value="top_coor" {{ request('role') === 'top_coor' ? 'selected' : '' }}>Top Coordinators</option>
                    <option value="registry_officer" {{ request('role') === 'registry_officer' ? 'selected' : '' }}>Registry Officers</option>
                </select>
            </form>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 45px;">Photo</th>
                            <th>Name & Username</th>
                            <th>Role</th>
                            <th>Position</th>
                            <th>Contact Phone</th>
                            <th>Tasks Stats</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personnel as $p)
                            <tr>
                                <td><img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="user-avatar-sm" style="width: 36px; height: 36px;"></td>
                                <td>
                                    <div style="font-weight: 700; color: var(--color-deep-navy);">{{ $p->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-light);"><code>@<span>{{ $p->username }}</span></code></div>
                                </td>
                                <td><span class="badge {{ $p->role_badge }}">{{ $p->role_label }}</span></td>
                                <td>{{ $p->position ?? 'Associate' }}</td>
                                <td>{{ $p->contact_number ?? '—' }}</td>
                                <td><strong>{{ $p->completed_tasks_count }}</strong> done / {{ $p->assigned_tasks_count }} total</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
