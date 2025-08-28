<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NUJUM') }} - Super Admin Panel</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo3.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('image/logo3.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/logo3.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('image/logo3.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/logo3.png') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --top-navbar-height: 60px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #991b1b 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        /* Mobile Sidebar */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 100%;
                max-width: 320px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .sidebar-backdrop {
                display: none;
            }

            .sidebar-backdrop.show {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
        }



        /* Hide sidebar toggle on all screens - not needed */
        .sidebar-toggle {
            display: none !important;
        }

        .brand-section {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(220, 38, 38, 0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .user-info {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(220, 38, 38, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .nav {
            padding: 1rem 0;
            flex-grow: 1;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: rgba(255, 255, 255, 0.3);
        }

        .nav-link.active {
            color: white;
            background: rgba(220, 38, 38, 0.2);
            border-left-color: #dc2626;
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .bottom-actions {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            background: rgba(220, 38, 38, 0.3);
            border: 1px solid rgba(220, 38, 38, 0.4);
            color: white;
            font-size: 1rem;
            padding: 0.375rem 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(220, 38, 38, 0.4);
            border-color: rgba(220, 38, 38, 0.5);
        }

        .sidebar-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        /* Page Content */
        .page-content {
            padding: 1.5rem;
        }

        /* Mobile Responsiveness */
        @media (max-width: 576px) {
            .page-content {
                padding: 1rem 0.75rem;
            }

            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .page-content {
                padding: 1rem;
            }
        }

        /* Responsive Typography */
        @media (max-width: 576px) {
            h1.h3 {
                font-size: 1.5rem !important;
            }

            h2 {
                font-size: 1.75rem !important;
            }

            .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            h1.h3 {
                font-size: 1.75rem !important;
            }

            .card-body {
                padding: 1.25rem;
            }
        }

        /* Responsive Tables */
        @media (max-width: 768px) {
            .table-responsive {
                border: 0;
            }

            .table-responsive .table {
                font-size: 0.875rem;
            }

            .table-responsive .table td,
            .table-responsive .table th {
                padding: 0.5rem 0.375rem;
            }

            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        /* Responsive Cards */
        @media (max-width: 576px) {
            .card {
                margin-bottom: 1rem;
            }

            .row.g-3 {
                --bs-gutter-y: 0.75rem;
            }

            .col-12.col-sm-6.col-lg-3 {
                margin-bottom: 0.75rem;
            }
        }

        /* Responsive Forms */
        @media (max-width: 768px) {
            .form-control,
            .form-select {
                font-size: 16px; /* Prevents zoom on iOS */
            }

            .input-group {
                flex-direction: column;
            }

            .input-group .form-control,
            .input-group .form-select {
                border-radius: 0.375rem !important;
                margin-bottom: 0.5rem;
            }

            .input-group .input-group-text {
                border-radius: 0.375rem !important;
                margin-bottom: 0.5rem;
            }
        }

        /* Responsive Modals */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                padding: 0.75rem 1rem;
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
                margin: 0.25rem 0;
            }
        }

        /* Responsive Buttons */
        @media (max-width: 576px) {
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
            }
        }

        /* Responsive Navigation */
        @media (max-width: 768px) {
            .nav-link {
                padding: 1rem 1.25rem;
                font-size: 1rem;
            }

            .nav-link i {
                font-size: 1.25rem;
                margin-right: 1rem;
            }
        }

        /* Responsive Stats Cards */
        @media (max-width: 576px) {
            .stats-card .card-body {
                padding: 1rem;
            }

            .stats-card h2 {
                font-size: 1.5rem;
            }

            .stats-card h6 {
                font-size: 0.875rem;
            }
        }

        /* Responsive Search and Filter */
        @media (max-width: 768px) {
            .search-filter-row .col-12.col-md-4,
            .search-filter-row .col-12.col-md-3,
            .search-filter-row .col-12.col-md-2 {
                margin-bottom: 0.75rem;
            }

            .search-filter-row .col-12.col-md-2:last-child {
                margin-bottom: 0;
            }
        }

        /* Responsive Pagination */
        @media (max-width: 576px) {
            .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }

            .pagination .page-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Backdrop for Mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="d-flex flex-column h-100">
                <!-- Brand Section -->
                <div class="brand-section">
                    <div class="d-flex align-items-center">
                        <div class="brand-icon me-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="brand-text">
                            <h5 class="text-white mb-0 fw-bold">Super Admin</h5>
                            <small class="text-white-50">System Control</small>
                        </div>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="user-info">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-white mb-1 fw-semibold">{{ Auth::user()->name }}</h6>
                            <small class="text-white-50">{{ ucfirst(Auth::user()->role) }}</small>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <ul class="nav flex-column flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.admins.*') ? 'active' : '' }}" href="{{ route('superadmin.admins.index') }}">
                            <i class="bi bi-people"></i>
                            <span>Admins</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}" href="{{ route('superadmin.users.index') }}">
                            <i class="bi bi-person-badge"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.predictions.*') ? 'active' : '' }}" href="{{ route('superadmin.predictions.index') }}">
                            <i class="bi bi-graph-up"></i>
                            <span>Predictions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}" href="{{ route('superadmin.settings') }}">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}" href="{{ route('superadmin.logs') }}">
                            <i class="bi bi-journal-text"></i>
                            <span>System Logs</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Bottom Actions -->
                <div class="bottom-actions">
                    <button class="btn btn-outline-light btn-sm w-100" onclick="showLogoutModal()">
                        <i class="bi bi-box-arrow-right me-2"></i><span>Logout</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Mobile Menu Button -->
            <div class="d-md-none p-3">
                <button class="btn btn-outline-primary" id="mobileMenuToggle">
                    <i class="bi bi-list me-2"></i>Menu
                </button>
            </div>
            
            <!-- Page Content -->
            <div class="page-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
        
        <!-- Logout Confirmation Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-warning">
                            <i class="bi bi-box-arrow-right me-2"></i>Confirm Logout
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-question-circle text-warning fs-1"></i>
                            </div>
                            <h6 class="text-warning mb-2">Are you sure you want to logout?</h6>
                            <p class="text-muted mb-0">You will be redirected to the superadmin login page</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('superadmin.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            
            // Mobile: Toggle sidebar visibility
            function toggleMobileSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                    sidebarBackdrop.classList.toggle('show');
                }
            }
            
            // Event listeners
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    toggleMobileSidebar();
                });
            }
            
            sidebarBackdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                }
            });
        });

        function showLogoutModal() {
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            logoutModal.show();
        }
    </script>
</body>
</html>
