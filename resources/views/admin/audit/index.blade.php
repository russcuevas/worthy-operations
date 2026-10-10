@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page-title', 'System Audit Trail & Security Log')
@section('page-subtitle', 'Immutable history of user logins, inventory movements, task verifications, and system modifications')

@section('content')
    <!-- Filter Bar -->
    <div class="wa-card">
        <div class="wa-card-body" style="padding: 16px 20px;">
            <form action="{{ route('admin.audit.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
                <div style="min-width: 160px;">
                    <select name="module" class="form-select" onchange="this.form.submit()">
                        <option value="">All Modules</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 180px;">
                    <select name="user_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    <span style="color: var(--text-muted); font-size: 12px;">to</span>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    <button type="submit" class="btn-wa btn-wa-primary btn-wa-sm">Filter</button>
                </div>

                @if(request()->hasAny(['module', 'user_id', 'date_from', 'date_to']))
                    <a href="{{ route('admin.audit.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-shield-check text-primary"></i>
                <span>Audit Logs History ({{ $logs->count() }} Entries)</span>
            </div>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Timestamp</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; font-size: 12.5px; color: var(--color-deep-navy);">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </div>
                                    <div style="font-size: 11px; color: var(--text-light);">
                                        {{ $log->created_at->format('h:i:s A') }} ({{ $log->created_at->diffForHumans() }})
                                    </div>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div style="font-weight: 600; font-size: 13px; color: var(--color-deep-navy);">{{ $log->user->name }}</div>
                                        <div style="font-size: 11px; color: var(--text-light);">@<span>{{ $log->user->username }}</span> &bull; {{ $log->user->role }}</div>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 12px;">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $log->module }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <div style="font-size: 13px; font-weight: 500; color: var(--text-main); line-height: 1.45;">
                                        {{ $log->description }}
                                    </div>
                                    @if(!empty($log->formatted_payload))
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px;">
                                            @foreach($log->formatted_payload as $item)
                                                <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(30, 41, 59, 0.04); border: 1px solid var(--border-color, #e2e8f0); border-radius: 5px; padding: 2px 8px; font-size: 11px;">
                                                    <span style="color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 9.5px; letter-spacing: 0.3px;">
                                                        {{ $item['label'] }}:
                                                    </span>
                                                    <span style="color: var(--color-deep-navy); font-weight: 600; font-family: monospace; font-size: 11.5px;">
                                                        {{ $item['value'] }}
                                                    </span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <code style="font-size: 11.5px;">{{ $log->ip_address ?? '127.0.0.1' }}</code>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
