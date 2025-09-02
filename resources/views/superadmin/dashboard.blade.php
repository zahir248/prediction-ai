@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Super Admin Dashboard</h1>
            <p class="text-muted mb-0">System overview and management console</p>
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
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::where('role', 'admin')->count() }}</h2>
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
                            <h6 class="text-muted mb-1 fw-semibold">Total Clients</h6>
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
                                        <th class="border-0 px-3 py-3">Topic Name</th>
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
                                                {{ $prediction->user->role_with_organization }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div style="max-width: 300px; word-wrap: break-word;">
                                                {{ $prediction->topic }}
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
                                @php
                                    $recentActivities = collect();
                                    
                                    // Get recent user logins
                                    $recentLogins = \App\Models\User::whereNotNull('last_login_at')
                                        ->orderBy('last_login_at', 'desc')
                                        ->take(3)
                                        ->get()
                                        ->map(function($user) {
                                            return [
                                                'type' => 'login',
                                                'user' => $user,
                                                'time' => $user->last_login_at,
                                                'status' => 'success',
                                                'icon' => 'bi-person-check',
                                                'color' => 'primary',
                                                'action' => 'User Login'
                                            ];
                                        });
                                    
                                    // Get recent predictions
                                    $recentPredictions = \App\Models\Prediction::with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->take(3)
                                        ->get()
                                        ->map(function($prediction) {
                                            return [
                                                'type' => 'prediction',
                                                'user' => $prediction->user,
                                                'time' => $prediction->created_at,
                                                'status' => $prediction->status,
                                                'icon' => 'bi-graph-up',
                                                'color' => 'success',
                                                'action' => 'New Prediction'
                                            ];
                                        });
                                    
                                    // Get recent user creations
                                    $recentUsers = \App\Models\User::orderBy('created_at', 'desc')
                                        ->take(3)
                                        ->get()
                                        ->map(function($user) {
                                            return [
                                                'type' => 'user_created',
                                                'user' => $user,
                                                'time' => $user->created_at,
                                                'status' => 'success',
                                                'icon' => 'bi-person-plus',
                                                'color' => 'info',
                                                'action' => 'User Created'
                                            ];
                                        });
                                    
                                    // Combine and sort by time
                                    $recentActivities = $recentLogins->concat($recentPredictions)->concat($recentUsers)
                                        ->sortByDesc('time')
                                        ->take(5);
                                @endphp
                                
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-{{ $activity['color'] }} bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi {{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $activity['action'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">{{ $activity['user']->email }}</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($activity['type'] === 'prediction')
                                            <span class="badge bg-{{ $activity['status'] === 'completed' ? 'success' : ($activity['status'] === 'processing' ? 'warning' : 'secondary') }} rounded-pill">
                                                {{ ucfirst($activity['status']) }}
                                            </span>
                                        @else
                                            <span class="badge bg-success rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i>Success
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-clock-history fs-4 d-block mb-2"></i>
                                            No recent activity
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
