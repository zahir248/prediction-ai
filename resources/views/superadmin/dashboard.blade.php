@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Super Admin Dashboard</h1>
            <p class="text-muted mb-0">System overview and management console</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-download me-2"></i>Export Report
            </button>
            <button class="btn btn-danger btn-sm">
                <i class="bi bi-plus-circle me-2"></i>Quick Action
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-shield-check text-danger fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Admins</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::whereIn('role', ['admin', 'superadmin'])->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-people text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Users</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::where('role', 'user')->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-graph-up text-info fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Predictions</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\Prediction::count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-event text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Active Today</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::whereDate('last_login_at', today())->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-check text-primary fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Today's Predictions</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\Prediction::whereDate('created_at', today())->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and System Status -->
    <div class="row g-3 mb-4">
        <!-- Quick Actions -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-lightning text-warning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <a href="{{ route('superadmin.admins.index') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-shield-lock text-danger fs-2 mb-2"></i>
                                <span class="fw-semibold">Manage Admins</span>
                                <small class="text-muted">Add, edit, or remove admin users</small>
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-person-badge text-success fs-2 mb-2"></i>
                                <span class="fw-semibold">Manage Clients</span>
                                <small class="text-muted">View and manage client accounts</small>
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a href="{{ route('superadmin.settings') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-gear text-info fs-2 mb-2"></i>
                                <span class="fw-semibold">System Settings</span>
                                <small class="text-muted">Configure system parameters</small>
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a href="{{ route('superadmin.logs') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-journal-text text-warning fs-2 mb-2"></i>
                                <span class="fw-semibold">System Logs</span>
                                <small class="text-muted">Monitor system activity</small>
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a href="{{ route('superadmin.predictions.index') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                <i class="bi bi-graph-up text-danger fs-2 mb-2"></i>
                                <span class="fw-semibold">View Predictions</span>
                                <small class="text-muted">Monitor all predictions</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-activity text-success me-2"></i>System Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Database</span>
                            <span class="badge bg-success rounded-pill">
                                <i class="bi bi-check-circle me-1"></i>Online
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Cache</span>
                            <span class="badge bg-success rounded-pill">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Queue</span>
                            <span class="badge bg-warning rounded-pill">
                                <i class="bi bi-exclamation-triangle me-1"></i>Pending
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Storage</span>
                            <span class="badge bg-success rounded-pill">
                                <i class="bi bi-check-circle me-1"></i>Available
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Predictions and System Activity -->
    <div class="row g-4">
        <!-- Recent Predictions -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-graph-up text-danger me-2"></i>Recent Predictions
                        </h5>
                        <a href="{{ route('superadmin.predictions.index') }}" class="btn btn-outline-danger btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(\App\Models\Prediction::count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 px-3 py-3">User</th>
                                        <th class="border-0 px-3 py-3">Role</th>
                                        <th class="border-0 px-3 py-3">Prediction</th>
                                        <th class="border-0 px-3 py-3">Date</th>
                                        <th class="border-0 px-3 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Prediction::with('user')->latest()->take(5)->get() as $prediction)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-person text-danger"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $prediction->user->name }}</h6>
                                                    <small class="text-muted">{{ $prediction->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="badge bg-{{ $prediction->user->role === 'superadmin' ? 'danger' : ($prediction->user->role === 'admin' ? 'primary' : 'secondary') }} rounded-pill">
                                                {{ ucfirst($prediction->user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-truncate" style="max-width: 200px;">
                                                {{ $prediction->prediction_text }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="text-muted">{{ $prediction->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="badge bg-success rounded-pill">Completed</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-graph-up text-danger fs-1"></i>
                            </div>
                            <h6 class="text-muted mb-2">No predictions yet</h6>
                            <p class="text-muted small mb-0">When users start making predictions, they'll appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Activity -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-clock-history text-danger me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 px-3 py-3">Action</th>
                                    <th class="border-0 px-3 py-3">User</th>
                                    <th class="border-0 px-3 py-3">Time</th>
                                    <th class="border-0 px-3 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-person-plus text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">User Login</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">admin@example.com</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">2 minutes ago</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-success rounded-pill">Success</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-gear text-warning"></i>
                                            </div>
                                            <span class="fw-semibold">Settings Update</span>
                                        </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">superadmin@example.com</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">15 minutes ago</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-success rounded-pill">Success</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-database text-info"></i>
                                            </div>
                                            <span class="fw-semibold">Database Backup</span>
                                        </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">System</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">1 hour ago</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-success rounded-pill">Success</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-heart-pulse text-danger me-2"></i>System Health
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">CPU Usage</span>
                                <span class="fw-semibold">45%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 45%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Memory Usage</span>
                                <span class="fw-semibold">62%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 62%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Disk Space</span>
                                <span class="fw-semibold">78%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: 78%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Network</span>
                                <span class="fw-semibold">23%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 23%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for dashboard */
@media (max-width: 576px) {
    .quick-actions .btn {
        padding: 1rem 0.75rem;
    }
    
    .quick-actions .btn i {
        font-size: 1.5rem !important;
    }
    
    .quick-actions .btn span {
        font-size: 0.875rem;
    }
    
    .quick-actions .btn small {
        font-size: 0.75rem;
    }
}

@media (max-width: 768px) {
    .stats-card h2 {
        font-size: 1.5rem;
    }
    
    .stats-card h6 {
        font-size: 0.875rem;
    }
    
    .quick-actions .col-12.col-sm-6 {
        margin-bottom: 0.75rem;
    }
    
    .quick-actions .col-12.col-sm-6:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 992px) {
    .system-status .card-body {
        padding: 1rem;
    }
    
    .system-health .card-body {
        padding: 1rem;
    }
}
</style>
@endsection
