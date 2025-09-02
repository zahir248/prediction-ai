@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Prediction Management</h1>
            <p class="text-muted mb-0">Monitor and analyze all predictions in the system</p>
        </div>

    </div>

    <!-- Prediction Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-graph-up text-danger fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Predictions</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="totalPredictions">{{ $predictions->total() }}</h2>
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
                            <i class="bi bi-calendar-check text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Today's Predictions</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="todayPredictions">{{ $predictions->where('created_at', '>=', today())->count() }}</h2>
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
                            <i class="bi bi-clock text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">This Week</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="weekPredictions">{{ $predictions->where('created_at', '>=', now()->startOfWeek())->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 search-filter-row">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search predictions...">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="userFilter">
                        <option value="">All Users</option>
                        @foreach($predictions->unique('user_id')->take(10) as $prediction)
                            <option value="{{ $prediction->user->id }}">{{ $prediction->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="dateFilter">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-outline-danger w-100" id="clearFilters">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Predictions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-graph-up text-danger me-2"></i>All Predictions
                </h5>
                                 <div class="d-flex gap-2 mt-2 mt-sm-0">
                     
                 </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="predictionsTable">
                                         <thead class="table-light">
                                                 <tr>
                            <th class="border-0 px-3 py-3">User</th>
                           <th class="border-0 px-3 py-3">Role</th>
                                                       <th class="border-0 px-3 py-3">Topic Name</th>
                            <th class="border-0 px-3 py-3">Target</th>
                            <th class="border-0 px-3 py-3">Horizon</th>
                           <th class="border-0 px-3 py-3">Date</th>
                           <th class="border-0 px-3 py-3">Status</th>
                           <th class="border-0 px-3 py-3">Actions</th>
                       </tr>
                    </thead>
                    <tbody id="predictionsTableBody">
                        @forelse($predictions as $prediction)
                                                         <tr data-prediction-id="{{ $prediction->id }}" data-user-id="{{ $prediction->user->id }}" data-user-role="{{ $prediction->user->role }}">
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
                                    <div style="max-width: 300px; word-wrap: break-word;">
                                        {{ $prediction->target ?: 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-info rounded-pill">{{ ucwords(str_replace('_', ' ', $prediction->prediction_horizon)) }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <small class="text-muted">{{ $prediction->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-success rounded-pill">Completed</span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-danger btn-sm" onclick="viewPredictionDetails({{ $prediction->id }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deletePrediction({{ $prediction->id }})" title="Delete Prediction">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                                                         <tr>
                                 <td colspan="8" class="text-center py-5">
                                     <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                         <i class="bi bi-graph-up text-danger fs-1"></i>
                                     </div>
                                     <h6 class="text-muted mb-2">No predictions found</h6>
                                     <p class="text-muted small mb-0">Try adjusting your filters or refresh the page</p>
                                 </td>
                             </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($predictions->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Predictions pagination">
                {{ $predictions->links() }}
            </nav>
        </div>
    @endif
</div>

<!-- Prediction Details Modal -->
<div class="modal fade" id="predictionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Prediction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">User Name</label>
                        <p class="mb-0" id="modalUserName">User Name</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">User Email</label>
                        <p class="mb-0" id="modalUserEmail">user@example.com</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">User Role</label>
                        <p class="mb-0" id="modalUserRole">User</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Created Date</label>
                        <p class="mb-0" id="modalCreatedDate">Jan 1, 2024</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Topic Name</label>
                        <p class="mb-0" id="modalPredictionText">Topic name here...</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Target</label>
                        <p class="mb-0" id="modalPredictionTarget">Target here...</p>
                    </div>
                </div>
            </div>
                         <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
             </div>
        </div>
    </div>
</div>

<!-- Delete Prediction Modal -->
<div class="modal fade" id="deletePredictionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Prediction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    </div>
                    <h6 class="text-danger mb-2">Are you sure?</h6>
                    <p class="text-muted mb-0">You are about to delete the prediction: <strong id="deletePredictionTopic">Topic Name</strong></p>
                    <p class="text-muted small mt-2">This action cannot be undone and will permanently remove the prediction from the system.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeletePrediction()">
                    <i class="bi bi-trash me-2"></i>Delete Prediction
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for superadmin predictions */
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
    
    .table-responsive .table td,
    .table-responsive .table th {
        padding: 0.5rem 0.375rem;
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 768px) {
    .search-filter-row .col-12.col-md-4,
    .search-filter-row .col-12.col-md-3,
    .search-filter-row .col-12.col-md-2 {
        margin-bottom: 0.75rem;
    }
    
    .search-filter-row .col-12.col-md-2:last-child {
        margin-bottom: 0;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}

/* Pagination active state */
.pagination .page-item.active .page-link {
    background-color: #dc2626;
    border-color: #dc2626;
}

.pagination .page-link:hover {
    color: #dc2626;
}
</style>

<script>
let currentPredictionId = null;

 document.addEventListener('DOMContentLoaded', function() {
     setupEventListeners();
 });

 function setupEventListeners() {
     const searchInput = document.getElementById('searchInput');
     const userFilter = document.getElementById('userFilter');
     const dateFilter = document.getElementById('dateFilter');
     const clearFilters = document.getElementById('clearFilters');

     searchInput.addEventListener('input', filterPredictions);
     userFilter.addEventListener('change', filterPredictions);
     dateFilter.addEventListener('change', filterPredictions);
     clearFilters.addEventListener('click', clearAllFilters);
 }

function filterPredictions() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const userFilter = document.getElementById('userFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#predictionsTableBody tr');
    
    rows.forEach(row => {
        const userName = row.querySelector('h6').textContent.toLowerCase();
        const userEmail = row.querySelector('small').textContent.toLowerCase();
        const topicName = row.querySelector('td:nth-child(3) div').textContent.toLowerCase();
        const userId = row.dataset.userId;
        const createdDate = row.querySelector('td:nth-child(5) small').textContent;
        
        const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm) || topicName.includes(searchTerm);
        const matchesUser = !userFilter || userId === userFilter;
        const matchesDate = !dateFilter || matchesDateFilter(createdDate, dateFilter);
        
        row.style.display = matchesSearch && matchesUser && matchesDate ? '' : 'none';
    });
}

function matchesDateFilter(createdDate, filter) {
    const date = new Date(createdDate);
    const now = new Date();
    
    switch(filter) {
        case 'today':
            return date.toDateString() === now.toDateString();
        case 'week':
            const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
            return date >= weekAgo;
        case 'month':
            const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
            return date >= monthAgo;
        default:
            return true;
    }
}

function clearAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('userFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    const rows = document.querySelectorAll('#predictionsTableBody tr');
    rows.forEach(row => row.style.display = '');
}



function viewPredictionDetails(predictionId) {
    const predictionRow = document.querySelector(`tr[data-prediction-id="${predictionId}"]`);
    if (!predictionRow) return;
    
    const userName = predictionRow.querySelector('h6').textContent;
    const userEmail = predictionRow.querySelector('small').textContent;
    const userRole = predictionRow.querySelector('td:nth-child(2) .badge').textContent;
    const createdDate = predictionRow.querySelector('td:nth-child(6) small').textContent;
    const topicName = predictionRow.querySelector('td:nth-child(3) div').textContent;
    const target = predictionRow.querySelector('td:nth-child(4) div').textContent;
    
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalUserEmail').textContent = userEmail;
    document.getElementById('modalUserRole').textContent = userRole;
    document.getElementById('modalCreatedDate').textContent = createdDate;
    document.getElementById('modalPredictionText').textContent = topicName;
    document.getElementById('modalPredictionTarget').textContent = target;
    
    currentPredictionId = predictionId;
    
    const modal = new bootstrap.Modal(document.getElementById('predictionDetailsModal'));
    modal.show();
}



let currentDeletePredictionId = null;

function deletePrediction(predictionId) {
    // Get prediction data and populate delete modal
    const predictionRow = document.querySelector(`tr[data-prediction-id="${predictionId}"]`);
    if (predictionRow) {
        const topicName = predictionRow.querySelector('td:nth-child(3) div').textContent;
        
        // Populate delete modal
        document.getElementById('deletePredictionTopic').textContent = topicName;
        
        // Store prediction ID for delete functionality
        currentDeletePredictionId = predictionId;
        
        // Open delete modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deletePredictionModal'));
        deleteModal.show();
    }
}

function confirmDeletePrediction() {
    if (currentDeletePredictionId) {
        // Simulate deleting prediction (replace with actual API call)
        showNotification('Deleting prediction...', 'warning');
        
        setTimeout(() => {
            const row = document.querySelector(`tr[data-prediction-id="${currentDeletePredictionId}"]`);
            if (row) {
                row.remove();
                showNotification('Prediction deleted successfully', 'success');
                
                // Close the modal
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deletePredictionModal'));
                deleteModal.hide();
                
                // Reset the current delete ID
                currentDeletePredictionId = null;
            }
        }, 1000);
    }
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
