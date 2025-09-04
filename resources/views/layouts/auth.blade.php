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
        
        .btn-outline {
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }
        
        .btn-outline:hover {
            background: #f9fafb;
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
        
        .hover\:bg-blue-700:hover { background-color: #1d4ed8; }
        .hover\:bg-gray-700:hover { background-color: #4b5563; }
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
            .container {
                padding: 0 20px;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }
            
            .text-4xl { font-size: 28px; }
            .text-3xl { font-size: 24px; }
            .text-2xl { font-size: 20px; }
            .text-xl { font-size: 18px; }
            
            .py-8 { padding-top: 24px; padding-bottom: 24px; }
            .py-12 { padding-top: 32px; padding-bottom: 32px; }
            
            .px-4 { padding-left: 12px; padding-right: 12px; }
            .px-6 { padding-left: 16px; padding-right: 16px; }
            .px-8 { padding-left: 20px; padding-right: 20px; }
        }
        
        @media (max-width: 640px) {
            .container {
                padding: 0 12px;
            }
            
            .py-8 { padding-top: 20px; padding-bottom: 20px; }
            .py-12 { padding-top: 28px; padding-bottom: 28px; }
            
            .px-4 { padding-left: 8px; padding-right: 8px; }
            .px-6 { padding-left: 12px; padding-right: 12px; }
            .px-8 { padding-left: 16px; padding-right: 16px; }
            
            .mb-8 { margin-bottom: 24px; }
            .mb-12 { margin-bottom: 32px; }
            
            .mt-8 { margin-top: 24px; }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 8px;
            }
            
            .btn {
                padding: 6px 12px;
                font-size: 13px;
            }
            
            .form-input {
                padding: 8px 10px;
                font-size: 13px;
            }
            
            .py-8 { padding-top: 16px; padding-bottom: 16px; }
            .py-12 { padding-top: 24px; padding-bottom: 24px; }
            
            .px-4 { padding-left: 6px; padding-right: 6px; }
            .px-6 { padding-left: 8px; padding-right: 8px; }
            .px-8 { padding-left: 12px; padding-right: 12px; }
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

            @yield('content')
        </main>
    </div>
</body>
</html>
