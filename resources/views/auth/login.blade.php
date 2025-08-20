@extends('layouts.auth')

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 48px 16px;">
    <div style="max-width: 420px; width: 100%;">
        <!-- Logo/Brand Section -->
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="width: 64px; height: 64px; background: rgba(255, 255, 255, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; backdrop-filter: blur(10px);">
                <span style="font-size: 28px; font-weight: bold; color: white;">🤖</span>
            </div>
            <h1 style="font-size: 28px; font-weight: 700; color: white; margin-bottom: 8px;">AI Predictions</h1>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 16px;">Sign in to your account</p>
        </div>

        <!-- Login Form Card -->
        <div style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); backdrop-filter: blur(10px);">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <!-- Email Field -->
                <div style="margin-bottom: 24px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Email Address
                    </label>
                    <div style="position: relative;">
                        <input id="email" name="email" type="email" required 
                               style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;"
                               placeholder="Enter your email"
                               value="{{ old('email') }}">
                        <div style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;">
                            📧
                        </div>
                    </div>
                </div>

                <!-- Password Field -->
                <div style="margin-bottom: 32px;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Password
                    </label>
                    <div style="position: relative;">
                        <input id="password" name="password" type="password" required 
                               style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;"
                               placeholder="Enter your password">
                        <div style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;">
                            🔒
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="margin-right: 8px;">⚠️</span>
                            <span style="font-weight: 600; color: #991b1b; font-size: 14px;">Authentication Error</span>
                        </div>
                        @foreach ($errors->all() as $error)
                            <p style="color: #dc2626; font-size: 14px; margin: 0; line-height: 1.4;">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" 
                        style="width: 100%; padding: 16px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                    Sign In
                </button>

                <!-- Register Link -->
                <div style="text-align: center; padding-top: 24px; border-top: 1px solid #f3f4f6;">
                    <p style="color: #6b7280; font-size: 14px; margin-bottom: 8px;">Don't have an account?</p>
                    <a href="{{ route('register') }}" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.3s ease; text-transform: uppercase; letter-spacing: 0.5px;">
                        Create Account →
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 32px;">
            <p style="color: rgba(255, 255, 255, 0.7); font-size: 12px;">
                © 2024 AI Predictions. All rights reserved.
            </p>
        </div>
    </div>
</div>

<style>
    /* Responsive design improvements */
    @media (max-width: 768px) {
        div[style*="padding: 48px 16px"] {
            padding: 32px 12px !important;
        }
        
        div[style*="max-width: 420px"] {
            max-width: 100% !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 32px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 32px 24px !important;
        }
        
        div[style*="font-size: 28px"] {
            font-size: 24px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 14px !important;
        }
        
        div[style*="margin-bottom: 24px"] {
            margin-bottom: 20px !important;
        }
        
        div[style*="margin-bottom: 32px"] {
            margin-bottom: 24px !important;
        }
        
        div[style*="padding: 16px 20px"] {
            padding: 14px 16px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 15px !important;
        }
        
        div[style*="padding: 16px 24px"] {
            padding: 14px 20px !important;
        }
        
        div[style*="margin-top: 32px"] {
            margin-top: 24px !important;
        }
    }
    
    @media (max-width: 640px) {
        div[style*="padding: 48px 16px"] {
            padding: 24px 8px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 24px 20px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 24px !important;
        }
        
        div[style*="font-size: 28px"] {
            font-size: 22px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 13px !important;
        }
        
        div[style*="margin-bottom: 24px"] {
            margin-bottom: 16px !important;
        }
        
        div[style*="margin-bottom: 32px"] {
            margin-bottom: 20px !important;
        }
        
        div[style*="padding: 16px 20px"] {
            padding: 12px 14px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 14px !important;
        }
        
        div[style*="padding: 16px 24px"] {
            padding: 12px 18px !important;
        }
        
        div[style*="margin-top: 32px"] {
            margin-top: 20px !important;
        }
        
        div[style*="width: 64px"] {
            width: 56px !important;
        }
        
        div[style*="height: 64px"] {
            height: 56px !important;
        }
        
        div[style*="font-size: 28px"] {
            font-size: 24px !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 48px 16px"] {
            padding: 20px 4px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 20px 16px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 20px !important;
        }
        
        div[style*="font-size: 28px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 12px !important;
        }
        
        div[style*="margin-bottom: 24px"] {
            margin-bottom: 12px !important;
        }
        
        div[style*="margin-bottom: 32px"] {
            margin-bottom: 16px !important;
        }
        
        div[style*="padding: 16px 20px"] {
            padding: 10px 12px !important;
        }
        
        div[style*="font-size: 16px"] {
            font-size: 13px !important;
        }
        
        div[style*="padding: 16px 24px"] {
            padding: 10px 16px !important;
        }
        
        div[style*="margin-top: 32px"] {
            margin-top: 16px !important;
        }
        
        div[style*="width: 64px"] {
            width: 48px !important;
        }
        
        div[style*="height: 64px"] {
            height: 48px !important;
        }
        
        div[style*="font-size: 28px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 11px !important;
        }
        
        div[style*="font-size: 12px"] {
            font-size: 10px !important;
        }
    }
    
    /* Touch-friendly improvements for mobile */
    @media (max-width: 768px) {
        input, button, a {
            min-height: 44px;
        }
        
        input {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
    }
    
    /* Focus and hover effects */
    input:focus {
        outline: none;
        border-color: #667eea !important;
        background: white !important;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
        transform: translateY(-2px);
    }
    
    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5) !important;
    }
    
    a:hover {
        color: #5a67d8 !important;
    }
    
    /* Smooth transitions */
    * {
        transition: all 0.3s ease;
    }
    
    /* Improve form accessibility on mobile */
    @media (max-width: 768px) {
        label {
            font-size: 13px !important;
        }
        
        input::placeholder {
            font-size: 14px !important;
        }
    }
</style>
@endsection
