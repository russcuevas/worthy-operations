@extends('layouts.app')

@section('title', 'Registry Officers Directory')
@section('page-title', 'Registry Officers')
@section('page-subtitle', 'Registry Officers in ' . (Auth::user()->cluster ?? 'Coordination Desk'))

@section('content')
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-people-fill text-primary"></i>
                <span>Registry Officers ({{ $officers->count() }})</span>
            </div>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 45px;">Photo</th>
                            <th>Name & Username</th>
                            <th>Cluster</th>
                            <th>Position</th>
                            <th>Contact Phone</th>
                            <th>Accomplished Tasks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($officers as $p)
                            <tr>
                                <td><img src="{{ $p->avatar_url }}" alt="{{ $p->name }}" class="user-avatar-sm" style="width: 36px; height: 36px;"></td>
                                <td>
                                    <div style="font-weight: 700; color: var(--color-deep-navy);">{{ $p->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-light);"><code>@<span>{{ $p->username }}</span></code></div>
                                </td>
                                <td><span class="badge badge-info">{{ $p->cluster ?? 'N/A' }}</span></td>
                                <td>{{ $p->position ?? 'Registry Officer' }}</td>
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
