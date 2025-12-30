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
            <div style="display: flex; height: 100%;">
                <div style="width: 100%; display: flex; flex-direction: column; flex: 1;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Account Information</h3>
                    
                    <!-- Staff ID Card Design -->
                    <div style="position: relative; background: white; border-radius: 12px; padding: 0; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); overflow: hidden; width: 100%; flex: 1; display: flex; flex-direction: column; min-height: 0;">
                        <!-- Colored Header Strip -->
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 50px; position: relative;">
                            <!-- Profile Card Text -->
                            <div style="position: absolute; top: 50%; left: 20px; transform: translateY(-50%);">
                                <h4 style="color: white; font-size: 16px; font-weight: 700; margin: 0; letter-spacing: 1px;">PROFILE CARD</h4>
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div style="padding: 24px; display: flex; gap: 24px; flex: 1; flex-direction: column;">
                            <!-- Content Wrapper to match heights -->
                            <div style="display: flex; gap: 24px; width: 100%; align-items: flex-start; flex: 1;">
                                <!-- Left Side: Photo Area -->
                                <div style="flex-shrink: 0;">
                                    <div style="width: 120px; background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%); border-radius: 12px; border: 2px solid #e5e7eb; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);" id="profile-image-container">
                                        <!-- Placeholder for photo - using initials -->
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; min-height: 100%;">
                                            <span style="color: white; font-size: 42px; font-weight: 700; text-transform: uppercase;">
                                                @php
                                                    $initials = '';
                                                    $nameParts = explode(' ', $user->name);
                                                    foreach($nameParts as $part) {
                                                        $initials .= strtoupper(substr($part, 0, 1));
                                                    }
                                                    echo substr($initials, 0, 2);
                                                @endphp
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Side: Information -->
                                <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-start; gap: 20px;" id="info-section">
                                    <!-- Name -->
                                    <div>
                                        <p style="color: #111827; font-size: 20px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.2;">{{ strtoupper($user->name) }}</p>
                                    </div>
                                    
                                    <!-- Account Details -->
                                    <div style="padding-top: 16px; border-top: 1px solid #e5e7eb;">
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px;">
                                            <!-- Role -->
                                            <div>
                                                <p style="color: #6b7280; font-size: 10px; margin: 0 0 6px 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Role</p>
                                                <p style="color: #111827; font-size: 14px; margin: 0; font-weight: 600;">{{ ucfirst($user->role) }}</p>
                                            </div>
                                            
                                            <!-- Organization -->
                                            <div>
                                                <p style="color: #6b7280; font-size: 10px; margin: 0 0 6px 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Organization</p>
                                                <p style="color: #111827; font-size: 14px; margin: 0; font-weight: 600;">{{ $user->organization ? $user->organization : 'N/A' }}</p>
                                            </div>
                                            
                                            <!-- Last Login -->
                                            <div>
                                                <p style="color: #6b7280; font-size: 10px; margin: 0 0 6px 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Last Login</p>
                                                <p style="color: #111827; font-size: 14px; margin: 0; font-weight: 600;">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y') : 'Never' }}</p>
                                            </div>
                                            
                                            <!-- Member Since -->
                                            <div>
                                                <p style="color: #6b7280; font-size: 10px; margin: 0 0 6px 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Member Since</p>
                                                <p style="color: #111827; font-size: 14px; margin: 0; font-weight: 600;">{{ $user->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                            </div>
                        </div>
                            </div>
                        </div>
                        
                        <!-- Barcode Section -->
                        <div style="padding: 0 24px 20px 24px; margin-top: auto;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 2px; height: 70px; background: #f9fafb; border-radius: 4px; padding: 10px;">
                                <!-- Barcode lines -->
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 3px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                                <div style="width: 4px; height: 100%; background: #111827;"></div>
                                <div style="width: 1px; height: 100%; background: #111827;"></div>
                                <div style="width: 2px; height: 100%; background: #111827;"></div>
                            </div>
                        </div>
                        
                        <script>
                            // Match profile image height to info section height
                            document.addEventListener('DOMContentLoaded', function() {
                                const profileImage = document.getElementById('profile-image-container');
                                const infoSection = document.getElementById('info-section');
                                
                                if (profileImage && infoSection) {
                                    function updateHeight() {
                                        profileImage.style.height = infoSection.offsetHeight + 'px';
                                    }
                                    
                                    updateHeight();
                                    window.addEventListener('resize', updateHeight);
                                    
                                    // Use MutationObserver to watch for content changes
                                    const observer = new MutationObserver(updateHeight);
                                    observer.observe(infoSection, { childList: true, subtree: true, attributes: true });
                                }
                            });
                        </script>
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
        
        /* ID Card responsive adjustments */
        div[style*="aspect-ratio: 1.586 / 1"] {
            max-width: 100% !important;
        }
        
        div[style*="height: 50px"] {
            height: 45px !important;
        }
        
        h4[style*="font-size: 16px"] {
            font-size: 14px !important;
        }
        
        div[style*="width: 90px; height: 110px"] {
            width: 70px !important;
            height: 85px !important;
        }
        
        span[style*="font-size: 32px"] {
            font-size: 24px !important;
        }
        
        p[style*="font-size: 18px"][style*="text-transform: uppercase"] {
            font-size: 16px !important;
        }
        
        div[style*="padding: 18px"] {
            padding: 14px !important;
            gap: 14px !important;
        }
        
        div[style*="width: 90px"][style*="flex-shrink: 0"] {
            width: 70px !important;
        }
        
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
