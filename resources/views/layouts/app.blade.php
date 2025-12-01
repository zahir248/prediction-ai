<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NUJUM') }} - AI Intelligence Platform</title>

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
            transition: transform 0.3s ease-in-out;
        }
        
        .nav.nav-hidden {
            transform: translateY(-100%);
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 56px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .nav-brand {
            font-size: 1.125rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: 32px;
        }
        
        .nav-link {
            color: #64748b;
            text-decoration: none;
            padding: 8px 12px;
            font-weight: 500;
            font-size: 13px;
            border-radius: 6px;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: #374151;
            background-color: #f8fafc;
        }
        
        .nav-link.active {
            color: #667eea;
            background-color: #f0f4ff;
            font-weight: 600;
        }
        
        .nav-dropdown.active .nav-dropdown-toggle {
            color: #667eea;
            background-color: #f0f4ff;
            font-weight: 600;
        }
        
        /* Dropdown menu styles */
        .nav-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .nav-dropdown-toggle {
            color: #64748b;
            text-decoration: none;
            padding: 8px 12px;
            font-weight: 500;
            font-size: 13px;
            border-radius: 6px;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        
        .nav-dropdown-toggle:hover {
            color: #374151;
            background-color: #f8fafc;
        }
        
        .nav-dropdown-toggle::after {
            content: '▼';
            font-size: 10px;
            transition: transform 0.2s ease;
        }
        
        .nav-dropdown.active .nav-dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        .nav-dropdown.has-active-child .nav-dropdown-toggle {
            color: #667eea;
            background-color: #f0f4ff;
            font-weight: 600;
        }
        
        .nav-dropdown.has-active-child .nav-dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        .nav-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 8px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 1000;
            border: 1px solid #e2e8f0;
        }
        
        .nav-dropdown.active .nav-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .nav-dropdown-item {
            display: block;
            padding: 10px 16px;
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .nav-dropdown-item:last-child {
            border-bottom: none;
        }
        
        .nav-dropdown-item:hover {
            color: #374151;
            background-color: #f8fafc;
        }
        
        .nav-dropdown-item.active {
            color: #667eea;
            background-color: #f0f4ff;
            font-weight: 600;
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
            gap: 12px;
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
        
        .mobile-nav-link.active {
            color: #667eea;
            background-color: #f0f4ff;
            font-weight: 600;
        }
        
        .mobile-nav-section.active .mobile-nav-section-header {
            color: #667eea;
            background-color: #f0f4ff;
            font-weight: 600;
        }
        
        .mobile-nav-sub-link.active {
            color: #667eea;
            background-color: #f0f4ff;
            font-weight: 600;
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
        
        /* Mobile dropdown styles */
        .mobile-nav-section {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        
        .mobile-nav-section-header {
            color: #374151;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s ease;
        }
        
        .mobile-nav-section-header:hover {
            background-color: #f1f5f9;
        }
        
        .mobile-nav-arrow {
            font-size: 10px;
            transition: transform 0.2s ease;
        }
        
        .mobile-nav-section.active .mobile-nav-arrow {
            transform: rotate(180deg);
        }
        
        .mobile-nav-section-content {
            padding-top: 8px;
        }
        
        .mobile-nav-sub-link {
            padding-left: 32px !important;
            font-size: 13px !important;
            color: #64748b !important;
        }
        
        .mobile-nav-sub-link:hover {
            background-color: #f8fafc !important;
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
                gap: 24px;
                margin-left: 40px;
            }
            
            .nav-content {
                height: 60px;
                padding: 0 24px;
            }
        }
        
        @media (min-width: 1400px) {
            .nav-links {
                gap: 28px;
                margin-left: 48px;
            }
            
            .nav-content {
                height: 64px;
                padding: 0 28px;
            }
        }
        
        @media (min-width: 1600px) {
            .nav-links {
                gap: 32px;
                margin-left: 56px;
            }
            
            .nav-content {
                height: 64px;
                padding: 0 32px;
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
                width: 36px !important;
                height: 36px !important;
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
                width: 32px !important;
                height: 32px !important;
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
        
        .nav-user a:not(.nav-link):hover {
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
                        <a href="{{ route('dashboard') }}" class="nav-brand" style="display: flex; align-items: center;">
                            <img src="{{ asset('image/logo2.png') }}" alt="NUJUM Logo" style="width: 40px; height: 40px; object-fit: contain;">
                        </a>
                    </div>
                    <div class="nav-links hidden-mobile">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            Home
                        </a>
                        <div class="nav-dropdown {{ request()->routeIs('predictions.*') ? 'has-active-child' : '' }}" id="predictionsDropdown">
                            <a href="#" class="nav-dropdown-toggle" onclick="event.preventDefault(); toggleDropdown('predictionsDropdown');">
                                Predictions Analysis
                            </a>
                            <div class="nav-dropdown-menu">
                                <a href="{{ route('predictions.create') }}" class="nav-dropdown-item {{ request()->routeIs('predictions.create') ? 'active' : '' }}">
                                    Analyze Prediction
                                </a>
                                <a href="{{ route('predictions.history') }}" class="nav-dropdown-item {{ request()->routeIs('predictions.history') ? 'active' : '' }}">
                                    History
                                </a>
                                <a href="{{ route('predictions.analytics') }}" class="nav-dropdown-item {{ request()->routeIs('predictions.analytics') ? 'active' : '' }}">
                                    Analytics
                                </a>
                            </div>
                        </div>
                        <div class="nav-dropdown {{ request()->routeIs('social-media.*') ? 'has-active-child' : '' }}" id="socialMediaDropdown">
                            <a href="#" class="nav-dropdown-toggle" onclick="event.preventDefault(); toggleDropdown('socialMediaDropdown');">
                            Social Media Analysis
                        </a>
                            <div class="nav-dropdown-menu">
                                <a href="{{ route('social-media.index') }}" class="nav-dropdown-item {{ request()->routeIs('social-media.index') ? 'active' : '' }}">
                                    Analyze Profile
                                </a>
                                <a href="{{ route('social-media.history') }}" class="nav-dropdown-item {{ request()->routeIs('social-media.history') ? 'active' : '' }}">
                                    History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="nav-user hidden-mobile">
                    @auth
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                Profile
                            </a>
                            <button type="button" onclick="showLogoutModal()" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; transition: background-color 0.2s ease;">
                                Logout
                            </button>
                        </div>
                    @else
                        <a href="{{ route('login') }}" style="padding: 6px 12px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; transition: background-color 0.2s ease;">
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
                    <a href="{{ route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Home
                    </a>
                    <div class="mobile-nav-section {{ request()->routeIs('predictions.*') ? 'active has-active-child' : '' }}" id="mobilePredictionsSection">
                        <div class="mobile-nav-section-header" onclick="toggleMobileDropdown('mobilePredictionsDropdown', 'mobilePredictionsSection')">
                            Predictions Analysis
                            <span class="mobile-nav-arrow">▼</span>
                        </div>
                        <div class="mobile-nav-section-content" id="mobilePredictionsDropdown" style="display: none;">
                            <a href="{{ route('predictions.create') }}" class="mobile-nav-link mobile-nav-sub-link {{ request()->routeIs('predictions.create') ? 'active' : '' }}">
                                Analyze Prediction
                            </a>
                            <a href="{{ route('predictions.history') }}" class="mobile-nav-link mobile-nav-sub-link {{ request()->routeIs('predictions.history') ? 'active' : '' }}">
                                History
                            </a>
                            <a href="{{ route('predictions.analytics') }}" class="mobile-nav-link mobile-nav-sub-link {{ request()->routeIs('predictions.analytics') ? 'active' : '' }}">
                                Analytics
                            </a>
                        </div>
                    </div>
                    <div class="mobile-nav-section {{ request()->routeIs('social-media.*') ? 'active has-active-child' : '' }}" id="mobileSocialMediaSection">
                        <div class="mobile-nav-section-header" onclick="toggleMobileDropdown('mobileSocialMediaDropdown', 'mobileSocialMediaSection')">
                        Social Media Analysis
                            <span class="mobile-nav-arrow">▼</span>
                        </div>
                        <div class="mobile-nav-section-content" id="mobileSocialMediaDropdown" style="display: {{ request()->routeIs('social-media.*') ? 'block' : 'none' }};">
                            <a href="{{ route('social-media.index') }}" class="mobile-nav-link mobile-nav-sub-link {{ request()->routeIs('social-media.index') ? 'active' : '' }}">
                                Analyze Profile
                    </a>
                            <a href="{{ route('social-media.history') }}" class="mobile-nav-link mobile-nav-sub-link {{ request()->routeIs('social-media.history') ? 'active' : '' }}">
                                History
                            </a>
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('profile.show') }}" class="mobile-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            Profile
                        </a>
                        <button type="button" onclick="showLogoutModal()" class="mobile-nav-link">
                            Logout
                        </button>
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
        
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                // Only toggle 'active' class, keep 'has-active-child' if it exists
                dropdown.classList.toggle('active');
            }
        }
        
        function toggleMobileDropdown(dropdownId, sectionId) {
            const dropdown = document.getElementById(dropdownId);
            const section = document.getElementById(sectionId);
            
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
                section.classList.add('active');
            } else {
                dropdown.style.display = 'none';
                // Don't remove 'active' class if section has 'has-active-child' class - preserve highlighting
                if (!section.classList.contains('has-active-child')) {
                    section.classList.remove('active');
                }
            }
        }
        
        // Close dropdown when clicking outside (but preserve highlighting)
        document.addEventListener('click', function(event) {
            const predictionsDropdown = document.getElementById('predictionsDropdown');
            const socialMediaDropdown = document.getElementById('socialMediaDropdown');
            
            if (predictionsDropdown && !predictionsDropdown.contains(event.target)) {
                // Only remove 'active' class, keep 'has-active-child' for highlighting
                predictionsDropdown.classList.remove('active');
            }
            
            if (socialMediaDropdown && !socialMediaDropdown.contains(event.target)) {
                // Only remove 'active' class, keep 'has-active-child' for highlighting
                socialMediaDropdown.classList.remove('active');
            }
        });
        
        // Keep dropdown highlighted (but closed) if on a predictions route
        document.addEventListener('DOMContentLoaded', function() {
            const predictionsDropdown = document.getElementById('predictionsDropdown');
            const socialMediaDropdown = document.getElementById('socialMediaDropdown');
            
            @if(request()->routeIs('predictions.*'))
                if (predictionsDropdown) {
                    predictionsDropdown.classList.add('has-active-child');
                    // Don't auto-open, just keep it highlighted
                }
            @endif
            
            @if(request()->routeIs('social-media.*'))
                if (socialMediaDropdown) {
                    socialMediaDropdown.classList.add('has-active-child');
                    // Don't auto-open, just keep it highlighted
                }
            @endif
        });
        
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
        
        // Navbar hide on scroll down functionality
        (function() {
            const nav = document.querySelector('.nav');
            let lastScrollTop = 0;
            let scrollThreshold = 10; // Minimum scroll distance to trigger hide/show
            let ticking = false;
            
            if (!nav) return;
            
            function updateNavbar() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Always show navbar at the top of the page
                if (scrollTop <= scrollThreshold) {
                    nav.classList.remove('nav-hidden');
                } else {
                    // Hide when scrolling down, show when scrolling up
                    if (scrollTop > lastScrollTop) {
                        // Scrolling down - hide navbar
                        nav.classList.add('nav-hidden');
                    } else {
                        // Scrolling up - show navbar
                        nav.classList.remove('nav-hidden');
                    }
                }
                
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                ticking = false;
            }
            
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(updateNavbar);
                    ticking = true;
                }
            }, { passive: true });
        })();
    </script>
</body>
</html>
