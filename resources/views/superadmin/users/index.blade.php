@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Client Management</h1>
            <p class="text-muted mb-0">Manage client accounts and their activities</p>
        </div>
                 <div class="d-flex flex-column flex-sm-row gap-2">
             <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addClientModal">
                 <i class="bi bi-plus-circle me-2"></i>Add Client
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
                    <i class="bi bi-person-badge text-danger fs-3"></i>
                </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Clients</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ $users->total() }}</h2>
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
                            <h6 class="text-muted mb-1 fw-semibold">Active Clients</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::where('role', 'user')->whereNotNull('last_login_at')->count() }}</h2>
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
                            <h6 class="text-muted mb-1 fw-semibold">New This Month</h6>
                            <h2 class="mb-0 fw-bold text-dark">{{ \App\Models\User::where('role', 'user')->whereMonth('created_at', now()->month)->count() }}</h2>
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
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search clients...">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" id="organizationFilter">
                        <option value="">All Organizations</option>
                        @foreach(\App\Models\User::where('role', 'user')->distinct('organization')->pluck('organization')->filter() as $org)
                            <option value="{{ $org }}">{{ $org }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="activityFilter">
                        <option value="">All Activity</option>
                        <option value="with_predictions">With Predictions</option>
                        <option value="no_predictions">No Predictions</option>
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

    <!-- Clients Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-person-badge text-danger me-2"></i>Client Accounts
                </h5>
                                 <div class="d-flex gap-2 mt-2 mt-sm-0">
                     
                 </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="usersTable">
                                         <thead class="table-light">
                         <tr>
                             <th class="border-0 px-3 py-3">Client</th>
                             <th class="border-0 px-3 py-3">Organization</th>
                            <th class="border-0 px-3 py-3">Status</th>
                            <th class="border-0 px-3 py-3 d-none d-md-table-cell">Predictions</th>
                            <th class="border-0 px-3 py-3 d-none d-lg-table-cell">Last Login</th>
                            <th class="border-0 px-3 py-3 d-none d-lg-table-cell">Joined</th>
                            <th class="border-0 px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                                                 <tr data-user-id="{{ $user->id }}" data-status="{{ $user->last_login_at && ($user->last_login_at instanceof \Carbon\Carbon || is_string($user->last_login_at)) ? 'active' : 'inactive' }}" data-activity="{{ $user->predictions_count > 0 ? 'with_predictions' : 'no_predictions' }}">
                             <td class="px-3 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-person text-danger"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                        <small class="text-muted d-none d-sm-inline">{{ $user->email }}</small>
                                        <small class="text-muted d-inline d-sm-none">{{ Str::limit($user->email, 20) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                @if($user->organization)
                                    <span class="badge bg-info rounded-pill">{{ $user->organization }}</span>
                                @else
                                    <span class="text-muted small">No Organization</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @if($user->last_login_at && ($user->last_login_at instanceof \Carbon\Carbon || is_string($user->last_login_at)))
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
                                <span class="badge bg-info rounded-pill">
                                    <i class="bi bi-graph-up me-1"></i>{{ $user->predictions_count }}
                                </span>
                            </td>
                            <td class="px-3 py-3 d-none d-lg-table-cell">
                                @if($user->last_login_at)
                                    <small class="text-muted">
                                        @if($user->last_login_at instanceof \Carbon\Carbon)
                                            {{ $user->last_login_at->diffForHumans() }}
                                        @else
                                            {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                                        @endif
                                    </small>
                                @else
                                    <small class="text-muted">Never</small>
                                @endif
                            </td>
                            <td class="px-3 py-3 d-none d-lg-table-cell">
                                <small class="text-muted">
                                    @if($user->created_at instanceof \Carbon\Carbon)
                                        {{ $user->created_at->format('M d, Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                                    @endif
                                </small>
                            </td>
                            <td class="px-3 py-3">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-danger btn-sm" onclick="viewClientDetails({{ $user->id }})" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editClient({{ $user->id }})" title="Edit Client">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteClient({{ $user->id }})" title="Delete Client">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                                                 <tr>
                             <td colspan="7" class="text-center py-5">
                                 <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                     <i class="bi bi-person-badge text-muted fs-1"></i>
                                 </div>
                                 <h6 class="text-muted mb-2">No clients found</h6>
                                 <p class="text-muted small mb-0">Clients will appear here when created by administrators</p>
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
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- Show Client Modal -->
<div class="modal fade" id="showUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Client Details</h5>
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
                                <h4 class="mb-1" id="showUserName">Client Name</h4>
                                <p class="text-muted mb-0" id="showUserEmail">client@example.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Role</label>
                        <p class="mb-0 fw-bold" id="showUserRole">Client</p>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Status</label>
                        <p class="mb-0" id="showUserStatus">Active</p>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Organization</label>
                        <p class="mb-0" id="showUserOrganization">Organization Name</p>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Joined</label>
                        <p class="mb-0" id="showUserJoined">Jan 1, 2024</p>
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Last Login</label>
                        <p class="mb-0" id="showUserLastLogin">2 hours ago</p>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Total Predictions</label>
                        <p class="mb-0" id="showUserPredictions">0</p>
                    </div>
                </div>
            </div>
                         <div class="modal-footer justify-content-center">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-warning" onclick="openEditClientModal()">
                     <i class="bi bi-pencil me-2"></i>Edit Client
                 </button>
             </div>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="editUserName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="editUserEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Organization</label>
                        <input type="text" class="form-control" name="organization" id="editUserOrganization" placeholder="Enter organization name">
                        <small class="text-muted">Optional - Leave blank if no organization</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password" minlength="8">
                        <small class="text-muted">Minimum 8 characters if changing</small>
                    </div>
                </div>
                                 <div class="modal-footer justify-content-center">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                     <button type="submit" class="btn btn-danger">Update Client</button>
                 </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Client Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    </div>
                    <h6 class="text-danger mb-2">Are you sure?</h6>
                    <p class="text-muted mb-0">You are about to delete the client: <strong id="deleteUserName">Client Name</strong></p>
                    <p class="text-muted small mt-2">This action cannot be undone and will permanently remove the client and all their predictions from the system.</p>
                </div>
            </div>
                         <div class="modal-footer justify-content-center">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                 <form id="deleteUserForm" method="POST" class="d-inline">
                     @csrf
                     @method('DELETE')
                     <button type="submit" class="btn btn-danger">
                         <i class="bi bi-trash me-2"></i>Delete Client
                     </button>
                 </form>
             </div>
        </div>
    </div>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('superadmin.users.store') }}" method="POST">
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
                        <label class="form-label">Organization</label>
                        <input type="text" class="form-control" name="organization" placeholder="Enter organization name">
                        <small class="text-muted">Optional - Leave blank if no organization</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Add Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for users index */
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
    const organizationFilter = document.getElementById('organizationFilter');
    const statusFilter = document.getElementById('statusFilter');
    const activityFilter = document.getElementById('activityFilter');
    const clearFilters = document.getElementById('clearFilters');
    const table = document.getElementById('usersTable');
    const rows = table.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const organizationValue = organizationFilter.value;
        const statusValue = statusFilter.value;
        const activityValue = activityFilter.value;

        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(1) h6').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(1) small').textContent.toLowerCase();
            const organization = row.querySelector('td:nth-child(2) .badge, td:nth-child(2) .small')?.textContent.toLowerCase() || '';
            const status = row.dataset.status;
            const activity = row.dataset.activity;

            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesOrganization = !organizationValue || organization.includes(organizationValue.toLowerCase());
            const matchesStatus = !statusValue || status === statusValue;
            const matchesActivity = !activityValue || activity === activityValue;

            row.style.display = matchesSearch && matchesOrganization && matchesStatus && matchesActivity ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    organizationFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    activityFilter.addEventListener('change', filterTable);

    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        organizationFilter.value = '';
        statusFilter.value = '';
        activityFilter.value = '';
        filterTable();
    });
});

