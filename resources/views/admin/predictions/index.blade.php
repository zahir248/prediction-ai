@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Prediction Management</h1>
            <p class="text-muted mb-0">Monitor and analyze all predictions in the system</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="exportPredictions()">
                <i class="bi bi-download me-2"></i>Export
            </button>
            <button class="btn btn-primary btn-sm" onclick="refreshPredictions()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Prediction Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-graph-up text-primary fs-3"></i>
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
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-people text-info fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Active Users</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="activeUsers">{{ $predictions->unique('user_id')->count() }}</h2>
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
                        <option value="">All Clients</option>
                        @foreach($predictions->unique('user_id')->take(10) as $prediction)
                            <option value="{{ $prediction->user->id }}">{{ $prediction->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="dateFilter">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-outline-primary w-100" id="clearFilters">
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
                    <i class="bi bi-graph-up text-primary me-2"></i>Prediction Data
                </h5>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <button class="btn btn-outline-primary btn-sm" id="bulkActions" disabled>
                        <i class="bi bi-gear me-2"></i>Bulk Actions
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshPredictions()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="predictionsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-3 py-3">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="border-0 px-3 py-3">Client</th>
                            <th class="border-0 px-3 py-3">Prediction</th>
                            <th class="border-0 px-3 py-3">Date</th>
                            <th class="border-0 px-3 py-3">Status</th>
                            <th class="border-0 px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="predictionsTableBody">
                        @forelse($predictions as $prediction)
                            <tr data-prediction-id="{{ $prediction->id }}" data-user-id="{{ $prediction->user_id }}">
                                <td class="px-3 py-3">
                                    <input type="checkbox" class="form-check-input prediction-checkbox" value="{{ $prediction->id }}">
                                </td>
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
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $prediction->prediction_text }}">
                                        {{ $prediction->prediction_text }}
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <small class="text-muted">{{ $prediction->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-success rounded-pill">Completed</span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewPredictionDetails({{ $prediction->id }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="editPrediction({{ $prediction->id }})" title="Edit Prediction">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deletePrediction({{ $prediction->id }})" title="Delete Prediction">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                        <i class="bi bi-graph-up text-primary fs-1"></i>
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
                        <label class="form-label fw-semibold text-muted">User</label>
                        <p class="mb-0" id="modalUserName">User Name</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Date</label>
                        <p class="mb-0" id="modalPredictionDate">Jan 1, 2024</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Prediction Text</label>
                        <div class="bg-light p-3 rounded" id="modalPredictionText">Prediction content...</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Additional Details</label>
                        <p class="mb-0 text-muted">No additional details available</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editPredictionFromModal()">
                    <i class="bi bi-pencil me-2"></i>Edit Prediction
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Prediction Modal -->
<div class="modal fade" id="editPredictionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Prediction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPredictionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editPredictionText" class="form-label">Prediction Text</label>
                        <textarea class="form-control" id="editPredictionText" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editPredictionStatus" class="form-label">Status</label>
                        <select class="form-select" id="editPredictionStatus" required>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Prediction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for admin predictions */
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
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.pagination .page-link:hover {
    color: #3b82f6;
}
</style>

<script>
let currentPredictionId = null;

document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    updateBulkActionsButton();
});

function setupEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const userFilter = document.getElementById('userFilter');
    const dateFilter = document.getElementById('dateFilter');
    const clearFilters = document.getElementById('clearFilters');
    const selectAll = document.getElementById('selectAll');
    const predictionCheckboxes = document.querySelectorAll('.prediction-checkbox');

    searchInput.addEventListener('input', filterPredictions);
    userFilter.addEventListener('change', filterPredictions);
    dateFilter.addEventListener('change', filterPredictions);
    clearFilters.addEventListener('click', clearAllFilters);
    selectAll.addEventListener('change', toggleSelectAll);
    
    predictionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsButton);
    });
}

function filterPredictions() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const userFilter = document.getElementById('userFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#predictionsTableBody tr');
    
    rows.forEach(row => {
        const predictionText = row.querySelector('.text-truncate').textContent.toLowerCase();
        const userName = row.querySelector('h6').textContent.toLowerCase();
        const userId = row.dataset.userId;
        
        const matchesSearch = predictionText.includes(searchTerm) || userName.includes(searchTerm);
        const matchesUser = !userFilter || userId === userFilter;
        const matchesDate = !dateFilter || matchesDateFilter(row, dateFilter);
        
        row.style.display = matchesSearch && matchesUser && matchesDate ? '' : 'none';
    });
}

function matchesDateFilter(row, dateFilter) {
    const dateText = row.querySelector('small').textContent;
    const rowDate = new Date(dateText);
    const today = new Date();
    
    switch(dateFilter) {
        case 'today':
            return rowDate.toDateString() === today.toDateString();
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            return rowDate.toDateString() === yesterday.toDateString();
        case 'week':
            const weekAgo = new Date(today);
            weekAgo.setDate(weekAgo.getDate() - 7);
            return rowDate >= weekAgo;
        case 'month':
            const monthAgo = new Date(today);
            monthAgo.setMonth(monthAgo.getMonth() - 1);
            return rowDate >= monthAgo;
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

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const predictionCheckboxes = document.querySelectorAll('.prediction-checkbox');
    
    predictionCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActionsButton();
}

function updateBulkActionsButton() {
    const checkedBoxes = document.querySelectorAll('.prediction-checkbox:checked');
    const bulkActionsBtn = document.getElementById('bulkActions');
    
    bulkActionsBtn.disabled = checkedBoxes.length === 0;
}

function viewPredictionDetails(predictionId) {
    // Simulate fetching prediction details (replace with actual API call)
    const prediction = {
        userName: 'User Name',
        date: 'Jan 1, 2024',
        text: 'This is a sample prediction text that would be displayed in the modal...'
    };
    
    document.getElementById('modalUserName').textContent = prediction.userName;
    document.getElementById('modalPredictionDate').textContent = prediction.date;
    document.getElementById('modalPredictionText').textContent = prediction.text;
    
    currentPredictionId = predictionId;
    
    const modal = new bootstrap.Modal(document.getElementById('predictionDetailsModal'));
    modal.show();
}

function editPrediction(predictionId) {
    currentPredictionId = predictionId;
    
    // Simulate fetching prediction data (replace with actual API call)
    document.getElementById('editPredictionText').value = 'Sample prediction text...';
    document.getElementById('editPredictionStatus').value = 'completed';
    
    const modal = new bootstrap.Modal(document.getElementById('editPredictionModal'));
    modal.show();
}

function editPredictionFromModal() {
    const predictionDetailsModal = bootstrap.Modal.getInstance(document.getElementById('predictionDetailsModal'));
    predictionDetailsModal.hide();
    
    editPrediction(currentPredictionId);
}

function deletePrediction(predictionId) {
    if (confirm('Are you sure you want to delete this prediction? This action cannot be undone.')) {
        // Simulate deleting prediction (replace with actual API call)
        showNotification('Deleting prediction...', 'warning');
        
        setTimeout(() => {
            const row = document.querySelector(`tr[data-prediction-id="${predictionId}"]`);
            if (row) {
                row.remove();
                showNotification('Prediction deleted successfully', 'success');
            }
        }, 1000);
    }
}

function refreshPredictions() {
    showNotification('Refreshing predictions...', 'info');
    
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function exportPredictions() {
    showNotification('Exporting predictions...', 'info');
    
    setTimeout(() => {
        showNotification('Predictions exported successfully', 'success');
    }, 2000);
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
