@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">System Logs</h1>
            <p class="text-muted mb-0">Monitor and analyze system activity logs</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <button class="btn btn-outline-danger btn-sm" onclick="refreshLogs()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="exportLogs()">
                <i class="bi bi-download me-2"></i>Export
            </button>
            <button class="btn btn-danger btn-sm" onclick="clearLogs()">
                <i class="bi bi-trash me-2"></i>Clear Logs
            </button>
        </div>
    </div>

    <!-- Log Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-journal-text text-danger fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Total Logs</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="totalLogs">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-triangle text-danger fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Errors</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="errorLogs">0</h2>
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
                            <i class="bi bi-exclamation-circle text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Warnings</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="warningLogs">0</h2>
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
                            <i class="bi bi-info-circle text-info fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Info</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="infoLogs">0</h2>
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
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search logs...">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select class="form-select" id="levelFilter">
                        <option value="">All Levels</option>
                        <option value="ERROR">Error</option>
                        <option value="WARNING">Warning</option>
                        <option value="INFO">Info</option>
                        <option value="DEBUG">Debug</option>
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
                    <button class="btn btn-outline-danger w-100" id="clearFilters">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-list-ul text-danger me-2"></i>System Logs
                </h5>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                        <label class="form-check-label" for="autoRefresh">Auto-refresh</label>
                    </div>
                    <select class="form-select form-select-sm" id="refreshInterval" style="width: auto;">
                        <option value="5000">5s</option>
                        <option value="10000">10s</option>
                        <option value="30000" selected>30s</option>
                        <option value="60000">1m</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="logsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-3 py-3">Level</th>
                            <th class="border-0 px-3 py-3">Message</th>
                            <th class="border-0 px-3 py-3 d-none d-md-table-cell">Context</th>
                            <th class="border-0 px-3 py-3 d-none d-lg-table-cell">Timestamp</th>
                            <th class="border-0 px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <!-- Logs will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Logs pagination">
            <ul class="pagination" id="logsPagination">
                <!-- Pagination will be populated here -->
            </ul>
        </nav>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Log Level</label>
                        <p class="mb-0" id="modalLogLevel">INFO</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-muted">Timestamp</label>
                        <p class="mb-0" id="modalTimestamp">2024-01-01 12:00:00</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Message</label>
                        <p class="mb-0" id="modalMessage">Log message content</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Context</label>
                        <pre class="bg-light p-3 rounded" id="modalContext">No context available</pre>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Stack Trace</label>
                        <pre class="bg-light p-3 rounded" id="modalStackTrace">No stack trace available</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="copyLogDetails()">
                    <i class="bi bi-clipboard me-2"></i>Copy Details
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for logs */
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
    
    .auto-refresh-controls {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 992px) {
    .d-none.d-lg-table-cell {
        display: table-cell !important;
    }
}

/* Log level badges */
.log-level-ERROR {
    background-color: #dc3545;
    color: white;
}

/* Pagination active state */
.pagination .page-item.active .page-link {
    background-color: #dc2626;
    border-color: #dc2626;
}

.pagination .page-link:hover {
    color: #dc2626;
}

.log-level-WARNING {
    background-color: #ffc107;
    color: #212529;
}

.log-level-INFO {
    background-color: #0dcaf0;
    color: white;
}

.log-level-DEBUG {
    background-color: #6c757d;
    color: white;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive .table {
        font-size: 0.875rem;
    }
    
    .log-message {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
}
</style>

<script>
let logsData = [];
let currentPage = 1;
let logsPerPage = 20;
let autoRefreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    initializeLogs();
    setupEventListeners();
    startAutoRefresh();
});

function initializeLogs() {
    // Simulate loading logs data
    loadSampleLogs();
    updateLogStatistics();
    renderLogsTable();
    renderPagination();
}

function setupEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const levelFilter = document.getElementById('levelFilter');
    const dateFilter = document.getElementById('dateFilter');
    const clearFilters = document.getElementById('clearFilters');
    const autoRefresh = document.getElementById('autoRefresh');
    const refreshInterval = document.getElementById('refreshInterval');

    searchInput.addEventListener('input', filterLogs);
    levelFilter.addEventListener('change', filterLogs);
    dateFilter.addEventListener('change', filterLogs);
    clearFilters.addEventListener('click', clearAllFilters);
    autoRefresh.addEventListener('change', toggleAutoRefresh);
    refreshInterval.addEventListener('change', updateRefreshInterval);
}

function loadSampleLogs() {
    // Simulate log data (replace with actual API call)
    logsData = [
        {
            level: 'ERROR',
            message: 'Database connection failed: Connection refused',
            context: 'Database connection attempt',
            timestamp: '2024-01-20 10:30:15',
            stackTrace: 'Stack trace information...'
        },
        {
            level: 'WARNING',
            message: 'Cache directory not writable',
            context: 'Cache initialization',
            timestamp: '2024-01-20 10:25:42',
            stackTrace: 'Stack trace information...'
        },
        {
            level: 'INFO',
            message: 'User login successful: admin@example.com',
            context: 'Authentication',
            timestamp: '2024-01-20 10:20:18',
            stackTrace: 'No stack trace available'
        },
        {
            level: 'DEBUG',
            message: 'Route cache cleared successfully',
            context: 'Cache management',
            timestamp: '2024-01-20 10:15:33',
            stackTrace: 'No stack trace available'
        },
        {
            level: 'ERROR',
            message: 'File upload failed: Invalid file type',
            context: 'File upload',
            timestamp: '2024-01-20 10:10:55',
            stackTrace: 'Stack trace information...'
        }
    ];
}

