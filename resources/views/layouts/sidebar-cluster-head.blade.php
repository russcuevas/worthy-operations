<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Operations Center Team Acosta 2028">
        <div class="brand-text-container">
            <span class="brand-title">CLUSTER HEAD</span>
            <span class="brand-subtitle">{{ Auth::user()->cluster ?? 'FIELD OPERATIONS' }}</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-category">Main Menu</div>
        <a href="{{ route('cluster_head.dashboard') }}" class="nav-link {{ request()->routeIs('cluster_head.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Cluster Dashboard</span>
        </a>

        <div class="nav-category">Cluster Operations</div>
        <a href="{{ route('cluster_head.tasks.index') }}" class="nav-link {{ request()->routeIs('cluster_head.tasks.index') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i>
            <span>Task Assignments</span>
            @php
                $clusterActive = \App\Models\StaffTask::where(function($q) {
                    $q->where('assigned_by', Auth::id())
                      ->orWhere('assigned_to', Auth::id());
                })->whereNotIn('status', ['completed', 'cancelled'])->count();
            @endphp
            @if($clusterActive > 0)
                <span class="nav-badge" title="Active Tasks">{{ $clusterActive }}</span>
            @endif
        </a>
        <a href="{{ route('cluster_head.tasks.calendar') }}" class="nav-link {{ request()->routeIs('cluster_head.tasks.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar-week-fill"></i>
            <span>Cluster Calendar</span>
        </a>
        <a href="{{ route('cluster_head.directory.index') }}" class="nav-link {{ request()->routeIs('cluster_head.directory.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Cluster Personnel</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('profile.index') }}" class="sidebar-footer-link" title="Click to edit profile">
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-sm">
            <div class="sidebar-user-info">
                <div class="user-name-text">{{ Auth::user()->name }}</div>
                <div class="user-role-badge">CLUSTER HEAD</div>
            </div>
            <i class="bi bi-gear-fill" style="margin-left: auto; color: var(--color-wave-cyan); font-size: 13px; opacity: 0.8;"></i>
        </a>
    </div>
</aside>
