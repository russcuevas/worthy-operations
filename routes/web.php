<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StaffDirectoryController;
use App\Http\Controllers\Admin\StaffTaskController as AdminStaffTaskController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClusterHead\DashboardController as ClusterHeadDashboardController;
use App\Http\Controllers\ClusterHead\DirectoryController as ClusterHeadDirectoryController;
use App\Http\Controllers\ClusterHead\TaskController as ClusterHeadTaskController;
use App\Http\Controllers\HQ\DashboardController as HqDashboardController;
use App\Http\Controllers\HQ\DirectoryController as HqDirectoryController;
use App\Http\Controllers\HQ\ReportController as HqReportController;
use App\Http\Controllers\HQ\TaskController as HqTaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistryOfficer\DashboardController as RegistryOfficerDashboardController;
use App\Http\Controllers\RegistryOfficer\ScheduleController as RegistryOfficerScheduleController;
use App\Http\Controllers\RegistryOfficer\TaskController as RegistryOfficerTaskController;
use App\Http\Controllers\TopCoor\DashboardController as TopCoorDashboardController;
use App\Http\Controllers\TopCoor\DirectoryController as TopCoorDirectoryController;
use App\Http\Controllers\TopCoor\TaskController as TopCoorTaskController;
use Illuminate\Support\Facades\Route;

// Redirect root to role dashboard or login
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'hq' => redirect()->route('hq.dashboard'),
            'cluster_head' => redirect()->route('cluster_head.dashboard'),
            'top_coor' => redirect()->route('top_coor.dashboard'),
            'registry_officer' => redirect()->route('registry_officer.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// Generic /tasks redirect for notifications
Route::get('/tasks', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    $qs = request()->getQueryString() ? '?' . request()->getQueryString() : '';
    return match($user->role) {
        'admin' => redirect('/admin/tasks' . $qs),
        'hq' => redirect('/hq/tasks' . $qs),
        'cluster_head' => redirect('/cluster-head/tasks' . $qs),
        'top_coor' => redirect('/top-coor/tasks' . $qs),
        'registry_officer' => redirect('/registry-officer/tasks' . $qs),
        default => redirect('/login'),
    };
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Shared Authenticated Routes (Notifications & Profile)
Route::middleware('auth')->group(function () {
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ==========================================
// 1. 627 (ADMIN) ROUTES
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Tasks Management (Single & Bulk Assignment, Evidence, Calendar)
    Route::get('/tasks', [AdminStaffTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [AdminStaffTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [AdminStaffTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{id}/cancel', [AdminStaffTaskController::class, 'cancel'])->name('tasks.cancel');
    Route::get('/calendar', [AdminStaffTaskController::class, 'calendar'])->name('tasks.calendar');
    Route::get('/calendar/events', [AdminStaffTaskController::class, 'calendarEvents'])->name('tasks.calendar.events');

    // User & Personnel Management
    Route::get('/staff', [StaffDirectoryController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffDirectoryController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}', [StaffDirectoryController::class, 'show'])->name('staff.show');
    Route::put('/staff/{id}', [StaffDirectoryController::class, 'update'])->name('staff.update');

    // Reports & Activity Logs
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
});

// ==========================================
// 2. HQ ROUTES
// ==========================================
Route::middleware(['auth', 'hq'])->prefix('hq')->name('hq.')->group(function () {
    Route::get('/dashboard', [HqDashboardController::class, 'index'])->name('dashboard');

    // Tasks Management (Orders to Cluster Head, Top Coor, Registry Officer)
    Route::get('/tasks', [HqTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [HqTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [HqTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{id}/cancel', [HqTaskController::class, 'cancel'])->name('tasks.cancel');
    Route::get('/calendar', [HqTaskController::class, 'calendar'])->name('tasks.calendar');
    Route::get('/calendar/events', [HqTaskController::class, 'calendarEvents'])->name('tasks.calendar.events');

    // Personnel Directory & Reports
    Route::get('/directory', [HqDirectoryController::class, 'index'])->name('directory.index');
    Route::get('/reports', [HqReportController::class, 'index'])->name('reports.index');
});

// ==========================================
// 3. CLUSTER HEAD ROUTES
// ==========================================
Route::middleware(['auth', 'cluster_head'])->prefix('cluster-head')->name('cluster_head.')->group(function () {
    Route::get('/dashboard', [ClusterHeadDashboardController::class, 'index'])->name('dashboard');

    // Tasks Management (Orders to Top Coor and Registry Officer)
    Route::get('/tasks', [ClusterHeadTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [ClusterHeadTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [ClusterHeadTaskController::class, 'show'])->name('tasks.show');
    Route::get('/calendar', [ClusterHeadTaskController::class, 'calendar'])->name('tasks.calendar');
    Route::get('/calendar/events', [ClusterHeadTaskController::class, 'calendarEvents'])->name('tasks.calendar.events');

    // Cluster Personnel
    Route::get('/directory', [ClusterHeadDirectoryController::class, 'index'])->name('directory.index');
});

// ==========================================
// 4. TOP COOR ROUTES
// ==========================================
Route::middleware(['auth', 'top_coor'])->prefix('top-coor')->name('top_coor.')->group(function () {
    Route::get('/dashboard', [TopCoorDashboardController::class, 'index'])->name('dashboard');

    // Tasks Management (Orders to Registry Officers + View tasks assigned to self)
    Route::get('/tasks', [TopCoorTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TopCoorTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [TopCoorTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{id}/start', [TopCoorTaskController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{id}/submit-proof', [TopCoorTaskController::class, 'submitProof'])->name('tasks.submitProof');
    Route::get('/calendar', [TopCoorTaskController::class, 'calendar'])->name('tasks.calendar');
    Route::get('/calendar/events', [TopCoorTaskController::class, 'calendarEvents'])->name('tasks.calendar.events');

    // Directory
    Route::get('/directory', [TopCoorDirectoryController::class, 'index'])->name('directory.index');
});

// ==========================================
// 5. REGISTRY OFFICER ROUTES
// ==========================================
Route::middleware(['auth', 'registry_officer'])->prefix('registry-officer')->name('registry_officer.')->group(function () {
    Route::get('/dashboard', [RegistryOfficerDashboardController::class, 'index'])->name('dashboard');

    // Own Tasks (View only own assigned tasks, start, and submit proof)
    Route::get('/tasks', [RegistryOfficerTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{id}', [RegistryOfficerTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{id}/start', [RegistryOfficerTaskController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{id}/submit-proof', [RegistryOfficerTaskController::class, 'submitProof'])->name('tasks.submitProof');

    // Schedule / Calendar
    Route::get('/schedule', [RegistryOfficerScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/events', [RegistryOfficerScheduleController::class, 'calendarEvents'])->name('schedule.events');
});
