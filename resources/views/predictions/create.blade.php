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
                        <option value="next_two_days" {{ old('prediction_horizon') == 'next_two_days' ? 'selected' : '' }}>Next Two Days</option>
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

                <!-- Source URLs Field -->
                <div style="margin-bottom: 32px;">
                    <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Source URLs (Optional)
                    </label>
                    <div id="source-urls-container">
                        <div class="source-url-row" style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                            <input type="url" 
                                   name="source_urls[]" 
                                    style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;"
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
                    <div style="display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap;">
                    <button type="button" 
                            id="add-source-url" 
                                style="padding: 12px 20px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; min-width: 140px; justify-content: center;"
                            onclick="addSourceUrlField()">
                        ➕ Add Another Source
                    </button>
                        <button type="button" 
                                id="validate-urls-btn" 
                                style="padding: 12px 20px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; min-width: 140px; justify-content: center;"
                                onclick="validateUrls()">
                            🔍 Validate URLs
                        </button>
                        
                    </div>
                    
                    <!-- URL Validation Results -->
                    <div id="url-validation-results" style="display: none; margin-top: 16px; padding: 16px; border-radius: 12px; border: 1px solid #e5e7eb;">
                        <div id="validation-loading" style="display: none; text-align: center; padding: 20px;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                                <div style="width: 20px; height: 20px; border: 2px solid #3b82f6; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <span style="color: #3b82f6; font-weight: 600;">Validating URLs...</span>
                            </div>
                        </div>
                        <div id="validation-content" style="display: none;"></div>
                    </div>
                    
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
                 <div class="submit-buttons-container" style="display: flex; justify-content: center; gap: 16px; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
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
    
    @media (max-width: 768px) {
         .source-url-row {
             flex-direction: column;
             gap: 8px;
         }
         
         .remove-source-url {
             width: 100%;
         }
         
         /* Better touch targets for mobile */
         button, a {
             min-width: 44px;
         }
         
                   /* Source URLs responsive improvements */
          #add-source-url, #validate-urls-btn {
              width: 100%;
              justify-content: center;
              padding: 16px 20px;
              font-size: 16px;
              min-height: 48px;
          }
          
          /* Submit buttons responsive improvements */
          .submit-buttons-container a, .submit-buttons-container button[type="submit"] {
              width: 100%;
              justify-content: center;
              padding: 16px 20px;
              font-size: 16px;
              min-height: 48px;
              text-align: center;
          }
         
         /* URL input field mobile optimization */
         input[name="source_urls[]"] {
            font-size: 16px !important;
            padding: 14px 16px !important;
             min-height: 48px;
         }
         
                   /* Button container mobile layout */
          div[style*="display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap;"] {
              flex-direction: column;
              gap: 8px;
          }
          
          /* Submit buttons container mobile layout */
          .submit-buttons-container {
              flex-direction: column;
              gap: 12px;
        }
    }
    
    @media (max-width: 480px) {
         /* Extra small devices */
         #add-source-url, #validate-urls-btn {
             padding: 14px 16px;
             font-size: 15px;
             min-height: 44px;
         }
         
         input[name="source_urls[]"] {
             padding: 12px 14px !important;
             font-size: 15px !important;
             min-height: 44px;
         }
         
         /* Reduce margins for very small screens */
         .source-url-row {
             margin-bottom: 8px;
         }
         
                   div[style*="display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap;"] {
              margin-top: 8px;
              gap: 6px;
          }
          
          /* Submit buttons extra small devices */
          .submit-buttons-container a, .submit-buttons-container button[type="submit"] {
              padding: 14px 16px;
              font-size: 15px;
            min-height: 44px;
        }
        
          .submit-buttons-container {
              gap: 8px;
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
                    style="width: 100%; padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #f9fafb;"
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



// URL validation function
function validateUrls() {
    console.log('validateUrls function called');
    
    const urlInputs = document.querySelectorAll('input[name="source_urls[]"]');
    console.log('Found URL inputs:', urlInputs.length);
    
    const urls = [];
    
    // Collect all non-empty URLs
    urlInputs.forEach((input, index) => {
        const url = input.value.trim();
        console.log(`URL ${index + 1}: "${url}"`);
        if (url) {
            urls.push(url);
        }
    });
    
    console.log('Collected URLs:', urls);
    
    if (urls.length === 0) {
        alert('Please add at least one URL to validate.');
        return;
    }
    
    // Show validation area and loading
    const validationResults = document.getElementById('url-validation-results');
    const validationLoading = document.getElementById('validation-loading');
    const validationContent = document.getElementById('validation-content');
    
    if (!validationResults || !validationLoading || !validationContent) {
        console.error('Validation elements not found:', {
            validationResults: !!validationResults,
            validationLoading: !!validationLoading,
            validationContent: !!validationContent
        });
        alert('Error: Validation elements not found. Please refresh the page and try again.');
        return;
    }
    
    validationResults.style.display = 'block';
    validationLoading.style.display = 'block';
    validationContent.style.display = 'none';
    
    // Get CSRF token from the form (try multiple methods)
    let token = null;
    
    // Method 1: Try to get from hidden input
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput) {
        token = tokenInput.value;
        console.log('CSRF token found from input:', token ? 'Yes' : 'No');
    }
    
    // Method 2: Try to get from meta tag
    if (!token) {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            token = metaToken.getAttribute('content');
            console.log('CSRF token found from meta:', token ? 'Yes' : 'No');
        }
    }
    
    // Method 3: Try to get from any hidden input that might contain the token
    if (!token) {
        const allHiddenInputs = document.querySelectorAll('input[type="hidden"]');
        for (const input of allHiddenInputs) {
            if (input.value && input.value.length > 20) { // CSRF tokens are usually long
                token = input.value;
                console.log('CSRF token found from hidden input:', token ? 'Yes' : 'No');
                break;
            }
        }
    }
    
    if (!token) {
        console.error('CSRF token not found by any method');
        alert('Error: CSRF token not found. Please refresh the page and try again.');
        return;
    }
    
         const validationUrl = '{{ url("/predictions/validate-urls") }}';
     console.log('Sending validation request to:', validationUrl);
     console.log('Request payload:', { urls: urls });
    
         // Send validation request
     fetch(validationUrl, {
        method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ urls: urls })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
        console.log('Validation response:', data);
        validationLoading.style.display = 'none';
        validationContent.style.display = 'block';
        
            if (data.success) {
            displayValidationResults(data.data);
                } else {
            validationContent.innerHTML = `
                <div style="color: #dc2626; padding: 16px; background: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                    <strong>Validation Error:</strong> ${data.message || 'Unknown error occurred'}
                </div>
            `;
            }
        })
        .catch(error => {
        console.error('Validation error:', error);
        validationLoading.style.display = 'none';
        validationContent.style.display = 'block';
        validationContent.innerHTML = `
            <div style="color: #dc2626; padding: 16px; background: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                <strong>Network Error:</strong> ${error.message}
            </div>
        `;
    });
}

