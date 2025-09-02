@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">System Analytics Dashboard</h1>
            <p class="text-muted mb-0">Track usage, costs, and performance metrics across the entire system with comprehensive insights.</p>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 search-filter-row">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search analytics...">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <input type="date" id="start_date" name="start_date" 
                           value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <input type="date" id="end_date" name="end_date" 
                           value="{{ request('end_date', now()->format('Y-m-d')) }}"
                           class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <button type="button" class="btn btn-primary w-100" id="updateAnalytics">
                        <i class="bi bi-arrow-clockwise me-2"></i>Update Analytics
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-clipboard-data text-primary fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Analyses</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ number_format($analytics['total_analyses']) }}</h2>
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
                            <i class="bi bi-cpu text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Tokens</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ number_format($analytics['total_tokens']) }}</h2>
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
                            <i class="bi bi-currency-dollar text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Cost</h6>
                            <h2 class="mb-0 fw-bold text-dark">${{ number_format($analytics['total_cost'], 4) }}</h2>
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
                            <i class="bi bi-check-circle text-info fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Success Rate</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ number_format($analytics['success_rate'], 1) }}%</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row g-4 mb-4">
        <!-- Performance Metrics -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>
                        <span class="text-dark">Performance Metrics</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                            <span class="text-muted mb-2 mb-sm-0">Average Processing Time</span>
                            <span class="badge bg-primary rounded-pill px-3">{{ number_format($analytics['average_processing_time'], 3) }}s</span>
                        </div>
                        <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                            <span class="text-muted mb-2 mb-sm-0">Active Users</span>
                            <span class="badge bg-success rounded-pill px-3">{{ number_format($analytics['active_users']) }}</span>
                        </div>
                        <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                            <span class="text-muted mb-2 mb-sm-0">Average Cost per Analysis</span>
                            <span class="badge bg-warning rounded-pill px-3">${{ $analytics['total_analyses'] > 0 ? number_format($analytics['total_cost'] / $analytics['total_analyses'], 6) : '0.000000' }}</span>
                        </div>
                        <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                            <span class="text-muted mb-2 mb-sm-0">Average Tokens per Analysis</span>
                            <span class="badge bg-info rounded-pill px-3">{{ $analytics['total_analyses'] > 0 ? number_format($analytics['total_tokens'] / $analytics['total_analyses']) : '0' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Analysis Type Breakdown -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-pie-chart text-primary me-2"></i>
                        <span class="text-dark">Analysis Type Breakdown</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @if(!empty($analytics['analysis_type_breakdown']))
                            @foreach($analytics['analysis_type_breakdown'] as $type => $count)
                            <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                                <span class="text-muted text-capitalize mb-2 mb-sm-0">{{ str_replace('-', ' ', $type) }}</span>
                                <span class="badge bg-secondary rounded-pill px-3">{{ number_format($count) }}</span>
                            </div>
                            @endforeach
                        @else
                            <div class="list-group-item text-center py-4">
                                <span class="text-muted">No analysis types found</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Content Row -->
    <div class="row g-4">
        <!-- Top Users by Usage -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                            <i class="bi bi-trophy text-primary me-2"></i>
                            <span class="text-dark">Top Users by Usage</span>
                        </h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(\App\Models\AnalysisAnalytics::count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3">User</th>
                                        <th class="border-0 px-4 py-3">Analyses</th>
                                        <th class="border-0 px-4 py-3">Total Tokens</th>
                                        <th class="border-0 px-4 py-3">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($analytics['top_users_by_usage']))
                                        @foreach($analytics['top_users_by_usage'] as $user)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-person text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold text-dark">{{ $user['name'] }}</h6>
                                                        <small class="text-muted">{{ $user['email'] }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="badge bg-primary rounded-pill px-3">{{ number_format($user['analyses_count']) }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="fw-semibold text-dark">{{ number_format($user['total_tokens']) }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="badge bg-success rounded-pill px-3">{{ number_format($user['percentage'], 1) }}%</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <span class="text-muted">No user data available</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span class="text-muted">No analytics data available</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Organization Breakdown -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-building text-primary me-2"></i>
                        <span class="text-dark">Organization Breakdown</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @if(!empty($analytics['organization_breakdown']))
                            @foreach($analytics['organization_breakdown'] as $org => $data)
                            <div class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted fw-semibold">{{ $org ?: 'No Organization' }}</span>
                                    <span class="badge bg-secondary rounded-pill px-3">{{ number_format($data['count']) }} analyses</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Total Tokens:</small>
                                    <span class="badge bg-primary rounded-pill px-3">{{ number_format($data['total_tokens']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Total Cost:</small>
                                    <span class="badge bg-warning rounded-pill px-3">${{ number_format($data['total_cost'], 4) }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="list-group-item text-center py-4">
                                <span class="text-muted">No organization data found</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Records Table -->
    <div class="card border-0 shadow-sm mb-4 mt-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-table text-primary me-2"></i>
                    <span class="text-dark">Analytics Records</span>
                </h5>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <span class="badge bg-primary rounded-pill px-3 fs-6">
                        Total Records: {{ count($analytics['detailed_records'] ?? []) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="analyticsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-3 py-3">User</th>
                            <th class="border-0 px-3 py-3">Type</th>
                            <th class="border-0 px-3 py-3">Tokens</th>
                            <th class="border-0 px-3 py-3">Cost</th>
                            <th class="border-0 px-3 py-3">Processing Time</th>
                            <th class="border-0 px-3 py-3">Status</th>
                            <th class="border-0 px-3 py-3">Date</th>
                            <th class="border-0 px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($analytics['detailed_records']))
                            @foreach($analytics['detailed_records'] as $record)
                            <tr data-record-id="{{ $record->id }}"
                                data-prediction-horizon="{{ $record->prediction_horizon }}"
                                data-model-used="{{ $record->model_used }}"
                                data-http-status="{{ $record->http_status_code }}"
                                data-currency="{{ $record->cost_currency }}"
                                data-retry-attempts="{{ $record->retry_attempts }}"
                                data-retry-reason="{{ $record->retry_reason }}"
                                data-started-at="{{ $record->analysis_started_at ? \Carbon\Carbon::parse($record->analysis_started_at)->format('M d, Y H:i') : 'N/A' }}"
                                data-completed-at="{{ $record->analysis_completed_at ? \Carbon\Carbon::parse($record->analysis_completed_at)->format('M d, Y H:i') : 'N/A' }}"
                                data-text-length="{{ $record->input_text_length }}"
                                data-urls-count="{{ $record->scraped_urls_count }}"
                                data-successful-scrapes="{{ $record->successful_scrapes }}"
                                data-files-count="{{ $record->uploaded_files_count }}"
                                data-total-file-size="{{ $record->total_file_size_bytes }}"
                                data-api-endpoint="{{ $record->api_endpoint }}"
                                data-api-error-message="{{ $record->api_error_message }}"
                                data-user-agent="{{ $record->user_agent }}"
                                data-ip-address="{{ $record->ip_address }}">
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $record->user->name ?? 'Unknown' }}</h6>
                                            <small class="text-muted">{{ $record->user->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-success rounded-pill">{{ str_replace('-', ' ', $record->analysis_type ?? 'N/A') }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ number_format($record->total_tokens ?? 0) }}</span>
                                        <small class="text-muted">In: {{ number_format($record->input_tokens ?? 0) }} / Out: {{ number_format($record->output_tokens ?? 0) }}</small>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-warning rounded-pill">${{ number_format($record->estimated_cost ?? 0, 4) }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ number_format($record->total_processing_time ?? 0, 2) }}s</span>
                                        <small class="text-muted">API: {{ number_format($record->api_response_time ?? 0, 2) }}s</small>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    @if($record->http_status_code >= 200 && $record->http_status_code < 300)
                                    <span class="badge bg-success rounded-pill">Success</span>
                                    @elseif($record->http_status_code >= 400)
                                    <span class="badge bg-danger rounded-pill">Failed</span>
                                @else
                                    <span class="badge bg-warning rounded-pill">Processing</span>
                                @endif
                                </td>
                                <td class="px-3 py-3">
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($record->created_at)->format('M d, Y H:i') }}</small>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewAnalyticsDetails({{ $record->id }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center" style="color: #9ca3af;">
                                    <small>No detailed analytics records available</small>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if(!empty($analytics['detailed_records']) && count($analytics['detailed_records']) > 10)
            <div class="mt-3 text-center">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Scrollable Table:</strong> This table contains many columns. Use the horizontal scroll bar below to view all data fields.
                    <br><small class="mt-1 d-block">Showing {{ count($analytics['detailed_records']) }} most recent records</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Analytics Details Modal -->
<div class="modal fade" id="analyticsDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Analytics Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- User Information -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">User Information</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Name</label>
                                        <p class="mb-0" id="modalUserName">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Email</label>
                                        <p class="mb-0" id="modalUserEmail">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Context -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">User Context</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">User Agent</label>
                                        <p class="mb-0 text-wrap" id="modalUserAgent">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">IP Address</label>
                                        <p class="mb-0" id="modalIpAddress">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Details -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">Analysis Details</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Analysis Type</label>
                                        <p class="mb-0" id="modalAnalysisType">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Prediction Horizon</label>
                                        <p class="mb-0" id="modalPredictionHorizon">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Model Used</label>
                                        <p class="mb-0" id="modalModelUsed">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">HTTP Status</label>
                                        <p class="mb-0" id="modalHttpStatus">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- API Details -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">API Details</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-muted small">API Endpoint</label>
                                        <p class="mb-0" id="modalApiEndpoint">-</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-muted small">Error Message</label>
                                        <p class="mb-0 text-danger" id="modalErrorMessage">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Token & Cost Information -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">Token & Cost Information</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Input Tokens</label>
                                        <p class="mb-0" id="modalInputTokens">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Output Tokens</label>
                                        <p class="mb-0" id="modalOutputTokens">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Total Tokens</label>
                                        <p class="mb-0" id="modalTotalTokens">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Estimated Cost</label>
                                        <p class="mb-0" id="modalEstimatedCost">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Currency</label>
                                        <p class="mb-0" id="modalCurrency">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Information -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">Processing Information</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">API Response Time</label>
                                        <p class="mb-0" id="modalApiTime">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Processing Time</label>
                                        <p class="mb-0" id="modalProcessingTime">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Retry Attempts</label>
                                        <p class="mb-0" id="modalRetryAttempts">-</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-muted small">Retry Reason</label>
                                        <p class="mb-0 text-warning" id="modalRetryReason">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Started At</label>
                                        <p class="mb-0" id="modalStartedAt">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Completed At</label>
                                        <p class="mb-0" id="modalCompletedAt">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Created At</label>
                                        <p class="mb-0" id="modalCreatedAt">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Information -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">Input Information</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Text Length</label>
                                        <p class="mb-0" id="modalTextLength">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Files Count</label>
                                        <p class="mb-0" id="modalFilesCount">-</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label fw-semibold text-muted small">Total File Size</label>
                                        <p class="mb-0" id="modalTotalFileSize">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scraping Information -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">Scraping Information</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">URLs Count</label>
                                        <p class="mb-0" id="modalUrlsCount">-</p>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-muted small">Successful Scrapes</label>
                                        <p class="mb-0" id="modalSuccessfulScrapes">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.search-filter-row {
    border-radius: 8px;
    padding: 1rem;
}

.table th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid #dee2e6;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #f1f3f4;
}

.list-group-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .modal-body {
        padding: 1rem;
    }
    
    .modal-body .row.g-4 {
        gap: 1rem !important;
    }
    
    .modal-body .card-body {
        padding: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
});

function setupEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const updateButton = document.getElementById('updateAnalytics');

    searchInput.addEventListener('input', filterAnalytics);
    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
    updateButton.addEventListener('click', updateAnalyticsData);
}

function filterAnalytics() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#analyticsTable tbody tr');
    
    rows.forEach(row => {
        const userName = row.querySelector('h6').textContent.toLowerCase();
        const userEmail = row.querySelector('small').textContent.toLowerCase();
        const analysisType = row.querySelector('td:nth-child(2) .badge').textContent.toLowerCase();
        
        const matchesSearch = userName.includes(searchTerm) || 
                            userEmail.includes(searchTerm) || 
                            analysisType.includes(searchTerm);
        
        row.style.display = matchesSearch ? '' : 'none';
    });
}

function validateDates() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    
    if (startDate > endDate) {
        showNotification('Start date cannot be later than end date', 'danger');
        document.getElementById('updateAnalytics').disabled = true;
    } else {
        document.getElementById('updateAnalytics').disabled = false;
    }
}

function updateAnalyticsData() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    showNotification('Updating analytics data...', 'info');
    
    // Redirect with new date parameters
    window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
}

function viewAnalyticsDetails(recordId) {
    const row = document.querySelector(`tr[data-record-id="${recordId}"]`);
    if (!row) return;

    // User Information
    document.getElementById('modalUserName').textContent = row.querySelector('h6').textContent;
    document.getElementById('modalUserEmail').textContent = row.querySelector('small').textContent;

    // Analysis Details
    document.getElementById('modalAnalysisType').textContent = row.querySelector('td:nth-child(2) .badge').textContent;
    document.getElementById('modalPredictionHorizon').textContent = row.dataset.predictionHorizon || 'N/A';
    document.getElementById('modalModelUsed').textContent = row.dataset.modelUsed || 'N/A';
    document.getElementById('modalHttpStatus').textContent = row.dataset.httpStatus || 'N/A';
    document.getElementById('modalApiEndpoint').textContent = row.dataset.apiEndpoint || 'N/A';
    document.getElementById('modalErrorMessage').textContent = row.dataset.apiErrorMessage || 'No errors';
    
    // User Context
    document.getElementById('modalUserAgent').textContent = row.dataset.userAgent || 'N/A';
    document.getElementById('modalIpAddress').textContent = row.dataset.ipAddress || 'N/A';
    
    // Scraping Information
    document.getElementById('modalSuccessfulScrapes').textContent = row.dataset.successfulScrapes || '0';
    
    // Retry Information
    document.getElementById('modalRetryReason').textContent = row.dataset.retryReason || 'No retries needed';

    // Token & Cost Information
    const tokensCell = row.querySelector('td:nth-child(3)');
    document.getElementById('modalInputTokens').textContent = tokensCell.querySelector('small').textContent.split(' / ')[0].replace('In: ', '');
    document.getElementById('modalOutputTokens').textContent = tokensCell.querySelector('small').textContent.split(' / ')[1].replace('Out: ', '');
    document.getElementById('modalTotalTokens').textContent = tokensCell.querySelector('.fw-semibold').textContent;
    document.getElementById('modalEstimatedCost').textContent = row.querySelector('td:nth-child(4) .badge').textContent;
    document.getElementById('modalCurrency').textContent = row.dataset.currency || 'USD';

    // Processing Information
    const processingCell = row.querySelector('td:nth-child(5)');
    document.getElementById('modalApiTime').textContent = processingCell.querySelector('small').textContent.replace('API: ', '');
    document.getElementById('modalProcessingTime').textContent = processingCell.querySelector('.fw-semibold').textContent;
    document.getElementById('modalRetryAttempts').textContent = row.dataset.retryAttempts || '0';
    document.getElementById('modalStartedAt').textContent = row.dataset.startedAt || 'N/A';
    document.getElementById('modalCompletedAt').textContent = row.dataset.completedAt || 'N/A';
    document.getElementById('modalCreatedAt').textContent = row.querySelector('td:nth-child(7) small').textContent;

    // Input Information
    document.getElementById('modalTextLength').textContent = row.dataset.textLength || '0';
    document.getElementById('modalUrlsCount').textContent = row.dataset.urlsCount || '0';
    document.getElementById('modalFilesCount').textContent = row.dataset.filesCount || '0';
    document.getElementById('modalTotalFileSize').textContent = row.dataset.totalFileSize || '0';

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('analyticsDetailsModal'));
    modal.show();
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
