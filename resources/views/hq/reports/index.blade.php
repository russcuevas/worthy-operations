@extends('layouts.app')

@section('title', 'HQ Operations Reports')
@section('page-title', 'HQ Operations Reports')
@section('page-subtitle', 'Summary and logs of directives dispatched by HQ Command')

@section('content')
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-file-earmark-bar-graph-fill text-primary"></i>
                <span>Task Dispatch Report</span>
            </div>
            <button onclick="window.print()" class="btn-wa btn-wa-outline btn-wa-sm">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Directive Title</th>
                            <th>Target Personnel</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $t)
                            <tr>
                                <td><code>{{ $t->task_code }}</code></td>
                                <td><strong>{{ $t->title }}</strong></td>
                                <td>{{ $t->assignedTo->name }} ({{ $t->assignedTo->role_label }})</td>
                                <td>{{ $t->due_datetime->format('M d, Y h:i A') }}</td>
                                <td><span class="badge {{ $t->status_badge }}">{{ $t->status_label }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