function displayValidationResults(data) {
    const validationContent = document.getElementById('validation-content');
    
    let html = `
        <div style="margin-bottom: 16px;">
            <h4 style="margin: 0 0 12px 0; color: #374151; font-size: 16px;">URL Validation Results</h4>
            <div style="display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
                <div style="padding: 8px 16px; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; color: #0369a1;">
                    <strong>Total:</strong> ${data.summary.total_urls}
                </div>
                <div style="padding: 8px 16px; background: #f0fdf4; border: 1px solid #10b981; border-radius: 8px; color: #166534;">
                    <strong>Accessible:</strong> ${data.summary.accessible_count}
                </div>
                <div style="padding: 8px 16px; background: #fef2f2; border: 1px solid #ef4444; border-radius: 8px; color: #dc2626;">
                    <strong>Inaccessible:</strong> ${data.summary.inaccessible_count}
                </div>
                <div style="padding: 8px 16px; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; color: #d97706;">
                    <strong>Success Rate:</strong> ${data.summary.success_rate}%
                </div>
            </div>
        </div>
    `;
    
    // Display recommendations
    if (data.recommendations && data.recommendations.length > 0) {
        html += `
            <div style="margin-bottom: 16px; padding: 16px; background: #f0f9ff; border-radius: 8px; border: 1px solid #0ea5e9;">
                <h5 style="margin: 0 0 8px 0; color: #0369a1; font-size: 14px;">💡 Recommendations</h5>
                <ul style="margin: 0; padding-left: 20px; color: #0369a1; font-size: 14px;">
                    ${data.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    // Display accessible URLs
    if (data.accessible_urls && data.accessible_urls.length > 0) {
        html += `
            <div style="margin-bottom: 16px;">
                <h5 style="margin: 0 0 8px 0; color: #166534; font-size: 14px;">✅ Accessible URLs</h5>
                ${data.accessible_urls.map(url => `
                    <div style="padding: 8px 12px; background: #f0fdf4; border: 1px solid #10b981; border-radius: 6px; margin-bottom: 4px; font-size: 14px; color: #166534;">
                        ${url.url} <span style="float: right; font-size: 12px;">${url.response_time}ms</span>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
         // Display inaccessible URLs with errors
     if (data.inaccessible_urls && data.inaccessible_urls.length > 0) {
         html += `
             <div style="margin-bottom: 16px;">
                 <h5 style="margin: 0 0 8px 0; color: #dc2626; font-size: 14px;">❌ Inaccessible URLs</h5>
                 ${data.inaccessible_urls.map(url => `
                     <div style="padding: 8px 12px; background: #fef2f2; border: 1px solid #ef4444; border-radius: 6px; margin-bottom: 4px; font-size: 14px; color: #dc2626;">
                         <div style="font-weight: 600;">${url.url}</div>
                         <div style="font-size: 12px; margin-top: 4px;">${url.error}</div>
                         ${url.status_code ? `<div style="font-size: 12px; color: #9ca3af;">Status: ${url.status_code}</div>` : ''}
                     </div>
                 `).join('')}
                 
                 <!-- AI General Knowledge Notice -->
                 <div style="margin-top: 12px; padding: 12px; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; color: #0369a1; font-size: 13px;">
                     <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                         <span style="font-size: 16px;">🤖</span>
                         <strong>AI Analysis Note:</strong>
                     </div>
                     <p style="margin: 0; line-height: 1.4;">
                         For inaccessible URLs, the AI will use its general knowledge and training data to provide relevant insights. 
                         While this may not include the most recent specific details from these sources, it will still deliver comprehensive 
                         analysis based on available information.
                     </p>
                 </div>
             </div>
         `;
     }
    
    validationContent.innerHTML = html;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
        
        // Set up initial remove button visibility
        updateRemoveButtonVisibility();

    // Show loading indicator when form is submitted
    const form = document.querySelector('form');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    if (form && loadingIndicator) {
    form.addEventListener('submit', function() {
        loadingIndicator.style.display = 'block';
        
        // Disable the submit button
        const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = '🔄 Processing...';
        submitButton.style.opacity = '0.7';
        submitButton.style.cursor = 'not-allowed';
            }
    });
    }
    
    console.log('Initialization complete');
});
</script>
@endsection