function viewClientDetails(userId) {
     // Get client data and populate show modal
     const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
     if (userRow) {
         const name = userRow.querySelector('td:nth-child(1) h6').textContent;
         const email = userRow.querySelector('td:nth-child(1) small').textContent;
         const organization = userRow.querySelector('td:nth-child(2) .badge')?.textContent || 'No Organization';
         const status = userRow.querySelector('td:nth-child(3) .badge').textContent.trim();
         const predictions = userRow.querySelector('td:nth-child(4) .badge')?.textContent.trim() || '0';
         const lastLogin = userRow.querySelector('td:nth-child(5) small')?.textContent || 'Never';
         const joined = userRow.querySelector('td:nth-child(6) small')?.textContent || 'Unknown';
        
        // Populate show modal
        document.getElementById('showUserName').textContent = name;
        document.getElementById('showUserEmail').textContent = email;
        document.getElementById('showUserRole').textContent = 'Client';
        document.getElementById('showUserOrganization').textContent = organization;
        document.getElementById('showUserStatus').textContent = status;
        document.getElementById('showUserJoined').textContent = joined;
        document.getElementById('showUserLastLogin').textContent = lastLogin;
        document.getElementById('showUserPredictions').textContent = predictions;
        
        // Store client ID for edit functionality
        window.currentUserId = userId;
        
        // Open show modal
        const showModal = new bootstrap.Modal(document.getElementById('showUserModal'));
        showModal.show();
    }
}

function editClient(userId) {
     // Get client data and populate edit modal
     const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
     if (userRow) {
         const name = userRow.querySelector('td:nth-child(1) h6').textContent;
         const email = userRow.querySelector('td:nth-child(1) small').textContent;
         const organization = userRow.querySelector('td:nth-child(2) .badge')?.textContent || '';
        
        // Populate edit form
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserOrganization').value = organization;
        
        // Set form action
        document.getElementById('editUserForm').action = `{{ url('superadmin/users') }}/${userId}`;
        
        // Close show modal if open
        const showModal = bootstrap.Modal.getInstance(document.getElementById('showUserModal'));
        if (showModal) {
            showModal.hide();
        }
        
        // Open edit modal
        const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();
    }
}

function deleteClient(userId) {
     // Get client data and populate delete modal
     const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
     if (userRow) {
         const name = userRow.querySelector('td:nth-child(1) h6').textContent;
        
        // Populate delete modal
        document.getElementById('deleteUserName').textContent = name;
        
        // Set form action
        document.getElementById('deleteUserForm').action = `{{ url('superadmin/users') }}/${userId}`;
        
        // Open delete modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        deleteModal.show();
    }
}

function openEditClientModal() {
    // Close show modal
    const showModal = bootstrap.Modal.getInstance(document.getElementById('showUserModal'));
    if (showModal) {
        showModal.hide();
    }
    
    // Open edit modal with current client data
    if (window.currentUserId) {
        editClient(window.currentUserId);
    }
}
</script>
@endsection