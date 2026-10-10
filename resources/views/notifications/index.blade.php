@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'System & Duty Notifications')
@section('page-subtitle', 'Stay informed with task assignments, approvals, return notices, and critical stock threshold alerts')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-title">
                    <i class="bi bi-bell-fill text-primary"></i>
                    <span>All Notifications</span>
                </div>
                <form action="{{ route('notifications.readAll') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-wa btn-wa-outline btn-wa-sm">
                        <i class="bi bi-check2-all"></i> Mark All as Read
                    </button>
                </form>
            </div>
            <div class="wa-card-body" style="padding: 16px;">
                @forelse($notifications as $notif)
                    <div style="padding: 14px 16px; border-radius: var(--radius-sm); margin-bottom: 10px; display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; {{ $notif->is_read ? 'background: #F8FAFC; border: 1px solid var(--card-border);' : 'background: #EFF6FF; border: 1px solid #BFDBFE;' }}">
                        <div style="display: flex; gap: 12px;">
                            <div style="font-size: 20px; color: {{ $notif->is_read ? 'var(--text-light)' : 'var(--color-primary-blue)' }}; margin-top: 2px;">
                                @if($notif->type === 'task_assigned') <i class="bi bi-plus-circle-fill"></i>
                                @elseif($notif->type === 'task_approved') <i class="bi bi-check-circle-fill text-success"></i>
                                @elseif($notif->type === 'task_returned') <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                @elseif($notif->type === 'low_stock') <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                @else <i class="bi bi-info-circle-fill"></i>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 14px; color: var(--color-deep-navy);">{{ $notif->title }}</div>
                                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">{{ $notif->message }}</p>
                                <div style="font-size: 11px; color: var(--text-light); margin-top: 4px;">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if(!$notif->is_read)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn-wa btn-wa-primary btn-wa-sm">
                                        View / Open
                                    </button>
                                </form>
                            @elseif($notif->link_url)
                                <a href="{{ $notif->link_url }}" class="btn-wa btn-wa-outline btn-wa-sm">
                                    Open
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-light);">
                        <i class="bi bi-bell-slash" style="font-size: 40px; color: var(--color-wave-cyan); display: block; margin-bottom: 8px;"></i>
                        No notifications to display.
                    </div>
                @endforelse

                <div style="margin-top: 16px;">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
