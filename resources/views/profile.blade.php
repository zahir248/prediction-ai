@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 700px; margin: 0 auto;">
        <!-- Main Card -->
        <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">My Profile</h1>
                <p style="color: #64748b; font-size: 14px; margin: 0;">
                    Manage your account information and preferences
                </p>
            </div>

            <!-- Profile Form -->
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div style="margin-bottom: 24px;">
                    <label for="name" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                        Full Name
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        required
                        style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white;"
                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                    >
                    @error('name')
                        <p style="color: #ef4444; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div style="margin-bottom: 24px;">
                    <label for="email" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        required
                        style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white;"
                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                    >
                    @error('email')
                        <p style="color: #ef4444; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Section -->
                <div style="margin-bottom: 32px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">Change Password</h3>
                    <p style="color: #64748b; font-size: 13px; margin: 0 0 20px 0;">
                        Leave blank to keep your current password
                    </p>

                    <!-- New Password Field -->
                    <div style="margin-bottom: 20px;">
                        <label for="password" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                            New Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            minlength="8"
                            style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white;"
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                        >
                        @error('password')
                            <p style="color: #ef4444; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div style="margin-bottom: 0;">
                        <label for="password_confirmation" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                            Confirm New Password
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            minlength="8"
                            style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s ease; background: white;"
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                        >
                    </div>
                </div>

                <!-- Account Information Section -->
                <div style="margin-bottom: 32px; padding: 20px; background: #f0f9ff; border-radius: 12px; border: 1px solid #bae6fd;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 16px 0;">Account Information</h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <div>
                            <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 500;">Role</p>
                            <p style="color: #1e293b; font-size: 15px; margin: 0; font-weight: 600;">{{ ucfirst($user->role) }}</p>
                        </div>
                        @if($user->organization)
                        <div>
                            <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 500;">Organization</p>
                            <p style="color: #1e293b; font-size: 15px; margin: 0; font-weight: 600;">{{ $user->organization }}</p>
                        </div>
                        @endif
                        @if($user->last_login_at)
                        <div>
                            <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 500;">Last Login</p>
                            <p style="color: #1e293b; font-size: 15px; margin: 0; font-weight: 600;">{{ $user->last_login_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                        <div>
                            <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 500;">Member Since</p>
                            <p style="color: #1e293b; font-size: 15px; margin: 0; font-weight: 600;">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('dashboard') }}" style="display: inline-block; padding: 12px 24px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.2s ease;" onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">
                        Cancel
                    </a>
                    <button type="submit" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="max-width: 700px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 20px !important;
        }
        
        div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="justify-content: flex-end"] {
            flex-direction: column-reverse;
        }
        
        div[style*="justify-content: flex-end"] a,
        div[style*="justify-content: flex-end"] button {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endsection
