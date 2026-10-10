@extends('layouts.app')

@section('title', 'Operations Reports')
@section('page-title', 'Task & Operations Reports')
@section('page-subtitle', 'Generate operational metrics, export CSV accomplishments, and print mission reports')

@section('content')
    <!-- Report Type Selector & Filters -->
    <div class="wa-card">
        <div class="wa-card-body" style="padding: 16px 20px;">
            <form action="{{ route('admin.reports.index') }}" method="GET">
                <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
                    <div style="flex: 1; min-width: 180px;">
                        <label class="form-label" style="font-size: 11px;">Report Type</label>
                        <select name="type" class="form-select">
                            <option value="operations_summary" {{ $reportType === 'operations_summary' ? 'selected' : '' }}>All Operations Directives</option>
                            <option value="completed_only" {{ $reportType === 'completed_only' ? 'selected' : '' }}>Completed Tasks (Accomplished)</option>
                            <option value="overdue_only" {{ $reportType === 'overdue_only' ? 'selected' : '' }}>Overdue Tasks</option>
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 160px;">
                        <label class="form-label" style="font-size: 11px;">Cluster</label>
                        <select name="cluster" class="form-select">
                            <option value="">All Clusters</option>
                            @foreach($clusters as $c)
                                <option value="{{ $c }}" {{ request('cluster') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 180px;">
                        <label class="form-label" style="font-size: 11px;">Personnel</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">All Personnel</option>
                            @foreach($personnel as $p)
                                <option value="{{ $p->id }}" {{ request('assigned_to') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->role_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="min-width: 140px;">
                        <label class="form-label" style="font-size: 11px;">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>

                    <div style="min-width: 140px;">
                        <label class="form-label" style="font-size: 11px;">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn-wa btn-wa-primary">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn-wa btn-wa-outline">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-file-earmark-bar-graph text-primary"></i>
                <span>Generated Report ({{ count($data['tasks']) }} Tasks Found)</span>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.reports.print', request()->all()) }}" target="_blank" class="btn-wa btn-wa-outline btn-wa-sm">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="{{ route('admin.reports.export', request()->all()) }}" class="btn-wa btn-wa-success btn-wa-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Directive Title</th>
                            <th>Assigned By (Tagged)</th>
                            <th>Assigned To</th>
                            <th>Cluster</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Evidence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['tasks'] as $t)
                            <tr>
                                <td><code>{{ $t->task_code }}</code></td>
                                <td><strong>{{ $t->title }}</strong></td>
                                <td>
                                    <div>{{ $t->assignedBy->name }}</div>
                                    <span class="badge {{ $t->assignedBy->role_badge }}" style="font-size: 10px;">{{ $t->assignedBy->role_label }}</span>
                                </td>
                                <td>
                                    <div>{{ $t->assignedTo->name }}</div>
                                    <span style="font-size: 10.5px; color: var(--text-light);">{{ $t->assignedTo->role_label }}</span>
                                </td>
                                <td><span class="badge badge-info">{{ $t->cluster ?? 'N/A' }}</span></td>
                                <td>{{ $t->due_datetime->format('M d, Y h:i A') }}</td>
                                <td><span class="badge {{ $t->status_badge }}">{{ $t->status_label }}</span></td>
                                <td>
                                    @if(!empty($t->evidence_attachments))
                                        <span class="badge badge-success">{{ count($t->evidence_attachments) }} Photos</span>
                                    @else
                                        <span style="font-size: 11px; color: var(--text-light);">None</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
