@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 32px 16px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <!-- Header Section -->
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);">
                <span style="font-size: 28px; color: white;">🔮</span>
            </div>
            <h1 style="font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Create New AI Prediction</h1>
            <p style="color: #64748b; font-size: 18px; line-height: 1.6;">Input your data and let AI analyze it for insights and predictions</p>
        </div>

        <!-- Main Form Card -->
        <div style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <form action="{{ route('predictions.store') }}" method="POST">
                @csrf
                
                <!-- Topic Field -->
                <div style="margin-bottom: 32px;">
                    <label for="topic" style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Prediction Topic *
                    </label>
                    <input type="text" 
                           id="topic" 
                           name="topic" 
                           value="{{ old('topic') }}"
                           style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;"
                           placeholder="e.g., Customer Sentiment Analysis, Market Trend Prediction"
                           required>
                    @error('topic')
                        <p style="color: #dc2626; font-size: 14px; margin-top: 8px; margin-bottom: 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Analysis Type (Fixed) -->
                <div style="margin-bottom: 32px;">
                    <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                        AI Analysis Type
                    </label>
                    <div style="padding: 16px 20px; background: #f1f5f9; border: 2px solid #e2e8f0; border-radius: 12px; color: #475569; font-size: 16px; cursor: not-allowed;">
                        Prediction Analysis - AI-powered insights and forecasting for any topic
                    </div>
                    <input type="hidden" name="analysis_type" value="prediction-analysis">
                    <p style="color: #64748b; font-size: 14px; margin-top: 8px; margin-bottom: 0;">This system is designed specifically for comprehensive prediction analysis and forecasting across any domain</p>
                </div>

                <!-- Input Data -->
                <div style="margin-bottom: 32px;">
                    <label for="input_data" style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Input Data *
                    </label>
                    <textarea id="input_data" 
                              name="input_data" 
                              rows="8"
                              style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb; resize: vertical; font-family: inherit;"
                              placeholder="Enter your text data here. The AI will analyze your input and provide comprehensive predictions including executive summary, current situation analysis, key factors, predictions, policy implications, risk assessment, and recommendations."
                              required>{{ old('input_data') }}</textarea>
                    <p style="color: #64748b; font-size: 14px; margin-top: 8px; margin-bottom: 0;">Minimum 10 characters required</p>
                    @error('input_data')
                        <p style="color: #dc2626; font-size: 14px; margin-top: 8px; margin-bottom: 0;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Analysis Type Info -->
                <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 24px; border-radius: 16px; margin-bottom: 32px; border: 1px solid #bfdbfe;">
                    <h3 style="font-weight: 600; color: #1e40af; margin-bottom: 16px; font-size: 18px;">📋 Prediction Analysis Information</h3>
                    <div style="color: #1e40af; font-size: 14px; line-height: 1.6;">
                        <p style="margin-bottom: 12px;">This AI system will analyze your input and generate comprehensive predictions for any topic including:</p>
                        <ul style="margin: 0; padding-left: 20px;">
                            <li style="margin-bottom: 6px;"><strong>Executive Summary:</strong> Key insights and overview</li>
                            <li style="margin-bottom: 6px;"><strong>Current Situation:</strong> Analysis of present circumstances</li>
                            <li style="margin-bottom: 6px;"><strong>Key Factors:</strong> Important elements influencing outcomes</li>
                            <li style="margin-bottom: 6px;"><strong>Predictions:</strong> Future scenarios and forecasts</li>
                            <li style="margin-bottom: 6px;"><strong>Policy Implications:</strong> Impact on decision-making and strategy</li>
                            <li style="margin-bottom: 6px;"><strong>Risk Assessment:</strong> Potential challenges and mitigation</li>
                            <li style="margin-bottom: 0;"><strong>Recommendations:</strong> Strategic guidance and next steps</li>
                        </ul>
                    </div>
                </div>

                <!-- System Status -->
                <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 24px; border-radius: 16px; margin-bottom: 32px; border: 1px solid #bbf7d0;">
                    <h3 style="font-weight: 600; color: #166534; margin-bottom: 16px; font-size: 18px;">✅ System Status</h3>
                    <div style="color: #166534; font-size: 14px; line-height: 1.6;">
                        <p style="margin-bottom: 8px;"><strong>AI System:</strong> Google Gemini Pro Prediction Engine</p>
                        <p style="margin: 0;">Advanced AI-powered prediction analysis using Google Gemini Pro for comprehensive future forecasting and strategic insights.</p>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 16px; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
                    <a href="{{ route('predictions.index') }}" style="display: inline-block; padding: 16px 32px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease;">
                        Cancel
                    </a>
                    <button type="submit" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); cursor: pointer;">
                        🚀 Create Prediction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Responsive design improvements */
    @media (max-width: 768px) {
        div[style*="padding: 32px 16px"] {
            padding: 24px 12px !important;
        }
        
        div[style*="max-width: 800px"] {
            max-width: 100% !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 32px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 32px 24px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 28px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
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
        
        div[style*="padding: 24px"] {
            padding: 20px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 13px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 12px 24px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 12px !important;
        }
        
        /* Stack buttons vertically on mobile */
        div[style*="justify-content: flex-end"] {
            justify-content: center !important;
            flex-direction: column !important;
        }
        
        div[style*="justify-content: flex-end"] > * {
            width: 100% !important;
            text-align: center !important;
        }
    }
    
    @media (max-width: 640px) {
        div[style*="padding: 32px 16px"] {
            padding: 20px 8px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 24px 20px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 24px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 24px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
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
        
        div[style*="padding: 24px"] {
            padding: 16px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 15px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 12px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 10px 20px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 8px !important;
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
        
        /* Reduce textarea rows on mobile */
        textarea[rows="8"] {
            rows: 6 !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 32px 16px"] {
            padding: 16px 4px !important;
        }
        
        div[style*="padding: 40px"] {
            padding: 20px 16px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 20px !important;
        }
        
        div[style*="font-size: 36px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 12px !important;
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
        
        div[style*="padding: 24px"] {
            padding: 12px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        
        div[style*="font-size: 14px"] {
            font-size: 11px !important;
        }
        
        div[style*="padding: 16px 32px"] {
            padding: 8px 16px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 6px !important;
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
        
        /* Further reduce textarea rows on very small screens */
        textarea[rows="8"] {
            rows: 4 !important;
        }
        
        /* Adjust list padding on very small screens */
        ul[style*="padding-left: 20px"] {
            padding-left: 16px !important;
        }
    }
    
    /* Touch-friendly improvements for mobile */
    @media (max-width: 768px) {
        input, textarea, button, a {
            min-height: 44px;
        }
        
        input, textarea {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
        
        /* Improve form spacing on mobile */
        .form-group {
            margin-bottom: 20px !important;
        }
    }
    
    /* Input focus effects */
    input:focus, textarea:focus {
        outline: none;
        border-color: #667eea !important;
        background: white !important;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
        transform: translateY(-2px);
    }
    
    /* Button hover effects */
    button:hover, a:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
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
        
        input::placeholder, textarea::placeholder {
            font-size: 14px !important;
        }
        
        /* Better touch targets for mobile */
        button, a {
            min-width: 44px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiStatus = document.getElementById('api-status');
    
    // Check if user is authenticated
    console.log('User authenticated:', {{ auth()->check() ? 'true' : 'false' }});
    
    // Check API status
    checkApiStatus();
    
    function checkApiStatus() {
        apiStatus.textContent = 'Checking...';
        apiStatus.style.color = '#92400e';
        
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Make actual API test call with proper headers
        fetch('/predictions/api/test', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token || ''
            },
            credentials: 'same-origin' // Include cookies for authentication
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API test response:', data);
            if (data.success) {
                if (data.api_status === 'connected') {
                    apiStatus.textContent = 'Available';
                    apiStatus.style.color = '#059669';
                } else if (data.api_status === 'failed') {
                    apiStatus.textContent = 'API Connection Failed';
                    apiStatus.style.color = '#dc2626';
                } else {
                    apiStatus.textContent = 'Unknown Status: ' + data.api_status;
                    apiStatus.style.color = '#dc2626';
                }
            } else {
                apiStatus.textContent = 'Error: ' + (data.error || data.message || 'Unknown error');
                apiStatus.style.color = '#dc2626';
            }
        })
        .catch(error => {
            console.error('API test failed:', error);
            apiStatus.textContent = 'Connection Error: ' + error.message;
            apiStatus.style.color = '#dc2626';
        });
    }
    
    // Manual test function
    function testApiManually() {
        console.log('Manual API test started');
        
        // Test simple endpoint first
        fetch('/predictions/simple-test')
            .then(response => {
                console.log('Simple test response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Simple test response:', data);
                alert('Simple test: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                console.error('Simple test failed:', error);
                alert('Simple test failed: ' + error.message);
            });
        
        // Test main API endpoint
        fetch('/predictions/api/test')
            .then(response => {
                console.log('Main API test response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Main API test response:', data);
                alert('Main API test: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                console.error('Main API test failed:', error);
                alert('Main API test failed: ' + error.message);
            });
    }
});
</script>
@endsection
