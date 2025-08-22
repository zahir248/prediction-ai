@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Admin Management</h1>
            <p class="text-muted mb-0">Manage admin and superadmin users</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="bi bi-plus-circle me-2"></i>Add Admin
            </button>
            <button class="btn btn-outline-success btn-sm">
                <i class="bi bi-download me-2"></i>Export
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
                    <i class="bi bi-shield-lock text-danger fs-3"></i>
                </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Regular Admins</h6>
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
                            <i class="bi bi-shield-check text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Super Admins</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::where('role', 'superadmin')->count() }}</h2>
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
                            <i class="bi bi-shield text-info fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Admins</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ $admins->total() }}</h2>
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
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::whereIn('role', ['admin', 'superadmin'])->whereDate('last_login_at', today())->count() }}</h2>
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
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search admins...">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Super Admin</option>
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
                    <button class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Admins Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-people text-danger me-2"></i>Admin Users
                </h5>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <button class="btn btn-outline-danger btn-sm" id="bulkActions" disabled>
                        <i class="bi bi-gear me-2"></i>Bulk Actions
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="adminsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-3 py-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="border-0 px-3 py-3">Admin</th>
                            <th class="border-0 px-3 py-3">Role</th>
                            <th class="border-0 px-3 py-3">Status</th>
                            <th class="border-0 px-3 py-3 d-none d-md-table-cell">Last Login</th>
                            <th class="border-0 px-3 py-3 d-none d-lg-table-cell">Joined</th>
                            <th class="border-0 px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr data-admin-id="{{ $admin->id }}" data-role="{{ $admin->role }}" data-status="{{ $admin->last_login_at && ($admin->last_login_at instanceof \Carbon\Carbon || is_string($admin->last_login_at)) ? 'active' : 'inactive' }}">
                                <td class="px-3 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input admin-checkbox" type="checkbox" value="{{ $admin->id }}">
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-person text-danger"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $admin->name }}</h6>
                                            <small class="text-muted d-none d-sm-inline">{{ $admin->email }}</small>
                                            <small class="text-muted d-inline d-sm-none">{{ Str::limit($admin->email, 20) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-{{ $admin->role === 'superadmin' ? 'danger' : 'primary' }} rounded-pill">
                                        <i class="bi bi-{{ $admin->role === 'superadmin' ? 'shield-check' : 'shield-lock' }} me-1"></i>
                                        <span class="d-none d-sm-inline">{{ ucfirst($admin->role) }}</span>
                                        <span class="d-inline d-sm-none">{{ ucfirst($admin->role === 'superadmin' ? 'SA' : 'Admin') }}</span>
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    @if($admin->last_login_at && ($admin->last_login_at instanceof \Carbon\Carbon || is_string($admin->last_login_at)))
                                        <span class="badge bg-success rounded-pill">
                                            <i class="bi bi-circle-fill me-1"></i>
                                            <span class="d-none d-sm-inline">Active</span>
                                            <span class="d-inline d-sm-none">✓</span>
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">
                                            <i class="bi bi-circle me-1"></i>
                                            <span class="d-none d-sm-inline">Inactive</span>
                                            <span class="d-inline d-sm-none">○</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 d-none d-md-table-cell">
                                    @if($admin->last_login_at)
                                        <small class="text-muted">
                                            @if($admin->last_login_at instanceof \Carbon\Carbon)
                                                {{ $admin->last_login_at->diffForHumans() }}
                                            @else
                                                {{ \Carbon\Carbon::parse($admin->last_login_at)->diffForHumans() }}
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">Never</small>
                                    @endif
                                </td>
                                <td class="px-3 py-3 d-none d-lg-table-cell">
                                    <small class="text-muted">
                                        @if($admin->created_at instanceof \Carbon\Carbon)
                                            {{ $admin->created_at->format('M d, Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($admin->created_at)->format('M d, Y') }}
                                        @endif
                                    </small>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-danger btn-sm" onclick="viewAdminDetails({{ $admin->id }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm" onclick="editAdmin({{ $admin->id }})" title="Edit Admin">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if($admin->id !== auth()->id())
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteAdmin({{ $admin->id }})" title="Delete Admin">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                        <i class="bi bi-people text-muted fs-1"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">No admin users found</h6>
                                    <p class="text-muted small mb-0">Start by adding your first admin user</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($admins->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $admins->links() }}
        </div>
    @endif
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('superadmin.admins.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                        <small class="text-muted">Select the appropriate role for the new user</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Add Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Admin Modal -->
<div class="modal fade" id="showAdminModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="bi bi-person text-danger fs-2"></i>
                </div>
                            <div>
                                <h4 class="mb-1" id="showAdminName">Admin Name</h4>
                                <p class="text-muted mb-0" id="showAdminEmail">admin@example.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Role</label>
                        <p class="mb-0 fw-bold" id="showAdminRole">Admin</p>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Status</label>
                        <p class="mb-0" id="showAdminStatus">Active</p>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Joined</label>
                        <p class="mb-0" id="showAdminJoined">Jan 1, 2024</p>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Last Login</label>
                        <p class="mb-0" id="showAdminLastLogin">2 hours ago</p>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Total Predictions</label>
                        <p class="mb-0" id="showAdminPredictions">0</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="openEditModal()">
                    <i class="bi bi-pencil me-2"></i>Edit Admin
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAdminForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="editAdminName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="editAdminEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" id="editAdminRole" required>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password" minlength="8">
                        <small class="text-muted">Minimum 8 characters if changing</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Update Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Admin Modal -->
<div class="modal fade" id="deleteAdminModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    </div>
                    <h6 class="text-danger mb-2">Are you sure?</h6>
                    <p class="text-muted mb-0">You are about to delete the admin user: <strong id="deleteAdminName">Admin Name</strong></p>
                    <p class="text-muted small mt-2">This action cannot be undone and will permanently remove the user from the system.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteAdminForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Admin
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for admin index */
@media (max-width: 576px) {
    .stats-card h2 {
        font-size: 1.5rem;
    }
    
    .stats-card h6 {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .table-responsive .table td,
    .table-responsive .table th {
        padding: 0.5rem 0.375rem;
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

@media (max-width: 992px) {
    .d-none.d-lg-table-cell {
        display: table-cell !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const clearFilters = document.getElementById('clearFilters');
    const table = document.getElementById('adminsTable');
    const rows = table.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value;
        const statusValue = statusFilter.value;

        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(2) h6').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(2) small').textContent.toLowerCase();
            const role = row.dataset.role;
            const status = row.dataset.status;

            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleValue || role === roleValue;
            const matchesStatus = !statusValue || status === statusValue;

            row.style.display = matchesSearch && matchesRole && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);

    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        roleFilter.value = '';
        statusFilter.value = '';
        filterTable();
    });

    // Bulk actions
    const selectAll = document.getElementById('selectAll');
    const adminCheckboxes = document.querySelectorAll('.admin-checkbox');
    const bulkActions = document.getElementById('bulkActions');

    selectAll.addEventListener('change', function() {
        adminCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    adminCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedCount = document.querySelectorAll('.admin-checkbox:checked').length;
        bulkActions.disabled = checkedCount === 0;
        if (checkedCount > 0) {
            bulkActions.innerHTML = `<i class="bi bi-gear me-2"></i>Bulk Actions (${checkedCount})`;
        } else {
            bulkActions.innerHTML = `<i class="bi bi-gear me-2"></i>Bulk Actions`;
        }
    }
});

function editAdmin(adminId) {
    // Get admin data and populate edit modal
    const adminRow = document.querySelector(`tr[data-admin-id="${adminId}"]`);
    if (adminRow) {
        const name = adminRow.querySelector('td:nth-child(2) h6').textContent;
        const email = adminRow.querySelector('td:nth-child(2) small').textContent;
        const role = adminRow.querySelector('td:nth-child(3) .badge').textContent.trim();
        
        // Populate edit form
        document.getElementById('editAdminName').value = name;
        document.getElementById('editAdminEmail').value = email;
        document.getElementById('editAdminRole').value = role.toLowerCase();
        
        // Set form action
        document.getElementById('editAdminForm').action = `/superadmin/admins/${adminId}`;
        
        // Close show modal if open
        const showModal = bootstrap.Modal.getInstance(document.getElementById('showAdminModal'));
        if (showModal) {
            showModal.hide();
        }
        
        // Open edit modal
        const editModal = new bootstrap.Modal(document.getElementById('editAdminModal'));
        editModal.show();
    }
}

function deleteAdmin(adminId) {
    // Get admin data and populate delete modal
    const adminRow = document.querySelector(`tr[data-admin-id="${adminId}"]`);
    if (adminRow) {
        const name = adminRow.querySelector('td:nth-child(2) h6').textContent;
        
        // Populate delete modal
        document.getElementById('deleteAdminName').textContent = name;
        
        // Set form action
        document.getElementById('deleteAdminForm').action = `/superadmin/admins/${adminId}`;
        
        // Open delete modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAdminModal'));
        deleteModal.show();
    }
}

function viewAdminDetails(adminId) {
    // Get admin data and populate show modal
    const adminRow = document.querySelector(`tr[data-admin-id="${adminId}"]`);
    if (adminRow) {
        const name = adminRow.querySelector('td:nth-child(2) h6').textContent;
        const email = adminRow.querySelector('td:nth-child(2) small').textContent;
        const role = adminRow.querySelector('td:nth-child(3) .badge').textContent.trim();
        const status = adminRow.querySelector('td:nth-child(4) .badge').textContent.trim();
        const lastLogin = adminRow.querySelector('td:nth-child(5) small')?.textContent || 'Never';
        const joined = adminRow.querySelector('td:nth-child(6) small')?.textContent || 'Unknown';
        
        // Populate show modal
        document.getElementById('showAdminName').textContent = name;
        document.getElementById('showAdminEmail').textContent = email;
        document.getElementById('showAdminRole').textContent = role;
        document.getElementById('showAdminStatus').textContent = status;
        document.getElementById('showAdminJoined').textContent = joined;
        document.getElementById('showAdminLastLogin').textContent = lastLogin;
        
        // Store admin ID for edit functionality
        window.currentAdminId = adminId;
        
        // Open show modal
        const showModal = new bootstrap.Modal(document.getElementById('showAdminModal'));
        showModal.show();
    }
}

function openEditModal() {
    // Close show modal
    const showModal = bootstrap.Modal.getInstance(document.getElementById('showAdminModal'));
    if (showModal) {
        showModal.hide();
    }
    
    // Open edit modal with current admin data
    if (window.currentAdminId) {
        editAdmin(window.currentAdminId);
    }
}
</script>
@endsection
