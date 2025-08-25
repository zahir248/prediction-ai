@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 32px 16px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <!-- Header Section -->
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Create New AI Prediction</h1>
            <p style="color: #64748b; font-size: 18px; line-height: 1.6;">Input your data and let AI analyze it for insights and predictions</p>
        </div>

        <!-- Main Form Card -->
        <div style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <form action="{{ route('predictions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Topic Field -->
                        <div style="margin-bottom: 32px;">
            <label for="topic" style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                Prediction Topic <span style="color: #dc2626;">*</span>
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

                <!-- Prediction Horizon Field -->
                        <div style="margin-bottom: 32px;">
            <label for="prediction_horizon" style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                Prediction Horizon <span style="color: #dc2626;">*</span>
            </label>
                    <select id="prediction_horizon" 
                            name="prediction_horizon" 
                            style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb; cursor: pointer;"
                            required>
                        <option value="">Select Prediction Horizon</option>
                        <option value="next_two_weeks" {{ old('prediction_horizon') == 'next_two_weeks' ? 'selected' : '' }}>Next Two Weeks</option>
                        <option value="next_month" {{ old('prediction_horizon') == 'next_month' ? 'selected' : '' }}>Next Month</option>
                        <option value="three_months" {{ old('prediction_horizon') == 'three_months' ? 'selected' : '' }}>3 Months</option>
                        <option value="six_months" {{ old('prediction_horizon') == 'six_months' ? 'selected' : '' }}>6 Months</option>
                        <option value="twelve_months" {{ old('prediction_horizon') == 'twelve_months' ? 'selected' : '' }}>12 Months</option>
                        <option value="two_years" {{ old('prediction_horizon') == 'two_years' ? 'selected' : '' }}>2 Years</option>
                    </select>
                    @error('prediction_horizon')
                        <p style="color: #dc2626; font-size: 14px; margin-top: 8px; margin-bottom: 0;">{{ $message }}</p>
                    @enderror
                    <p style="color: #64748b; font-size: 14px; margin-top: 8px; margin-bottom: 0;">Select the time period for your prediction analysis</p>
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
                Input Data <span style="color: #dc2626;">*</span>
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

                <!-- File Upload Section -->
                <div style="margin-bottom: 32px;">
                    <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Upload Files (Optional)
                    </label>
                    <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 12px; border: 2px solid #bfdbfe;">
                        <div style="margin-bottom: 16px;">
                            <input type="file" 
                                   id="uploaded_files" 
                                   name="uploaded_files[]" 
                                   multiple
                                   accept=".pdf,.xlsx,.xls,.csv,.txt"
                                   style="width: 100%; padding: 16px 20px; border: 2px solid #bfdbfe; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: white; cursor: pointer;"
                                   onchange="updateFileList()">
                        </div>
                        
                        <div id="file-list" style="display: none;">
                            <h4 style="font-weight: 600; color: #1e40af; margin-bottom: 12px; font-size: 16px;">Selected Files:</h4>
                            <div id="file-items" style="margin-bottom: 16px;"></div>
                        </div>
                        
                        <div style="color: #1e40af; font-size: 14px; line-height: 1.6;">
                            <p style="margin-bottom: 8px;"><strong>Supported Formats:</strong></p>
                            <ul style="margin: 0; padding-left: 20px; margin-bottom: 12px;">
                                <li style="margin-bottom: 4px;">📄 PDF Documents (.pdf)</li>
                                <li style="margin-bottom: 4px;">📊 Excel Spreadsheets (.xlsx, .xls)</li>
                                <li style="margin-bottom: 4px;">📋 CSV Files (.csv)</li>
                                <li style="margin-bottom: 0px;">📝 Text Files (.txt)</li>
                            </ul>
                            <p style="margin: 0; font-size: 13px;"><strong>Note:</strong> Maximum file size: 10MB per file. The AI will extract and analyze text content from these files along with your input data.</p>
                        </div>
                    </div>
                    @error('uploaded_files.*')
                        <p style="color: #dc2626; font-size: 14px; margin-top: 8px; margin-bottom: 0;">{{ $message }}</p>
                    @enderror
                    @if($errors->has('uploaded_files.*'))
                        <div style="margin-top: 8px; padding: 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;">
                            <p style="color: #dc2626; font-size: 14px; margin: 0; font-weight: 600;">File Upload Errors:</p>
                            <ul style="color: #dc2626; font-size: 13px; margin: 8px 0 0 0; padding-left: 20px;">
                                @foreach($errors->get('uploaded_files.*') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Source URLs Field -->
                <div style="margin-bottom: 32px;">
                    <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Source URLs (Optional)
                    </label>
                    <div id="source-urls-container">
                        <div class="source-url-row" style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                            <input type="url" 
                                   name="source_urls[]" 
                                   style="flex: 1; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;"
                                   placeholder="https://example.com/article or https://news.example.com/report"
                                   pattern="https?://.+">
                            <button type="button" 
                                    class="remove-source-url" 
                                    style="padding: 16px 20px; background: #ef4444; color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; min-width: 60px; display: none;"
                                    onclick="removeSourceUrl(this)">
                                🗑️
                            </button>
                        </div>
                    </div>
                    <button type="button" 
                            id="add-source-url" 
                            style="margin-top: 12px; padding: 12px 20px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;"
                            onclick="addSourceUrlField()">
                        ➕ Add Another Source
                    </button>
                    <p style="color: #64748b; font-size: 14px; margin-top: 8px; margin-bottom: 0;">Add links to articles, reports, or sources that provide additional context for your analysis. The AI will automatically fetch and read the actual content from these URLs, then explicitly cite specific facts, numbers, and insights from the source material in the analysis.</p>
                    @error('source_urls')
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
                <div style="display: flex; justify-content: center; gap: 16px; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
                    <a href="{{ route('predictions.index') }}" style="display: inline-block; padding: 16px 32px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease;">
                        Cancel
                    </a>
                                         <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 16px 32px; border-radius: 12px; font-size: 18px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                         🚀 Create Prediction
                     </button>
                </div>
                
                <!-- Loading Indicator -->
                <div id="loadingIndicator" style="display: none; margin-top: 20px; padding: 20px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; border: 1px solid #0ea5e9; text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                        <div style="width: 24px; height: 24px; border: 3px solid #0ea5e9; border-top: 3px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <span style="color: #0ea5e9; font-weight: 600; font-size: 16px;">AI Analysis in Progress...</span>
                    </div>
                    <p style="color: #0369a1; font-size: 14px; margin: 12px 0 0 0; opacity: 0.8;">
                        This may take 2-5 minutes. The AI is reading your source URLs and generating a comprehensive analysis.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .source-url-field {
        transition: all 0.3s ease;
    }
    
    .source-url-field.new-field {
        border: 2px solid #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
    }
    
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
// Global functions for source URLs
function addSourceUrlField() {
    try {
        const container = document.getElementById('source-urls-container');
        
        if (!container) {
            console.error('Container not found!');
            return;
        }
        
        const newRow = document.createElement('div');
        newRow.className = 'source-url-row';
        newRow.style.cssText = 'display: flex; gap: 12px; margin-bottom: 12px; align-items: center; border: 2px solid #10b981; background-color: #f0fdf4; padding: 8px; border-radius: 8px;';
        
        newRow.innerHTML = `
            <input type="url" 
                   name="source_urls[]" 
                   style="flex: 1; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;"
                   placeholder="https://example.com/article or https://news.example.com/report"
                   pattern="https?://.+">
            <button type="button" 
                    class="remove-source-url" 
                    style="padding: 16px 20px; background: #ef4444; color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; min-width: 60px;"
                    onclick="removeSourceUrl(this)">
                🗑️
            </button>
        `;
        
        // Add a temporary highlight to make the new field visible
        newRow.style.border = '2px solid #10b981';
        newRow.style.backgroundColor = '#f0fdf4';
        newRow.style.padding = '8px';
        newRow.style.borderRadius = '8px';
        
        // Remove highlight after 2 seconds
        setTimeout(() => {
            newRow.style.border = '';
            newRow.style.backgroundColor = '';
            newRow.style.padding = '';
            newRow.style.borderRadius = '';
        }, 2000);
        
        container.appendChild(newRow);
        
        // Always ensure first field has no remove button
        updateRemoveButtonVisibility();
        
    } catch (error) {
        console.error('Error adding source URL field:', error);
    }
}

function removeSourceUrl(button) {
    const row = button.closest('.source-url-row');
    const container = document.getElementById('source-urls-container');
    
    row.remove();
    
    // Update remove button visibility after removal
    updateRemoveButtonVisibility();
}

function updateRemoveButtonVisibility() {
    const container = document.getElementById('source-urls-container');
    const rows = container.querySelectorAll('.source-url-row');
    
    rows.forEach((row, index) => {
        const removeBtn = row.querySelector('.remove-source-url');
        if (removeBtn) {
            // First field (index 0) should never show remove button
            if (index === 0) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        }
    });
    
}

// File upload functions
function updateFileList() {
    const fileInput = document.getElementById('uploaded_files');
    const fileList = document.getElementById('file-list');
    const fileItems = document.getElementById('file-items');
    
    if (fileInput.files.length > 0) {
        fileList.style.display = 'block';
        fileItems.innerHTML = '';
        
        Array.from(fileInput.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 12px; background: white; border: 1px solid #bfdbfe; border-radius: 8px; margin-bottom: 8px;';
            
            const fileInfo = document.createElement('div');
            fileInfo.innerHTML = `
                <div style="font-weight: 600; color: #1e40af;">${file.name}</div>
                <div style="font-size: 12px; color: #64748b;">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
            `;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '🗑️';
            removeBtn.style.cssText = 'padding: 8px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; transition: all 0.3s ease;';
            removeBtn.onclick = () => removeFile(index);
            
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            fileItems.appendChild(fileItem);
        });
    } else {
        fileList.style.display = 'none';
    }
}

function removeFile(index) {
    const fileInput = document.getElementById('uploaded_files');
    const dt = new DataTransfer();
    
    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    updateFileList();
}

document.addEventListener('DOMContentLoaded', function() {
    const apiStatus = document.getElementById('api-status');
    
    // Check if user is authenticated
    console.log('User authenticated:', {{ auth()->check() ? 'true' : 'false' }});
    
    // Check API status
    checkApiStatus();
    
    // Source URLs functionality
    setupSourceUrls();
    
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
    
    function setupSourceUrls() {
        const addButton = document.getElementById('add-source-url');
        const container = document.getElementById('source-urls-container');
        
        // Set up initial remove button visibility
        updateRemoveButtonVisibility();
    }

    // Show loading indicator when form is submitted
    const form = document.querySelector('form');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    form.addEventListener('submit', function() {
        loadingIndicator.style.display = 'block';
        
        // Disable the submit button
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = '🔄 Processing...';
        submitButton.style.opacity = '0.7';
        submitButton.style.cursor = 'not-allowed';
    });
});
</script>
@endsection
