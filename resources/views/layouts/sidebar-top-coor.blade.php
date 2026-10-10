<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Operations Center Team Acosta 2028">
        <div class="brand-text-container">
            <span class="brand-title">TOP COORDINATOR</span>
            <span class="brand-subtitle">{{ Auth::user()->cluster ?? 'COORDINATION DESK' }}</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-category">Main Menu</div>
        <a href="{{ route('top_coor.dashboard') }}" class="nav-link {{ request()->routeIs('top_coor.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Overview Dashboard</span>
        </a>

        <div class="nav-category">Tasks & Orders</div>
        <a href="{{ route('top_coor.tasks.index') }}" class="nav-link {{ request()->routeIs('top_coor.tasks.index') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i>
            <span>Task Management</span>
            @php
                $myTaskCount = \App\Models\StaffTask::where('assigned_to', Auth::id())->whereNotIn('status', ['completed', 'cancelled'])->count();
            @endphp
            @if($myTaskCount > 0)
                <span class="nav-badge" title="My Pending Tasks">{{ $myTaskCount }}</span>
            @endif
        </a>
        <a href="{{ route('top_coor.tasks.calendar') }}" class="nav-link {{ request()->routeIs('top_coor.tasks.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar-week-fill"></i>
            <span>Schedule Calendar</span>
        </a>
        <a href="{{ route('top_coor.directory.index') }}" class="nav-link {{ request()->routeIs('top_coor.directory.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Registry Officers</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('profile.index') }}" class="sidebar-footer-link" title="Click to edit profile">
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-sm">
            <div class="sidebar-user-info">
                <div class="user-name-text">{{ Auth::user()->name }}</div>
                <div class="user-role-badge">TOP COOR</div>
            </div>
            <i class="bi bi-gear-fill" style="margin-left: auto; color: var(--color-wave-cyan); font-size: 13px; opacity: 0.8;"></i>
        </a>
    </div>
</aside>
