@extends('layouts.app')

@section('content')
<div class="container-responsive" style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 32px 16px;">
    <!-- Header Section -->
    <div style="text-align: center; margin-bottom: 48px;">
        <h1 class="text-responsive-4xl" style="font-weight: 800; color: #1e293b; margin-bottom: 16px;">Responsive Design Test</h1>
        <p class="text-responsive-lg" style="color: #64748b; max-width: 800px; margin: 0 auto; line-height: 1.6;">
            This page demonstrates all the responsive design features and utilities available in the AI Predictions app.
        </p>
    </div>

    <!-- Responsive Grid Demo -->
    <div class="card-responsive" style="margin-bottom: 32px;">
        <div class="card-responsive-header">
            <h2 class="text-responsive-2xl" style="font-weight: 700; color: #1e293b; margin: 0;">Responsive Grid System</h2>
        </div>
        <div class="card-responsive-body">
            <div class="grid-responsive grid-responsive-auto">
                <div class="card-responsive" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                    <div class="card-responsive-body">
                        <h3 class="text-responsive-lg" style="font-weight: 600; color: #1e40af; margin-bottom: 8px;">Grid Item 1</h3>
                        <p class="text-responsive-base" style="color: #1e40af;">This grid automatically adjusts based on screen size.</p>
                    </div>
                </div>
                <div class="card-responsive" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="card-responsive-body">
                        <h3 class="text-responsive-lg" style="font-weight: 600; color: #166534; margin-bottom: 8px;">Grid Item 2</h3>
                        <p class="text-responsive-base" style="color: #166534;">On mobile, these stack vertically for better usability.</p>
                    </div>
                </div>
                <div class="card-responsive" style="background: #fef3c7; border: 1px solid #fcd34d;">
                    <div class="card-responsive-body">
                        <h3 class="text-responsive-lg" style="font-weight: 600; color: #92400e; margin-bottom: 8px;">Grid Item 3</h3>
                        <p class="text-responsive-base" style="color: #92400e;">Responsive breakpoints ensure optimal layout on all devices.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Typography Demo -->
    <div class="card-responsive" style="margin-bottom: 32px;">
        <div class="card-responsive-header">
            <h2 class="text-responsive-2xl" style="font-weight: 700; color: #1e293b; margin: 0;">Responsive Typography</h2>
        </div>
        <div class="card-responsive-body">
            <div class="grid-responsive grid-responsive-2">
                <div>
                    <h3 class="text-responsive-xl" style="font-weight: 600; color: #374151; margin-bottom: 16px;">Desktop View</h3>
                    <p class="text-responsive-4xl" style="color: #667eea; margin-bottom: 8px;">Large Heading</p>
                    <p class="text-responsive-3xl" style="color: #667eea; margin-bottom: 8px;">Medium Heading</p>
                    <p class="text-responsive-2xl" style="color: #667eea; margin-bottom: 8px;">Small Heading</p>
                    <p class="text-responsive-lg" style="color: #667eea;">Body Text</p>
                </div>
                <div>
                    <h3 class="text-responsive-xl" style="font-weight: 600; color: #374151; margin-bottom: 16px;">Mobile View</h3>
                    <p class="text-responsive-base" style="color: #64748b;">Text automatically scales down on smaller screens for optimal readability.</p>
                    <p class="text-responsive-base" style="color: #64748b;">This ensures content remains accessible and visually appealing across all devices.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Form Demo -->
    <div class="card-responsive" style="margin-bottom: 32px;">
        <div class="card-responsive-header">
            <h2 class="text-responsive-2xl" style="font-weight: 700; color: #1e293b; margin: 0;">Responsive Forms</h2>
        </div>
        <div class="card-responsive-body">
            <form class="form-responsive">
                <div style="margin-bottom: 24px;">
                    <label for="demo-name" class="text-responsive-base" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">
                        Full Name
                    </label>
                    <input type="text" id="demo-name" class="form-responsive" placeholder="Enter your full name">
                </div>
                <div style="margin-bottom: 24px;">
                    <label for="demo-email" class="text-responsive-base" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">
                        Email Address
                    </label>
                    <input type="email" id="demo-email" class="form-responsive" placeholder="Enter your email">
                </div>
                <div style="margin-bottom: 24px;">
                    <label for="demo-message" class="text-responsive-base" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">
                        Message
                    </label>
                    <textarea id="demo-message" class="form-responsive" rows="4" placeholder="Enter your message"></textarea>
                </div>
                <div class="flex-responsive flex-responsive-wrap gap-responsive-md">
                    <button type="button" class="btn-responsive btn-responsive-lg" style="background: #667eea; color: white;">
                        Submit Form
                    </button>
                    <button type="button" class="btn-responsive btn-responsive-lg" style="background: transparent; color: #64748b; border: 2px solid #e2e8f0;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Responsive Table Demo -->
    <div class="card-responsive" style="margin-bottom: 32px;">
        <div class="card-responsive-header">
            <h2 class="text-responsive-2xl" style="font-weight: 700; color: #1e293b; margin: 0;">Responsive Tables</h2>
        </div>
        <div class="card-responsive-body">
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #374151; border-bottom: 1px solid #e2e8f0;">
                                Feature
                            </th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #374151; border-bottom: 1px solid #e2e8f0;">
                                Description
                            </th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #374151; border-bottom: 1px solid #e2e8f0;">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 16px; color: #374151;">Mobile Navigation</td>
                            <td style="padding: 16px; color: #64748b;">Hamburger menu for small screens</td>
                            <td style="padding: 16px;"><span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Active</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 16px; color: #374151;">Responsive Grid</td>
                            <td style="padding: 16px; color: #64748b;">Auto-stacking columns on mobile</td>
                            <td style="padding: 16px;"><span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Active</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 16px; color: #374151;">Touch-Friendly</td>
                            <td style="padding: 16px; color: #64748b;">44px minimum touch targets</td>
                            <td style="padding: 16px;"><span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Active</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Responsive Utilities Demo -->
    <div class="card-responsive" style="margin-bottom: 32px;">
        <div class="card-responsive-header">
            <h2 class="text-responsive-2xl" style="font-weight: 700; color: #1e293b; margin: 0;">Responsive Utilities</h2>
        </div>
        <div class="card-responsive-body">
            <div class="grid-responsive grid-responsive-3">
                <div class="text-center">
                    <h4 class="text-responsive-lg" style="font-weight: 600; color: #374151; margin-bottom: 8px;">Hidden on Mobile</h4>
                    <p class="text-responsive-sm text-gray-500">This content is hidden on screens smaller than 768px</p>
                    <div class="hidden-md" style="background: #fef3c7; color: #92400e; padding: 8px; border-radius: 8px; margin-top: 8px;">
                        Desktop Only Content
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="text-responsive-lg" style="font-weight: 600; color: #374151; margin-bottom: 8px;">Mobile Only</h4>
                    <p class="text-responsive-sm text-gray-500">This content only shows on mobile devices</p>
                    <div class="visible-md" style="background: #dbeafe; color: #1e40af; padding: 8px; border-radius: 8px; margin-top: 8px;">
                        Mobile Only Content
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="text-responsive-lg" style="font-weight: 600; color: #374151; margin-bottom: 8px;">Responsive Spacing</h4>
                    <p class="text-responsive-sm text-gray-500">Spacing automatically adjusts for different screen sizes</p>
                    <div class="p-responsive-md" style="background: #dcfce7; color: #166534; border-radius: 8px; margin-top: 8px;">
                        Adaptive Padding
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breakpoint Information -->
    <div class="card-responsive">
        <div class="card-responsive-header">
            <h2 class="text-responsive-2xl" style="font-weight: 700; color: #1e293b; margin: 0;">Breakpoint Information</h2>
        </div>
        <div class="card-responsive-body">
            <div class="grid-responsive grid-responsive-2">
                <div>
                    <h3 class="text-responsive-lg" style="font-weight: 600; color: #374151; margin-bottom: 16px;">Mobile First Approach</h3>
                    <ul class="text-responsive-base" style="color: #64748b; padding-left: 20px;">
                        <li style="margin-bottom: 8px;"><strong>480px and below:</strong> Extra small devices</li>
                        <li style="margin-bottom: 8px;"><strong>576px and below:</strong> Small devices</li>
                        <li style="margin-bottom: 8px;"><strong>768px and below:</strong> Medium devices</li>
                        <li style="margin-bottom: 8px;"><strong>992px and below:</strong> Large devices</li>
                        <li style="margin-bottom: 0;"><strong>1200px and below:</strong> Extra large devices</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-responsive-lg" style="font-weight: 600; color: #374151; margin-bottom: 16px;">Key Features</h3>
                    <ul class="text-responsive-base" style="color: #64748b; padding-left: 20px;">
                        <li style="margin-bottom: 8px;">Mobile navigation with hamburger menu</li>
                        <li style="margin-bottom: 8px;">Responsive grid system</li>
                        <li style="margin-bottom: 8px;">Touch-friendly interface</li>
                        <li style="margin-bottom: 8px;">Adaptive typography</li>
                        <li style="margin-bottom: 0;">Optimized spacing and layout</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Additional responsive styles for this demo page */
    @media (max-width: 768px) {
        .grid-responsive-3 {
            grid-template-columns: 1fr !important;
        }
        
        .grid-responsive-2 {
            grid-template-columns: 1fr !important;
        }
    }
    
    /* Demo-specific responsive adjustments */
    @media (max-width: 640px) {
        div[style*="padding: 32px 16px"] {
            padding: 20px 8px !important;
        }
        
        div[style*="margin-bottom: 48px"] {
            margin-bottom: 24px !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 32px 16px"] {
            padding: 16px 4px !important;
        }
        
        div[style*="margin-bottom: 48px"] {
            margin-bottom: 20px !important;
        }
    }
</style>
@endsection
