<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NUJUM') }} - Super Admin Login</title>

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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow: hidden;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #991b1b 100%);
            position: relative;
        }
        
        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 2;
        }
        
        .brand-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.4);
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .form-control:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.2);
            background: white;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.5);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .form-check-input {
            border-radius: 6px;
            border: 2px solid #e2e8f0;
        }
        
        .form-check-input:checked {
            background-color: #dc2626;
            border-color: #dc2626;
        }
        
        .background-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            background: rgba(220, 38, 38, 0.15);
            border-radius: 50%;
        }
        
        .shape-1 {
            width: 200px;
            height: 200px;
            top: -100px;
            right: -100px;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape-2 {
            width: 150px;
            height: 150px;
            bottom: -75px;
            left: -75px;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        .shape-3 {
            width: 100px;
            height: 100px;
            top: 50%;
            left: -50px;
            animation: float 10s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 576px) {
            .login-container {
                padding: 0.5rem;
            }
            
            .login-card {
                border-radius: 15px;
                max-width: 100%;
            }
            
            .brand-icon {
                width: 60px;
                height: 60px;
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
            
            h2 {
                font-size: 1.5rem !important;
            }
        }
        
        @media (max-height: 700px) {
            .login-container {
                padding: 0.5rem;
            }
            
            .brand-icon {
                width: 60px;
                height: 60px;
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
        }
        
        /* Prevent scrollbars */
        .login-content {
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }
        
        /* Custom scrollbar for content if needed */
        .login-content::-webkit-scrollbar {
            width: 4px;
        }
        
        .login-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
        }
        
        .login-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <!-- Background Shapes -->
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-content">
                <div class="card-body p-4 p-md-5">
                    <!-- Brand -->
                    <div class="text-center mb-4">
                        <div class="brand-icon">
                            <i class="bi bi-shield-check text-white fs-2"></i>
                        </div>
                        <h2 class="h3 fw-bold text-dark mb-2">Super Admin Access</h2>
                        <p class="text-muted mb-0">Sign in to access the super admin panel</p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('superadmin.login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-dark">
                                Email Address
                            </label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-dark">
                                Password
                            </label>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                required autocomplete="current-password" placeholder="Enter your password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input id="remember" name="remember" type="checkbox" class="form-check-input">
                                <label for="remember" class="form-check-label text-dark">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-login">
                                <i class="bi bi-shield-check me-2"></i>
                                Access Super Admin Panel
                            </button>
                        </div>
                    </form>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Need help? Contact your system administrator
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
