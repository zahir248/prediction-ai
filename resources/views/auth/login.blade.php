@extends('layouts.auth')

@section('content')
<div style="height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 16px; overflow: hidden;">
    <div style="max-width: 400px; width: 100%;">
        <!-- Logo/Brand Section -->
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; backdrop-filter: blur(10px);">
                <span style="font-size: 24px; font-weight: bold; color: white;">🤖</span>
            </div>
            <h1 style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 6px;">AI Predictions</h1>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 14px;">Sign in to your account</p>
        </div>

        <!-- Login Form Card -->
        <div style="background: white; border-radius: 18px; padding: 28px; box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25); backdrop-filter: blur(10px);">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <!-- Email Field -->
                <div style="margin-bottom: 18px;">
                    <label for="email" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Email Address
                    </label>
                    <input id="email" name="email" type="email" required 
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;"
                           placeholder="Enter your email"
                           value="{{ old('email') }}">
                </div>

                <!-- Password Field -->
                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Password
                    </label>
                    <input id="password" name="password" type="password" required 
                           style="width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;"
                           placeholder="Enter your password">
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-bottom: 18px;">
                        <div style="display: flex; align-items: center; margin-bottom: 6px;">
                            <span style="margin-right: 6px;">⚠️</span>
                            <span style="font-weight: 600; color: #991b1b; font-size: 13px;">Authentication Error</span>
                        </div>
                        @foreach ($errors->all() as $error)
                            <p style="color: #dc2626; font-size: 13px; margin: 0; line-height: 1.3;">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" 
                        style="width: 100%; padding: 14px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                    Sign In
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px;">
            <p style="color: rgba(255, 255, 255, 0.7); font-size: 11px;">
                © 2024 AI Predictions. All rights reserved.
            </p>
        </div>
    </div>
</div>

<style>
    /* Ensure no scrolling */
    body, html {
        overflow: hidden;
        height: 100vh;
        margin: 0;
        padding: 0;
    }
    
    /* Responsive design improvements */
    @media (max-width: 768px) {
        div[style*="padding: 20px 16px"] {
            padding: 16px 12px !important;
        }
        
        div[style*="max-width: 400px"] {
            max-width: 100% !important;
        }
        
        div[style*="margin-bottom: 24px"] {
            margin-bottom: 20px !important;
        }
        
        div[style*="padding: 28px"] {
            padding: 24px 20px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 22px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 13px !important;
        }
        
        div[style*="margin-bottom: 18px"] {
            margin-bottom: 16px !important;
        }
        
        div[style*="margin-bottom: 20px"] {
            margin-bottom: 18px !important;
        }
        
        div[style*="padding: 14px 16px"] {
            padding: 12px 14px !important;
        }
        
        div[style*="font-size: 15px"] {
            font-size: 14px !important;
        }
        
        div[style*="padding: 14px 20px"] {
            padding: 12px 18px !important;
        }
        
        div[style*="margin-top: 20px"] {
            margin-top: 16px !important;
        }
    }
    
    @media (max-width: 640px) {
        div[style*="padding: 20px 16px"] {
            padding: 12px 8px !important;
        }
        
        div[style*="padding: 28px"] {
            padding: 20px 16px !important;
        }
        
        div[style*="margin-bottom: 24px"] {
            margin-bottom: 16px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 12px !important;
        }
        
        div[style*="margin-bottom: 18px"] {
            margin-bottom: 14px !important;
        }
        
        div[style*="margin-bottom: 20px"] {
            margin-bottom: 16px !important;
        }
        
        div[style*="padding: 14px 16px"] {
            padding: 10px 12px !important;
        }
        
        div[style*="font-size: 15px"] {
            font-size: 13px !important;
        }
        
        div[style*="padding: 14px 20px"] {
            padding: 10px 16px !important;
        }
        
        div[style*="margin-top: 20px"] {
            margin-top: 12px !important;
        }
        
        div[style*="width: 56px"] {
            width: 48px !important;
        }
        
        div[style*="height: 56px"] {
            height: 48px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 20px 16px"] {
            padding: 8px 4px !important;
        }
        
        div[style*="padding: 28px"] {
            padding: 16px 12px !important;
        }
        
        div[style*="margin-bottom: 24px"] {
            margin-bottom: 12px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 18px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 11px !important;
        }
        
        div[style*="margin-bottom: 18px"] {
            margin-bottom: 10px !important;
        }
        
        div[style*="margin-bottom: 20px"] {
            margin-bottom: 12px !important;
        }
        
        div[style*="padding: 14px 16px"] {
            padding: 8px 10px !important;
        }
        
        div[style*="font-size: 15px"] {
            font-size: 12px !important;
        }
        
        div[style*="padding: 14px 20px"] {
            padding: 8px 14px !important;
        }
        
        div[style*="margin-top: 20px"] {
            margin-top: 8px !important;
        }
        
        div[style*="width: 56px"] {
            width: 40px !important;
        }
        
        div[style*="height: 56px"] {
            height: 40px !important;
        }
        
        div[style*="font-size: 24px"] {
            font-size: 18px !important;
        }
        
        div[style*="font-size: 13px"] {
            font-size: 10px !important;
        }
        
        div[style*="font-size: 11px"] {
            font-size: 9px !important;
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
            font-size: 12px !important;
        }
        
        input::placeholder {
            font-size: 13px !important;
        }
    }
</style>
@endsection
