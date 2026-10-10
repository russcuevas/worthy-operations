@extends('layouts.app')

@section('title', 'Field Personnel Directory')
@section('page-title', 'Cluster & Field Personnel Directory')
@section('page-subtitle', 'Active Cluster Heads, Top Coordinators, and Registry Officers')

@section('content')
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-people-fill text-primary"></i>
                <span>Field Operations Personnel ({{ $personnel->count() }})</span>
            </div>
            <form action="{{ route('hq.directory.index') }}" method="GET" style="display: flex; gap: 10px;">
                <select name="role" class="form-select" onchange="this.form.submit()" style="min-width: 150px;">
                    <option value="">All Roles</option>
                    <option value="cluster_head" {{ request('role') === 'cluster_head' ? 'selected' : '' }}>Cluster Head</option>
                    <option value="top_coor" {{ request('role') === 'top_coor' ? 'selected' : '' }}>Top Coor</option>
                    <option value="registry_officer" {{ request('role') === 'registry_officer' ? 'selected' : '' }}>Registry Officer</option>
                </select>
                <select name="cluster" class="form-select" onchange="this.form.submit()" style="min-width: 150px;">
                    <option value="">All Clusters</option>
                    @foreach($clusters as $c)
                        <option value="{{ $c }}" {{ request('cluster') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
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
                            <th>Cluster</th>
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
                                <td><span class="badge badge-info">{{ $p->cluster ?? 'N/A' }}</span></td>
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
