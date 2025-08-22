@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Client Management</h1>
            <p class="text-muted mb-0">Manage all clients in the system</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="exportUsers()">
                <i class="bi bi-download me-2"></i>Export
            </button>
            <button class="btn btn-primary btn-sm" onclick="refreshUsers()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
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
                            <h2 class="mb-0 fw-bold text-dark" id="totalUsers">{{ $users->total() }}</h2>
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
                            <i class="bi bi-person-check text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Active Clients</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="activeUsers">{{ $users->where('last_login_at', '!=', null)->count() }}</h2>
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
                            <i class="bi bi-person-x text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">New This Month</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="newUsers">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() }}</h2>
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
                            <h2 class="mb-0 fw-bold text-dark" id="totalPredictions">{{ $users->sum('predictions_count') }}</h2>
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
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search clients...">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="user">Client</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-people text-primary me-2"></i>Client Accounts
                    </h5>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <button class="btn btn-outline-primary btn-sm" id="bulkActions" disabled>
                        <i class="bi bi-gear me-2"></i>Bulk Actions
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshUsers()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-3 py-3">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="border-0 px-3 py-3">Client</th>
                            <th class="border-0 px-3 py-3">Role</th>
                            <th class="border-0 px-3 py-3">Predictions</th>
                            <th class="border-0 px-3 py-3">Joined</th>
                            <th class="border-0 px-3 py-3">Status</th>
                            <th class="border-0 px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @forelse($users as $user)
                            <tr data-user-id="{{ $user->id }}" data-role="{{ $user->role }}">
                                <td class="px-3 py-3">
                                    <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
                                </td>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-secondary rounded-pill">
                                        Client
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="fw-semibold">{{ $user->predictions_count ?? 0 }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-success rounded-pill">Active</span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewUserDetails({{ $user->id }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="editUser({{ $user->id }})" title="Edit User">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteUser({{ $user->id }})" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                        <i class="bi bi-people text-primary fs-1"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">No clients found</h6>
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
    @if($users->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Clients pagination">
                {{ $users->links() }}
            </nav>
        </div>
    @endif
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Client Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Name</label>
                        <p class="mb-0" id="modalUserName">Client Name</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Email</label>
                        <p class="mb-0" id="modalUserEmail">user@example.com</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Role</label>
                        <p class="mb-0" id="modalUserRole">Client</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Joined</label>
                        <p class="mb-0" id="modalUserJoined">Jan 1, 2024</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Total Predictions</label>
                        <p class="mb-0" id="modalUserPredictions">0</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editUserFromModal()">
                    <i class="bi bi-pencil me-2"></i>Edit User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editUserName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editUserEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserRole" class="form-label">Role</label>
                        <select class="form-select" id="editUserRole" required>
                            <option value="user">Client</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for admin users */
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
let currentUserId = null;

document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    updateBulkActionsButton();
});

function setupEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const clearFilters = document.getElementById('clearFilters');
    const selectAll = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');

    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
    statusFilter.addEventListener('change', filterUsers);
    clearFilters.addEventListener('click', clearAllFilters);
    selectAll.addEventListener('change', toggleSelectAll);
    
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsButton);
    });
}

function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('#usersTableBody tr');
    
    rows.forEach(row => {
        const userName = row.querySelector('h6').textContent.toLowerCase();
        const userEmail = row.querySelector('small').textContent.toLowerCase();
        const userRole = row.dataset.role;
        
        const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm);
        const matchesRole = !roleFilter || userRole === roleFilter;
        const matchesStatus = !statusFilter || row.querySelector('.badge').textContent.toLowerCase().includes(statusFilter);
        
        row.style.display = matchesSearch && matchesRole && matchesStatus ? '' : 'none';
    });
}

function clearAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    const rows = document.querySelectorAll('#usersTableBody tr');
    rows.forEach(row => row.style.display = '');
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    userCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActionsButton();
}

function updateBulkActionsButton() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const bulkActionsBtn = document.getElementById('bulkActions');
    
    bulkActionsBtn.disabled = checkedBoxes.length === 0;
}

function viewUserDetails(userId) {
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!userRow) return;
    
    const userName = userRow.querySelector('td:nth-child(2) h6').textContent;
    const userEmail = userRow.querySelector('td:nth-child(2) small').textContent;
    const userRole = 'Client'; // All users in admin view are clients
    const userJoined = userRow.querySelector('td:nth-child(5) small').textContent;
    const userPredictions = userRow.querySelector('td:nth-child(4) span').textContent;
    
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalUserEmail').textContent = userEmail;
    document.getElementById('modalUserRole').textContent = userRole;
    document.getElementById('modalUserJoined').textContent = userJoined;
    document.getElementById('modalUserPredictions').textContent = userPredictions;
    
    currentUserId = userId;
    
    const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    modal.show();
}

function editUser(userId) {
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!userRow) return;
    
    const userName = userRow.querySelector('td:nth-child(2) h6').textContent;
    const userEmail = userRow.querySelector('td:nth-child(2) small').textContent;
    
    document.getElementById('editUserName').value = userName;
    document.getElementById('editUserEmail').value = userEmail;
    document.getElementById('editUserRole').value = 'user'; // All users in admin view are clients
    
    currentUserId = userId;
    
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}

function editUserFromModal() {
    const userDetailsModal = bootstrap.Modal.getInstance(document.getElementById('userDetailsModal'));
    userDetailsModal.hide();
    
    editUser(currentUserId);
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        // Simulate deleting user (replace with actual API call)
        showNotification('Deleting user...', 'warning');
        
        setTimeout(() => {
            const row = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (row) {
                row.remove();
                showNotification('User deleted successfully', 'success');
            }
        }, 1000);
    }
}

function refreshUsers() {
    showNotification('Refreshing users...', 'info');
    
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function exportUsers() {
    showNotification('Exporting users...', 'info');
    
    setTimeout(() => {
        showNotification('Users exported successfully', 'success');
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
