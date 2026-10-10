<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Operations Center Team Acosta 2028">
        <div class="brand-text-container">
            <span class="brand-title">REGISTRY OFFICER</span>
            <span class="brand-subtitle">{{ Auth::user()->cluster ?? 'FIELD OPS' }}</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-category">Main Menu</div>
        <a href="{{ route('registry_officer.dashboard') }}" class="nav-link {{ request()->routeIs('registry_officer.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>My Dashboard</span>
        </a>

        <div class="nav-category">My Work</div>
        <a href="{{ route('registry_officer.tasks.index') }}" class="nav-link {{ request()->routeIs('registry_officer.tasks.*') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i>
            <span>Assigned Tasks</span>
            @php
                $pendingMyTasks = \App\Models\StaffTask::where('assigned_to', Auth::id())->whereNotIn('status', ['completed', 'cancelled'])->count();
            @endphp
            @if($pendingMyTasks > 0)
                <span class="nav-badge" title="Pending Tasks">{{ $pendingMyTasks }}</span>
            @endif
        </a>
        <a href="{{ route('registry_officer.schedule.index') }}" class="nav-link {{ request()->routeIs('registry_officer.schedule.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check-fill"></i>
            <span>My Schedule</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('profile.index') }}" class="sidebar-footer-link" title="Click to edit profile">
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-sm">
            <div class="sidebar-user-info">
                <div class="user-name-text">{{ Auth::user()->name }}</div>
                <div class="user-role-badge">REGISTRY OFFICER</div>
            </div>
            <i class="bi bi-gear-fill" style="margin-left: auto; color: var(--color-wave-cyan); font-size: 13px; opacity: 0.8;"></i>
        </a>
    </div>
</aside>
