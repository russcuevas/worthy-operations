<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Operations Center Team Acosta 2028') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- FullCalendar CSS (for schedules) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

    <!-- Custom Worthy Acosta Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Mobile Overlay -->
        <div class="sidebar-overlay"></div>

        <!-- Sidebar Navigation -->
        @if(Auth::check() && Auth::user()->isAdmin())
            @include('layouts.sidebar-admin')
        @elseif(Auth::check() && Auth::user()->isHQ())
            @include('layouts.sidebar-hq')
        @elseif(Auth::check() && Auth::user()->isClusterHead())
            @include('layouts.sidebar-cluster-head')
        @elseif(Auth::check() && Auth::user()->isTopCoor())
            @include('layouts.sidebar-top-coor')
        @else
            @include('layouts.sidebar-registry-officer')
        @endif

        <!-- Main Content Area -->
        <main class="app-main">
            <!-- App Header -->
            <header class="app-header">
                <div class="header-left">
                    <button type="button" class="sidebar-toggle-btn" aria-label="Toggle Sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="header-title-box">
                        <h1 class="page-breadcrumb-title" title="@yield('page-title', 'Dashboard')">@yield('page-title', 'Dashboard')</h1>
                        <p class="page-breadcrumb-sub">@yield('page-subtitle', 'Operations Center Team Acosta 2028')</p>
                    </div>
                </div>

                <div class="header-right">
                    @php
                        $unreadNotificationsCount = Auth::user() ? Auth::user()->notifications()->where('is_read', false)->count() : 0;
                    @endphp
                    <div class="notification-dropdown-wrapper">
                        <button type="button" class="header-icon-btn" id="notificationDropdownBtn" title="Notifications" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="notification-count-dot" id="notifBadge" style="{{ $unreadNotificationsCount > 0 ? '' : 'display: none;' }}">
                                {{ $unreadNotificationsCount }}
                            </span>
                        </button>

                        <div class="notification-dropdown-menu" id="notificationDropdownMenu">
                            <div class="notif-dropdown-header">
                                <div class="notif-dropdown-title">
                                    <i class="bi bi-bell-fill text-primary"></i>
                                    <span>Notifications</span>
                                </div>
                                <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" id="markAllReadBtn" style="padding: 3px 8px; font-size: 11px;">
                                    Mark All Read
                                </button>
                            </div>
                            <div class="notif-dropdown-body" id="notifDropdownBody">
                                <div style="padding: 24px; text-align: center; color: var(--text-light);">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Loading notifications...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Profile Link Badge -->
                    <a href="{{ route('profile.index') }}" class="header-user-badge-link" title="Edit Profile">
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-sm">
                        <div class="header-user-text">
                            <div style="font-weight: 700; font-size: 13px; color: var(--color-deep-navy); white-space: nowrap;">{{ Auth::user()->name }}</div>
                            <div style="font-size: 11px; color: var(--color-primary-blue); font-weight: 600; text-transform: uppercase;">{{ Auth::user()->role }}</div>
                        </div>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="header-icon-btn" title="Log Out" style="color: var(--danger); background: var(--danger-bg);">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Body View Content -->
            <div class="app-body">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Toast Notifications Container -->
    <div class="toast-container"></div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        // Trigger server session flash toasts
        @if(session('success'))
            showToast('Success', "{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast('Error', "{{ session('error') }}", 'danger');
        @endif
        @if(session('info'))
            showToast('Notice', "{{ session('info') }}", 'info');
        @endif
        @if(session('warning'))
            showToast('Warning', "{{ session('warning') }}", 'warning');
        @endif
    </script>

    @stack('scripts')
</body>
</html>
