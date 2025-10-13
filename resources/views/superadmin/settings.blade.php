@extends('layouts.superadmin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 mb-1 text-dark fw-bold">System Settings</h1>
            <p class="text-muted mb-0">Configure system parameters and monitor system health</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <button class="btn btn-outline-danger btn-sm" onclick="refreshSystemHealth()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
            <button class="btn btn-danger btn-sm">
                <i class="bi bi-save me-2"></i>Save Changes
            </button>
        </div>
    </div>

    <!-- System Health Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-cpu text-success fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">CPU Usage</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="cpuUsage">45%</h2>
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
                            <i class="bi bi-memory text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Memory Usage</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="memoryUsage">62%</h2>
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
                            <i class="bi bi-hdd text-danger fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Disk Space</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="diskUsage">78%</h2>
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
                            <i class="bi bi-wifi text-info fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 fw-semibold">Network</h6>
                            <h2 class="mb-0 fw-bold text-dark" id="networkUsage">23%</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Provider Settings -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-robot text-primary me-2"></i>AI Provider Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.ai-provider.update') }}" id="aiProviderForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label for="ai_provider" class="form-label fw-semibold">Select AI Provider</label>
                                <select class="form-select" id="ai_provider" name="ai_provider" required>
                                    @foreach($availableProviders as $key => $provider)
                                        <option value="{{ $key }}" {{ $currentProvider === $key ? 'selected' : '' }}>
                                            {{ $provider['name'] }} - {{ $provider['description'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Choose the AI provider for generating predictions. Both providers offer high-quality analysis.</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Actions</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save Provider
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="testAIProvider()">
                                        <i class="bi bi-wifi me-2"></i>Test Connection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Current Provider Status -->
                    <div class="row g-3 mt-3">
                        <div class="col-12 col-md-6">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Current Provider:</strong> {{ $availableProviders[$currentProvider]['name'] }}
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Model:</strong> {{ $availableProviders[$currentProvider]['model'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Content -->
    <div class="row g-4">
        <!-- System Information -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-info-circle text-danger me-2"></i>System Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-server text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">Server</h6>
                                    <small class="text-muted">{{ php_uname('s') }} {{ php_uname('r') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-code-slash text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">PHP Version</h6>
                                    <small class="text-muted">{{ phpversion() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-database text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">Database</h6>
                                    <small class="text-muted">MySQL {{ \DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-clock text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">Uptime</h6>
                                    <small class="text-muted" id="systemUptime">Calculating...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Controls -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-gear text-success me-2"></i>Quick Controls
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Maintenance Mode</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="maintenanceMode">
                                <label class="form-check-label" for="maintenanceMode"></label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Debug Mode</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="debugMode">
                                <label class="form-check-label" for="debugMode"></label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Auto Updates</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="autoUpdates" checked>
                                <label class="form-check-label" for="autoUpdates"></label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Security Logs</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="securityLogs" checked>
                                <label class="form-check-label" for="securityLogs"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Settings -->
    <div class="row g-4 mt-2">
        <!-- Cache Management -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-lightning text-warning me-2"></i>Cache Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                                            <button class="btn btn-outline-danger" onclick="clearCache('all')">
                        <i class="bi bi-trash me-2"></i>Clear All Cache
                    </button>
                        <button class="btn btn-outline-success" onclick="clearCache('views')">
                            <i class="bi bi-eye me-2"></i>Clear View Cache
                        </button>
                        <button class="btn btn-outline-info" onclick="clearCache('config')">
                            <i class="bi bi-gear me-2"></i>Clear Config Cache
                        </button>
                        <button class="btn btn-outline-warning" onclick="clearCache('routes')">
                            <i class="bi bi-sign-intersection me-2"></i>Clear Route Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Management -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-database text-info me-2"></i>Database Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                                            <button class="btn btn-outline-danger" onclick="backupDatabase()">
                        <i class="bi bi-download me-2"></i>Create Backup
                    </button>
                        <button class="btn btn-outline-success" onclick="optimizeDatabase()">
                            <i class="bi bi-speedometer2 me-2"></i>Optimize Tables
                        </button>
                        <button class="btn btn-outline-warning" onclick="checkDatabase()">
                            <i class="bi bi-search me-2"></i>Check Integrity
                        </button>
                        <button class="btn btn-outline-danger" onclick="repairDatabase()">
                            <i class="bi bi-wrench me-2"></i>Repair Tables
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Monitoring -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-graph-up text-success me-2"></i>System Monitoring
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                    <i class="bi bi-people text-danger fs-2"></i>
                                </div>
                                <h6 class="mb-1 fw-semibold">Active Users</h6>
                                <h4 class="mb-0 text-danger" id="activeUsers">0</h4>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                    <i class="bi bi-graph-up text-success fs-2"></i>
                                </div>
                                <h6 class="mb-1 fw-semibold">Requests/min</h6>
                                <h4 class="mb-0 text-success" id="requestsPerMin">0</h4>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                    <i class="bi bi-clock text-warning fs-2"></i>
                                </div>
                                <h6 class="mb-1 fw-semibold">Response Time</h6>
                                <h4 class="mb-0 text-warning" id="responseTime">0ms</h4>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                    <i class="bi bi-hdd text-info fs-2"></i>
                                </div>
                                <h6 class="mb-1 fw-semibold">Storage Used</h6>
                                <h4 class="mb-0 text-info" id="storageUsed">0GB</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional responsive styles for settings */
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
    
    .d-grid.gap-2 .btn {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
}

@media (max-width: 768px) {
    .system-info .col-12.col-sm-6 {
        margin-bottom: 0.75rem;
    }
    
    .system-info .col-12.col-sm-6:last-child {
        margin-bottom: 0;
    }
    
    .quick-controls .form-check {
        margin-bottom: 0.5rem;
    }
    
    .quick-controls .form-check:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 992px) {
    .system-monitoring .col-12.col-sm-6.col-lg-3 {
        margin-bottom: 0.75rem;
    }
    
    .system-monitoring .col-12.col-sm-6.col-lg-3:last-child {
        margin-bottom: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize system health data
    refreshSystemHealth();
    
    // Auto-refresh every 30 seconds
    setInterval(refreshSystemHealth, 30000);
    
    // Initialize toggle switches
    initializeToggles();
});

function refreshSystemHealth() {
    // Simulate system health data (replace with actual API calls)
    updateSystemMetrics();
    updateSystemUptime();
    updateMonitoringData();
}

function updateSystemMetrics() {
    // Simulate CPU usage
    const cpuUsage = Math.floor(Math.random() * 30) + 40;
    document.getElementById('cpuUsage').textContent = cpuUsage + '%';
    
    // Simulate memory usage
    const memoryUsage = Math.floor(Math.random() * 20) + 55;
    document.getElementById('memoryUsage').textContent = memoryUsage + '%';
    
    // Simulate disk usage
    const diskUsage = Math.floor(Math.random() * 15) + 70;
    document.getElementById('diskUsage').textContent = diskUsage + '%';
    
    // Simulate network usage
    const networkUsage = Math.floor(Math.random() * 40) + 10;
    document.getElementById('networkUsage').textContent = networkUsage + '%';
}

function updateSystemUptime() {
    // Simulate system uptime
    const uptime = Math.floor(Math.random() * 24) + 1;
    document.getElementById('systemUptime').textContent = uptime + ' days';
}

function updateMonitoringData() {
    // Simulate monitoring data
    document.getElementById('activeUsers').textContent = Math.floor(Math.random() * 50) + 10;
    document.getElementById('requestsPerMin').textContent = Math.floor(Math.random() * 100) + 50;
    document.getElementById('responseTime').textContent = (Math.random() * 200 + 50).toFixed(0) + 'ms';
    document.getElementById('storageUsed').textContent = (Math.random() * 10 + 5).toFixed(1) + 'GB';
}

function initializeToggles() {
    // Initialize toggle switches with default values
    document.getElementById('maintenanceMode').checked = false;
    document.getElementById('debugMode').checked = false;
    document.getElementById('autoUpdates').checked = true;
    document.getElementById('securityLogs').checked = true;
    
    // Add event listeners
    document.getElementById('maintenanceMode').addEventListener('change', function() {
        toggleMaintenanceMode(this.checked);
    });
    
    document.getElementById('debugMode').addEventListener('change', function() {
        toggleDebugMode(this.checked);
    });
    
    document.getElementById('autoUpdates').addEventListener('change', function() {
        toggleAutoUpdates(this.checked);
    });
    
    document.getElementById('securityLogs').addEventListener('change', function() {
        toggleSecurityLogs(this.checked);
    });
}

function toggleMaintenanceMode(enabled) {
    if (enabled) {
        showNotification('Maintenance mode enabled', 'warning');
    } else {
        showNotification('Maintenance mode disabled', 'success');
    }
}

function toggleDebugMode(enabled) {
    if (enabled) {
        showNotification('Debug mode enabled', 'info');
    } else {
        showNotification('Debug mode disabled', 'success');
    }
}

function toggleAutoUpdates(enabled) {
    if (enabled) {
        showNotification('Auto updates enabled', 'success');
    } else {
        showNotification('Auto updates disabled', 'warning');
    }
}

function toggleSecurityLogs(enabled) {
    if (enabled) {
        showNotification('Security logging enabled', 'success');
    } else {
        showNotification('Security logging disabled', 'warning');
    }
}

function clearCache(type) {
    showNotification(`Clearing ${type} cache...`, 'info');
    
    // Simulate cache clearing
    setTimeout(() => {
        showNotification(`${type} cache cleared successfully`, 'success');
    }, 1000);
}

function backupDatabase() {
    showNotification('Creating database backup...', 'info');
    
    // Simulate backup creation
    setTimeout(() => {
        showNotification('Database backup created successfully', 'success');
    }, 2000);
}

function optimizeDatabase() {
    showNotification('Optimizing database tables...', 'info');
    
    // Simulate optimization
    setTimeout(() => {
        showNotification('Database optimization completed', 'success');
    }, 1500);
}

function checkDatabase() {
    showNotification('Checking database integrity...', 'info');
    
    // Simulate integrity check
    setTimeout(() => {
        showNotification('Database integrity check completed', 'success');
    }, 1000);
}

function repairDatabase() {
    showNotification('Repairing database tables...', 'info');
    
    // Simulate repair
    setTimeout(() => {
        showNotification('Database repair completed', 'success');
    }, 2000);
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

function testAIProvider() {
    const selectedProvider = document.getElementById('ai_provider').value;
    
    showNotification('Testing AI provider connection...', 'info');
    
    fetch('{{ route("superadmin.ai-provider.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            provider: selectedProvider
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'danger');
        }
    })
    .catch(error => {
        showNotification('Failed to test AI provider: ' + error.message, 'danger');
    });
}
</script>
@endsection
