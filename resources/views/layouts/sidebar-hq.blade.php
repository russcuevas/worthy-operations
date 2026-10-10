<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Operations Center Team Acosta 2028">
        <div class="brand-text-container">
            <span class="brand-title">HQ COMMAND</span>
            <span class="brand-subtitle">OPERATIONS CENTER</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-category">Main Menu</div>
        <a href="{{ route('hq.dashboard') }}" class="nav-link {{ request()->routeIs('hq.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Overview Dashboard</span>
        </a>

        <div class="nav-category">Task Management</div>
        <a href="{{ route('hq.tasks.index') }}" class="nav-link {{ request()->routeIs('hq.tasks.index') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i>
            <span>Task Orders</span>
            @php
                $myActiveOrders = \App\Models\StaffTask::where(function($q) {
                    $q->where('assigned_by', Auth::id())
                      ->orWhere('assigned_to', Auth::id());
                })->whereNotIn('status', ['completed', 'cancelled'])->count();
            @endphp
            @if($myActiveOrders > 0)
                <span class="nav-badge" title="Active Tasks">{{ $myActiveOrders }}</span>
            @endif
        </a>
        <a href="{{ route('hq.tasks.calendar') }}" class="nav-link {{ request()->routeIs('hq.tasks.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar-week-fill"></i>
            <span>Schedule Calendar</span>
        </a>

        <div class="nav-category">Field Network</div>
        <a href="{{ route('hq.directory.index') }}" class="nav-link {{ request()->routeIs('hq.directory.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Personnel Directory</span>
        </a>
        <a href="{{ route('hq.reports.index') }}" class="nav-link {{ request()->routeIs('hq.reports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span>Task Reports</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('profile.index') }}" class="sidebar-footer-link" title="Click to edit profile">
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-sm">
            <div class="sidebar-user-info">
                <div class="user-name-text">{{ Auth::user()->name }}</div>
                <div class="user-role-badge">HQ OFFICER</div>
            </div>
            <i class="bi bi-gear-fill" style="margin-left: auto; color: var(--color-wave-cyan); font-size: 13px; opacity: 0.8;"></i>
        </a>
    </div>
</aside>