function updateLogStatistics() {
    const totalLogs = logsData.length;
    const errorLogs = logsData.filter(log => log.level === 'ERROR').length;
    const warningLogs = logsData.filter(log => log.level === 'WARNING').length;
    const infoLogs = logsData.filter(log => log.level === 'INFO').length;

    document.getElementById('totalLogs').textContent = totalLogs;
    document.getElementById('errorLogs').textContent = errorLogs;
    document.getElementById('warningLogs').textContent = warningLogs;
    document.getElementById('infoLogs').textContent = infoLogs;
}

function renderLogsTable() {
    const tbody = document.getElementById('logsTableBody');
    const startIndex = (currentPage - 1) * logsPerPage;
    const endIndex = startIndex + logsPerPage;
    const pageLogs = logsData.slice(startIndex, endIndex);

    tbody.innerHTML = '';

    if (pageLogs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-journal-text text-danger fs-1"></i>
                    </div>
                    <h6 class="text-muted mb-2">No logs found</h6>
                    <p class="text-muted small mb-0">Try adjusting your filters or refresh the page</p>
                </td>
            </tr>
        `;
        return;
    }

    pageLogs.forEach((log, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-3 py-3">
                <span class="badge log-level-${log.level} rounded-pill">
                    ${log.level}
                </span>
            </td>
            <td class="px-3 py-3">
                <div class="log-message" title="${log.message}">
                    ${log.message}
                </div>
            </td>
            <td class="px-3 py-3 d-none d-md-table-cell">
                <small class="text-muted">${log.context}</small>
            </td>
            <td class="px-3 py-3 d-none d-lg-table-cell">
                <small class="text-muted">${log.timestamp}</small>
            </td>
            <td class="px-3 py-3">
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-danger btn-sm" onclick="viewLogDetails(${startIndex + index})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="copyLogMessage('${log.message}')" title="Copy Message">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderPagination() {
    const totalPages = Math.ceil(logsData.length / logsPerPage);
    const pagination = document.getElementById('logsPagination');

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            paginationHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link ${i === currentPage ? 'bg-danger border-danger' : ''}" href="#" onclick="changePage(${i})">${i}</a>
                </li>
            `;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
        </li>
    `;

    pagination.innerHTML = paginationHTML;
}

function changePage(page) {
    if (page < 1 || page > Math.ceil(logsData.length / logsPerPage)) {
        return;
    }
    currentPage = page;
    renderLogsTable();
    renderPagination();
}

function filterLogs() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const levelFilter = document.getElementById('levelFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;

    // Reset to first page when filtering
    currentPage = 1;

    // Apply filters (simplified - replace with actual filtering logic)
    // For now, we'll just re-render the table
    renderLogsTable();
    renderPagination();
}

function clearAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('levelFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    currentPage = 1;
    renderLogsTable();
    renderPagination();
}

function viewLogDetails(logIndex) {
    const log = logsData[logIndex];
    
    document.getElementById('modalLogLevel').textContent = log.level;
    document.getElementById('modalTimestamp').textContent = log.timestamp;
    document.getElementById('modalMessage').textContent = log.message;
    document.getElementById('modalContext').textContent = log.context;
    document.getElementById('modalStackTrace').textContent = log.stackTrace;
    
    const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    modal.show();
}

function copyLogMessage(message) {
    navigator.clipboard.writeText(message).then(() => {
        showNotification('Log message copied to clipboard', 'success');
    }).catch(() => {
        showNotification('Failed to copy message', 'error');
    });
}

function copyLogDetails() {
    const details = `
Level: ${document.getElementById('modalLogLevel').textContent}
Timestamp: ${document.getElementById('modalTimestamp').textContent}
Message: ${document.getElementById('modalMessage').textContent}
Context: ${document.getElementById('modalContext').textContent}
Stack Trace: ${document.getElementById('modalStackTrace').textContent}
    `;
    
    navigator.clipboard.writeText(details.trim()).then(() => {
        showNotification('Log details copied to clipboard', 'success');
    }).catch(() => {
        showNotification('Failed to copy details', 'error');
    });
}

function refreshLogs() {
    // Simulate refreshing logs
    showNotification('Refreshing logs...', 'info');
    
    setTimeout(() => {
        loadSampleLogs();
        updateLogStatistics();
        renderLogsTable();
        renderPagination();
        showNotification('Logs refreshed successfully', 'success');
    }, 1000);
}

function exportLogs() {
    // Simulate exporting logs
    showNotification('Exporting logs...', 'info');
    
    setTimeout(() => {
        showNotification('Logs exported successfully', 'success');
    }, 2000);
}

function clearLogs() {
    if (confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
        showNotification('Clearing logs...', 'warning');
        
        setTimeout(() => {
            logsData = [];
            currentPage = 1;
            updateLogStatistics();
            renderLogsTable();
            renderPagination();
            showNotification('All logs cleared successfully', 'success');
        }, 1000);
    }
}

function toggleAutoRefresh() {
    const autoRefresh = document.getElementById('autoRefresh');
    
    if (autoRefresh.checked) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
}

function startAutoRefresh() {
    const interval = parseInt(document.getElementById('refreshInterval').value);
    autoRefreshInterval = setInterval(refreshLogs, interval);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

function updateRefreshInterval() {
    if (document.getElementById('autoRefresh').checked) {
        stopAutoRefresh();
        startAutoRefresh();
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
