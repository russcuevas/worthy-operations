<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Operations Center Team Acosta 2028">
        <div class="brand-text-container">
            <span class="brand-title">627 OPERATIONS</span>
            <span class="brand-subtitle">ADMIN MANAGEMENT</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-category">Command Center</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Overview Dashboard</span>
        </a>

        <div class="nav-category">Tasks & Operations</div>
        <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.index') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i>
            <span>Task Assignments</span>
            @php
                $pendingCount = \App\Models\StaffTask::whereNotIn('status', ['completed', 'cancelled'])->count();
            @endphp
            @if($pendingCount > 0)
                <span class="nav-badge" title="Pending / In Progress">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.tasks.calendar') }}" class="nav-link {{ request()->routeIs('admin.tasks.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar-week-fill"></i>
            <span>Schedule Calendar</span>
        </a>
        <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>User & Personnel Management</span>
        </a>

        <div class="nav-category">Analytics & Logs</div>
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span>Reports & Exports</span>
        </a>
        <a href="{{ route('admin.audit.index') }}" class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i>
            <span>Audit Trail Log</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('profile.index') }}" class="sidebar-footer-link" title="Click to edit profile">
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-sm">
            <div class="sidebar-user-info">
                <div class="user-name-text">{{ Auth::user()->name }}</div>
                <div class="user-role-badge">627 (ADMIN)</div>
            </div>
            <i class="bi bi-gear-fill" style="margin-left: auto; color: var(--color-wave-cyan); font-size: 13px; opacity: 0.8;"></i>
        </a>
    </div>
</aside>
