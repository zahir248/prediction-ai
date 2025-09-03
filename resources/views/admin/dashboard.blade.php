@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your system.</p>
        </div>

    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-people text-primary fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Clients</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::where('role', 'user')->where('organization', Auth::user()->organization)->count() }}</h2>
                            <small class="text-success">
                                <i class="bi bi-arrow-up me-1"></i>12% from last month
                            </small>
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
                            <i class="bi bi-graph-up text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Predictions</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\Prediction::whereHas('user', function($query) { $query->where('role', 'user')->where('organization', Auth::user()->organization); })->count() }}</h2>
                            <small class="text-success">
                                <i class="bi bi-arrow-up me-1"></i>8% from last month
                            </small>
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
                            <i class="bi bi-calendar-event text-info fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Today's Predictions</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\Prediction::whereHas('user', function($query) { $query->where('role', 'user')->where('organization', Auth::user()->organization); })->whereDate('created_at', today())->count() }}</h2>
                            <small class="text-info">New today</small>
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
                            <i class="bi bi-shield-check text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Client Limit</h6>
                            <h2 class="mb-0 fw-bold text-dark">
                                @if($stats['client_limit'])
                                    {{ $stats['current_client_count'] }}/{{ $stats['client_limit'] }}
                                @else
                                    {{ $stats['current_client_count'] }}/∞
                                @endif
                            </h2>
                            <small class="text-{{ $stats['can_create_more'] ? 'success' : 'danger' }}">
                                @if($stats['can_create_more'])
                                    <i class="bi bi-check-circle me-1"></i>{{ $stats['remaining_slots'] }} slots left
                                @else
                                    <i class="bi bi-x-circle me-1"></i>Limit reached
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Main Content Area -->
    <div class="row g-4">
        <!-- Recent Predictions -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-clock-history text-primary me-2"></i>Recent Predictions
                        </h5>
                        <a href="{{ route('admin.predictions.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(\App\Models\Prediction::whereHas('user', function($query) { $query->where('role', 'user')->where('organization', Auth::user()->organization); })->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 px-3 py-3">Client</th>
                                        <th class="border-0 px-3 py-3">Topic Name</th>
                                        <th class="border-0 px-3 py-3">Date</th>
                                        <th class="border-0 px-3 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Prediction::with('user')->whereHas('user', function($query) { $query->where('role', 'user')->where('organization', Auth::user()->organization); })->latest()->take(5)->get() as $prediction)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-person text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $prediction->user->name }}</h6>
                                                    <small class="text-muted">{{ $prediction->user->email }}</small>
                                                </div>
                                            </div>
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
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-graph-up text-primary fs-1"></i>
                            </div>
                            <h6 class="text-muted mb-2">No predictions yet</h6>
                            <p class="text-muted small mb-0">When clients start making predictions, they'll appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-people text-primary me-2"></i>Recent Clients
                        </h5>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach(\App\Models\User::where('role', 'user')->where('organization', Auth::user()->organization)->latest()->take(5)->get() as $user)
                        <div class="list-group-item border-0 px-3 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                    <small class="text-muted d-block">{{ $user->email }}</small>
                                </div>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'superadmin' ? 'danger' : 'secondary') }} rounded-pill">
                                    {{ $user->role_with_organization }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for admin dashboard */
@media (max-width: 576px) {
    .stats-card h2 {
        font-size: 1.5rem;
    }
    
    .stats-card h6 {
        font-size: 0.875rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

@media (max-width: 768px) {
    .quick-actions-grid .col-12.col-sm-6.col-lg-3 {
        margin-bottom: 0.75rem;
    }
    
    .quick-actions-grid .col-12.col-sm-6.col-lg-3:last-child {
        margin-bottom: 0;
    }
}
</style>
@endsection
