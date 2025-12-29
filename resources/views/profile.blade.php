@extends('layouts.app')

@section('content')
<!-- Profile Page -->
<div style="min-height: calc(100vh - 72px); padding: 30px 24px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header Section -->
        <div style="margin-bottom: 40px; text-align: center;">
            <h1 style="font-size: 38px; font-weight: 700; color: #111827; margin: 0 0 10px 0; letter-spacing: -0.02em;">My Profile</h1>
            <p style="color: #6b7280; font-size: 16px; margin: 0; line-height: 1.5;">
                Manage your account information and preferences
            </p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 8px; padding: 16px; margin-bottom: 32px; display: flex; align-items: center; gap: 12px;">
            <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #10b981;">
                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
            </svg>
            <span style="color: #065f46; font-size: 14px; font-weight: 500;">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Two Column Layout -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px; align-items: stretch;">
            <!-- Left Section: Account Information -->
            <div style="display: flex;">
                <div style="background: #f0f9ff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #bae6fd; width: 100%; display: flex; flex-direction: column;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Account Information</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; flex: 1;">
                        <div style="background: white; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #667eea;">
                                    <path d="M12,1L3,5V11C3,16.55 6.16,21.74 12,23C17.84,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C15.4,11.5 16,12.4 16,13V16C16,17.1 15.1,18 14,18H10C8.9,18 8,17.1 8,16V13C8,12.4 8.6,11.5 9.2,11.5V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.5,8.7 10.5,9.5V11.5H13.5V9.5C13.5,8.7 12.8,8.2 12,8.2Z"/>
                                </svg>
                                <p style="color: #6b7280; font-size: 12px; margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Role</p>
                            </div>
                            <p style="color: #111827; font-size: 15px; margin: 0; font-weight: 600;">{{ ucfirst($user->role) }}</p>
                        </div>
                        @if($user->organization)
                        <div style="background: white; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #667eea;">
                                    <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                                </svg>
                                <p style="color: #6b7280; font-size: 12px; margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Organization</p>
                            </div>
                            <p style="color: #111827; font-size: 15px; margin: 0; font-weight: 600;">{{ $user->organization }}</p>
                        </div>
                        @endif
                        @if($user->last_login_at)
                        <div style="background: white; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #667eea;">
                                    <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                                </svg>
                                <p style="color: #6b7280; font-size: 12px; margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Last Login</p>
                            </div>
                            <p style="color: #111827; font-size: 15px; margin: 0; font-weight: 600;">{{ $user->last_login_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                        <div style="background: white; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #667eea;">
                                    <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,5V19H5V5H19M17,12H15V17H17M13,12H11V17H13M9,12H7V17H9M17,7H15V10H17M13,7H11V10H13M9,7H7V10H9Z"/>
                                </svg>
                                <p style="color: #6b7280; font-size: 12px; margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Member Since</p>
                            </div>
                            <p style="color: #111827; font-size: 15px; margin: 0; font-weight: 600;">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section: Update Fields -->
            <div style="display: flex;">
                <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; width: 100%; display: flex; flex-direction: column;">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name and Email Fields - Side by Side -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                            <!-- Name Field -->
                            <div>
                                <label for="name" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                                    Full Name
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', $user->name) }}" 
                                    required
                                    style="width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white; color: #111827;"
                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                >
                                @error('name')
                                    <p style="color: #ef4444; font-size: 12px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div>
                                <label for="email" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                                    Email Address
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', $user->email) }}" 
                                    required
                                    style="width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white; color: #111827;"
                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                >
                                @error('email')
                                    <p style="color: #ef4444; font-size: 12px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div style="margin-bottom: 24px; padding: 20px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 12px; border: 1px solid #e2e8f0;">
                            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 6px 0;">Change Password</h3>
                            <p style="color: #6b7280; font-size: 13px; margin: 0 0 16px 0; line-height: 1.4;">
                                Leave blank to keep your current password
                            </p>

                            <!-- Password Fields - Side by Side -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <!-- New Password Field -->
                                <div>
                                    <label for="password" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                                        New Password
                                    </label>
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        minlength="8"
                                        style="width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white; color: #111827;"
                                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                    >
                                    @error('password')
                                        <p style="color: #ef4444; font-size: 12px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password Field -->
                                <div>
                                    <label for="password_confirmation" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                                        Confirm Password
                                    </label>
                                    <input 
                                        type="password" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        minlength="8"
                                        style="width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white; color: #111827;"
                                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div style="display: flex; gap: 12px; justify-content: center; margin-top: auto;">
                            <a href="{{ route('dashboard') }}" style="display: inline-block; padding: 12px 24px; background: white; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; transition: all 0.2s ease; border: 2px solid #d1d5db; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#9ca3af'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.15)';" onmouseout="this.style.background='white'; this.style.borderColor='#d1d5db'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)';">
                                Cancel
                            </a>
                            <button type="submit" style="display: inline-block; padding: 12px 24px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)';">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Responsive Design */
    @media (max-width: 768px) {
        div[style*="min-height: calc(100vh - 72px)"] {
            padding: 20px 16px !important;
        }
        
        div[style*="max-width: 1200px"] {
            padding: 0 !important;
        }
        
        h1[style*="font-size: 38px"] {
            font-size: 28px !important;
        }
        
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
            gap: 24px !important;
        }
        
        div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        
        /* Make all grid items stack on mobile */
        form div[style*="grid-template-columns: 1fr 1fr"],
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        
        div[style*="justify-content: center"] {
            flex-direction: column-reverse;
        }
        
        div[style*="justify-content: center"] a,
        div[style*="justify-content: center"] button {
            width: 100%;
            text-align: center;
        }
    }
    
    @media (max-width: 480px) {
        h1[style*="font-size: 38px"] {
            font-size: 24px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 16px !important;
        }
    }
</style>
@endsection
