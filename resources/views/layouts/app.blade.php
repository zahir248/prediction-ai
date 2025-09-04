<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NUJUM') }} - AI Prediction System</title>

    <!-- Favicon - Chrome Compatible -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ time() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Responsive CSS -->
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f3f4f6;
            color: #374151;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Mobile-first responsive container */
        @media (max-width: 640px) {
            .container {
                padding: 0 16px;
            }
        }
        
        .nav {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .nav-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-links {
            display: flex;
            gap: 32px;
            margin-left: 48px;
        }
        
        .nav-link {
            color: #64748b;
            text-decoration: none;
            padding: 12px 16px;
            font-weight: 500;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: #374151;
            background-color: #f8fafc;
        }
        
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal {
            background: white;
            border-radius: 12px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            text-align: center;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 16px;
        }
        
        .modal-message {
            color: #6b7280;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        
        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .modal-btn-cancel {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .modal-btn-cancel:hover {
            background-color: #e5e7eb;
        }
        
        .modal-btn-confirm {
            background-color: #ef4444;
            color: white;
        }
        
        .modal-btn-confirm:hover {
            background-color: #dc2626;
        }
        
        .nav-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        /* Mobile navigation */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #374151;
            cursor: pointer;
            padding: 8px;
            margin-left: auto;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        
        .mobile-menu-toggle:hover {
            background-color: #f1f5f9;
        }
        
        .mobile-menu-toggle svg {
            width: 24px;
            height: 24px;
        }
        
        .mobile-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 16px;
        }
        
        .mobile-nav.active {
            display: block;
        }
        
        .mobile-nav-links {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        /* Mobile nav user section */
        .mobile-nav-user {
            border-top: 1px solid #e2e8f0;
            margin-top: 16px;
            padding-top: 16px;
        }
        
        .mobile-nav-username {
            margin-bottom: 12px;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }
        
        .mobile-nav-link {
            color: #374151;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .mobile-nav-link:hover {
            background-color: #f1f5f9;
        }
        
        /* Mobile logout button styling */
        .mobile-nav-link[type="submit"] {
            background: #ef4444 !important;
            color: white !important;
            border: none !important;
            width: 100% !important;
            text-align: left !important;
            font-weight: 500 !important;
            transition: background-color 0.2s !important;
            border-radius: 8px !important;
            padding: 12px 16px !important;
        }
        
        .mobile-nav-link[type="submit"]:hover {
            background: #dc2626 !important;
        }
        
        /* Ensure mobile nav has proper spacing */
        @media (max-width: 768px) {
            .mobile-nav {
                padding: 20px 16px;
            }
            
            .mobile-nav-links {
                gap: 20px;
            }
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .btn-outline {
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }
        
        .btn-outline:hover {
            background: #f9fafb;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .card-header {
            margin-bottom: 16px;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .card-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-select {
            background: white;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        
        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        
        .alert-warning {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .badge-green {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-yellow {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge-purple {
            background: #e9d5ff;
            color: #7c3aed;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table th {
            background: #f9fafb;
            font-weight: 600;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .table tr:hover {
            background: #f9fafb;
        }
        
        /* Responsive table */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .grid {
            display: grid;
            gap: 24px;
        }
        
        .grid-cols-1 { grid-template-columns: 1fr; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        
        .flex {
            display: flex;
        }
        
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .justify-end { justify-content: flex-end; }
        
        .space-x-4 > * + * { margin-left: 16px; }
        .space-y-4 > * + * { margin-top: 16px; }
        .space-y-6 > * + * { margin-top: 24px; }
        .space-y-8 > * + * { margin-top: 32px; }
        
        .text-center { text-align: center; }
        .text-sm { font-size: 14px; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 20px; }
        .text-2xl { font-size: 24px; }
        .text-3xl { font-size: 30px; }
        .text-4xl { font-size: 36px; }
        
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        
        .text-gray-500 { color: #6b7280; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-700 { color: #374151; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-900 { color: #111827; }
        .text-blue-600 { color: #2563eb; }
        .text-green-600 { color: #059669; }
        .text-red-600 { color: #dc2626; }
        
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mb-8 { margin-bottom: 32px; }
        .mb-12 { margin-bottom: 48px; }
        
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .mt-8 { margin-top: 32px; }
        
        .py-8 { padding-top: 32px; padding-bottom: 32px; }
        .py-12 { padding-top: 48px; padding-bottom: 48px; }
        
        .px-4 { padding-left: 16px; padding-right: 16px; }
        .px-6 { padding-left: 24px; padding-right: 24px; }
        .px-8 { padding-left: 32px; padding-right: 32px; }
        
        .max-w-4xl { max-width: 896px; }
        .max-w-6xl { max-width: 1152px; }
        .max-w-7xl { max-width: 1280px; }
        
        .mx-auto { margin-left: auto; margin-right: auto; }
        
        .w-full { width: 100%; }
        .w-16 { width: 64px; }
        .w-32 { width: 128px; }
        
        .h-16 { height: 64px; }
        .h-12 { height: 48px; }
        .h-8 { height: 32px; }
        
        .rounded-lg { border-radius: 8px; }
        .rounded-md { border-radius: 6px; }
        .rounded-full { border-radius: 9999px; }
        
        .overflow-hidden { overflow: hidden; }
        .overflow-x-auto { overflow-x: auto; }
        
        .whitespace-nowrap { white-space: nowrap; }
        .whitespace-pre-wrap { white-space: pre-wrap; }
        
        .break-all { word-break: break-all; }
        
        .transition { transition: all 0.2s; }
        
        .hover\:bg-gray-50:hover { background-color: #f9fafb; }
        .hover\:bg-blue-700:hover { background-color: #1d4ed8; }
        .hover\:bg-gray-700:hover { background-color: #4b5563; }
        .hover\:bg-green-700:hover { background-color: #047857; }
        .hover\:bg-purple-700:hover { background-color: #7c3aed; }
        .hover\:shadow-lg:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .hover\:text-blue-900:hover { color: #1e3a8a; }
        .hover\:text-gray-700:hover { color: #374151; }
        .hover\:text-blue-500:hover { color: #3b82f6; }
        
        .focus\:outline-none:focus { outline: none; }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.5); }
        .focus\:ring-blue-500:focus { box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.5); }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Responsive breakpoints */
        @media (max-width: 1024px) {
            .nav-content {
                padding: 0 20px;
            }
            
            .nav-links {
                gap: 20px;
                margin-left: 32px;
            }
        }
        
        /* Large screen optimizations */
        @media (min-width: 1200px) {
            .nav-links {
                gap: 40px;
                margin-left: 64px;
            }
            
            .nav-content {
                height: 80px;
                padding: 0 32px;
            }
            
            .nav-brand {
                font-size: 1.5rem;
            }
        }
        
        @media (min-width: 1400px) {
            .nav-links {
                gap: 48px;
                margin-left: 80px;
            }
            
            .nav-content {
                height: 88px;
                padding: 0 40px;
            }
            
            .nav-brand {
                font-size: 1.625rem;
            }
        }
        
        @media (min-width: 1600px) {
            .nav-links {
                gap: 56px;
                margin-left: 96px;
            }
            
            .nav-content {
                height: 96px;
                padding: 0 48px;
            }
        }
        
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .mobile-menu-toggle {
                display: block;
                margin-left: auto;
            }
            
            .nav-content {
                padding: 0 16px;
            }
            
            .nav-brand {
                font-size: 1.125rem;
            }
            
            .nav-brand img {
                width: 48px !important;
                height: 48px !important;
            }
            
            .nav-user {
                gap: 12px;
            }
            
            .grid-cols-2,
            .grid-cols-3,
            .grid-cols-4 {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 20px;
            }
            
            .text-4xl { font-size: 28px; }
            .text-3xl { font-size: 24px; }
            .text-2xl { font-size: 20px; }
            .text-xl { font-size: 18px; }
        }
        
        @media (max-width: 640px) {
            .nav-content {
                padding: 0 12px;
            }
            
            .nav-brand {
                font-size: 1rem;
            }
            
            .nav-brand img {
                width: 40px !important;
                height: 40px !important;
            }
            
            .card {
                padding: 16px;
                margin-bottom: 16px;
            }
            
            .py-8 { padding-top: 24px; padding-bottom: 24px; }
            .py-12 { padding-top: 32px; padding-bottom: 32px; }
            
            .px-4 { padding-left: 12px; padding-right: 12px; }
            .px-6 { padding-left: 16px; padding-right: 16px; }
            .px-8 { padding-left: 20px; padding-right: 20px; }
            
            .space-x-4 > * + * { margin-left: 12px; }
            .space-y-6 > * + * { margin-top: 20px; }
            .space-y-8 > * + * { margin-top: 28px; }
        }
        
        @media (max-width: 480px) {
            .nav-content {
                padding: 0 8px;
            }
            
            .nav-brand img {
                width: 32px !important;
                height: 32px !important;
            }
            

            
            .nav-user {
                gap: 8px;
            }
            
            .card {
                padding: 12px;
            }
            
            .btn {
                padding: 6px 12px;
                font-size: 13px;
            }
            
            .form-input {
                padding: 8px 10px;
                font-size: 13px;
            }
        }
        
        /* Simple hover effects */
        .nav-user button:hover {
            background-color: #dc2626;
        }
        
        .nav-user a:hover {
            background-color: #5a67d8;
        }
        
        /* Hide elements on mobile */
        .hidden-mobile {
            display: block;
        }
        
        @media (max-width: 768px) {
            .hidden-mobile {
                display: none;
            }
        }
        
        /* Show elements only on mobile */
        .mobile-only {
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-only {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="nav">
            <div class="nav-content">
                <div style="display: flex; align-items: center;">
                    <div class="nav-brand">
                        <a href="{{ route('predictions.index') }}" class="nav-brand" style="display: flex; align-items: center;">
                            <img src="{{ asset('image/logo2.png') }}" alt="NUJUM Logo" style="width: 60px; height: 60px; object-fit: contain;">
                        </a>
                    </div>
                    <div class="nav-links hidden-mobile">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            Dashboard
                        </a>
                        <a href="{{ route('predictions.analytics') }}" class="nav-link">
                            Analytics
                        </a>
                        <a href="{{ route('predictions.create') }}" class="nav-link">
                            New Prediction
                        </a>
                        <a href="{{ route('predictions.history') }}" class="nav-link">
                            History
                        </a>
                    </div>
                </div>
                
                <div class="nav-user hidden-mobile">
                    @auth
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <span style="color: #64748b; font-size: 14px;">{{ Auth::user()->name }}</span>
                            <button type="button" onclick="showLogoutModal()" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; transition: background-color 0.2s ease;">
                                Logout
                            </button>
                        </div>
                    @else
                        <a href="{{ route('login') }}" style="padding: 8px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; transition: background-color 0.2s ease;">
                            Login
                        </a>
                    @endauth
                </div>
                
                <!-- Mobile menu toggle -->
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle mobile menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile navigation menu -->
            <div class="mobile-nav" id="mobileNav">
                <div class="mobile-nav-links">
                    <a href="{{ route('dashboard') }}" class="mobile-nav-link">
                        Dashboard
                    </a>
                    <a href="{{ route('predictions.analytics') }}" class="mobile-nav-link">
                        Analytics
                    </a>
                    <a href="{{ route('predictions.create') }}" class="mobile-nav-link">
                        New Prediction
                    </a>
                    <a href="{{ route('predictions.history') }}" class="mobile-nav-link">
                        History
                    </a>
                    @auth
                        <div class="mobile-nav-user">
                            <div class="mobile-nav-username">
                                {{ Auth::user()->name }}
                            </div>
                            <button type="button" onclick="showLogoutModal()" class="mobile-nav-link">
                                Logout
                            </button>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @if(session('success'))
                <div class="container mt-4">
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="container mt-4">
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="container mt-4">
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    
    <!-- Logout Confirmation Modal -->
    <div class="modal-overlay" id="logoutModal">
        <div class="modal">
            <div class="modal-title">Confirm Logout</div>
            <div class="modal-message">Are you sure you want to logout? You will need to login again to access your account.</div>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="hideLogoutModal()">Cancel</button>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="modal-btn modal-btn-confirm">Logout</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('active');
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileNav = document.getElementById('mobileNav');
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            
            if (!mobileNav.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                mobileNav.classList.remove('active');
            }
        });
        
        // Close mobile menu on window resize
        
        // Logout modal functions
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.add('active');
        }
        
        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.remove('active');
        }
        
        // Close modal when clicking outside
        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                hideLogoutModal();
            }
        });
        
        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideLogoutModal();
            }
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('mobileNav').classList.remove('active');
            }
        });
    </script>
</body>
</html>
