@extends('layouts.app')

@section('content')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Form Card -->
        <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <form action="{{ route('predictions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Form Header -->
                <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                    <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">Predictions Analysis</h1>
                    <p style="color: #64748b; font-size: 14px; margin-top: 8px; margin-bottom: 0;">Fill in the details below to generate your AI-powered prediction analysis</p>
                </div>
                
                <!-- Basic Information Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Basic Information</h2>
                    
                    <!-- Report Title Field -->
                    <div style="margin-bottom: 24px;">
                        <label for="topic" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                            Report Title <span style="color: #dc2626;">*</span>
                            <span class="info-tooltip" data-tooltip="The title that will appear on your prediction report. This helps identify and organize your analysis reports. Choose a clear, descriptive title that summarizes the main topic of your prediction.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 16px;"></i>
                            </span>
                        </label>
                        <input type="text" 
                               id="topic" 
                               name="topic" 
                               value="{{ old('topic') }}"
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;"
                               placeholder="Malaysia E-commerce Market Growth 2024"
                               required>
                        @error('topic')
                            <p style="color: #dc2626; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prediction Period and Target Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        <!-- Prediction Period Field -->
                        <div>
                            <label for="prediction_horizon" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                                Prediction Period <span style="color: #dc2626;">*</span>
                                <span class="info-tooltip" data-tooltip="The time horizon for your prediction analysis. This determines how far into the future the AI will forecast. Shorter periods (days/weeks) focus on immediate trends, while longer periods (months/years) analyze broader patterns and strategic implications.">
                                    <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 16px;"></i>
                                </span>
                            </label>
                            <select id="prediction_horizon" 
                                    name="prediction_horizon" 
                                    style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb; cursor: pointer;"
                                    required>
                                <option value="">Select prediction time period</option>
                                <option value="next_two_days" {{ old('prediction_horizon') == 'next_two_days' ? 'selected' : '' }}>Next Two Days</option>
                                <option value="next_two_weeks" {{ old('prediction_horizon') == 'next_two_weeks' ? 'selected' : '' }}>Next Two Weeks</option>
                                <option value="next_month" {{ old('prediction_horizon') == 'next_month' ? 'selected' : '' }}>Next Month</option>
                                <option value="three_months" {{ old('prediction_horizon') == 'three_months' ? 'selected' : '' }}>3 Months</option>
                                <option value="six_months" {{ old('prediction_horizon') == 'six_months' ? 'selected' : '' }}>6 Months</option>
                                <option value="twelve_months" {{ old('prediction_horizon') == 'twelve_months' ? 'selected' : '' }}>12 Months</option>
                                <option value="two_years" {{ old('prediction_horizon') == 'two_years' ? 'selected' : '' }}>2 Years</option>
                            </select>
                            @error('prediction_horizon')
                                <p style="color: #dc2626; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Field -->
                        <div>
                            <label for="target" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                                Target <span style="color: #64748b; font-weight: 400; font-size: 12px;">(Optional)</span>
                                <span class="info-tooltip" data-tooltip="The specific entity, group, industry, or demographic that your prediction focuses on. This helps the AI tailor the analysis to the relevant context. Examples: specific companies, market sectors, demographic groups, or geographic regions.">
                                    <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 16px;"></i>
                                </span>
                            </label>
                            <input type="text" 
                                   id="target" 
                                   name="target" 
                                   value="{{ old('target') }}"
                                   style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb;"
                                   placeholder="Malaysian SMEs, Tech Startups, E-commerce Sector">
                            @error('target')
                                <p style="color: #dc2626; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Issue Details Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Issue Details</h2>
                    <div>
                        <label for="input_data" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                            Describe the issue or topic <span style="color: #dc2626;">*</span>
                            <span class="info-tooltip" data-tooltip="Provide detailed information about the issue, situation, or topic you want to analyze. Include relevant data, statistics, current trends, challenges, and context. The more detailed information you provide, the more accurate and comprehensive the AI prediction will be. This is the core input that drives the entire analysis.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 16px;"></i>
                            </span>
                        </label>
                        <textarea id="input_data" 
                                  name="input_data" 
                                  rows="6"
                                  style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: all 0.3s ease; background: #f9fafb; resize: vertical; font-family: inherit; line-height: 1.6;"
                                  placeholder="Malaysia's e-commerce sector has experienced rapid growth, with online sales increasing by 42% in 2023. However, Malaysian SMEs face challenges including rising operational costs, digital adoption barriers, and competition from international platforms. Consumer behavior shows increasing preference for local products, with 68% of Malaysian shoppers supporting local brands. The shift towards cashless payments is accelerating, with e-wallet usage growing by 55%. Supply chain disruptions and logistics costs remain concerns for businesses. What are the predictions for the next 12 months regarding market trends, consumer preferences, and business opportunities in Malaysia's digital economy?"
                                  required>{{ old('input_data') }}</textarea>
                        <p style="color: #64748b; font-size: 13px; margin-top: 8px; margin-bottom: 0;">Minimum 10 characters required</p>
                        @error('input_data')
                            <p style="color: #dc2626; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Resources Section (Collapsible) -->
                <div style="margin-bottom: 32px;">
                    <button type="button" 
                            id="toggle-optional" 
                            onclick="toggleOptionalSection()"
                            style="width: 100%; padding: 14px 16px; background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 8px; font-weight: 600; font-size: 14px; color: #374151; cursor: pointer; text-align: left; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s ease;">
                        <span>Additional Resources <span style="color: #64748b; font-weight: 400; font-size: 12px;">(Optional)</span></span>
                        <span id="toggle-icon" style="font-size: 18px; transition: transform 0.3s ease;">▼</span>
                    </button>
                    
                    <div id="optional-section" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                        <!-- Source URLs and File Upload -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <!-- Source URLs Field -->
                            <div>
                                <label style="display: flex; align-items: center; gap: 6px; margin-bottom: 10px; font-weight: 600; color: #374151; font-size: 14px;">
                                    Source URLs
                                    <span class="info-tooltip" data-tooltip="Add URLs to articles, reports, or web pages that provide additional context for your analysis. The AI will automatically fetch and read the content from these URLs, then cite specific facts, numbers, and insights from the source material in the analysis. This enhances the accuracy and credibility of predictions.">
                                        <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 16px;"></i>
                                    </span>
                                </label>
                                <div id="source-urls-container">
                                    <div class="source-url-row" style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                                        <input type="url" 
                                               name="source_urls[]" 
                                               style="width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; transition: all 0.3s ease; background: #f9fafb;"
                                               placeholder="https://www.thestar.com.my/business"
                                               pattern="https?://.+">
                                        <button type="button" 
                                                class="remove-source-url" 
                                                style="padding: 10px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; transition: all 0.3s ease; min-width: 44px; display: none; cursor: pointer;"
                                                onclick="removeSourceUrl(this)">
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px; margin-top: 8px;">
                                    <button type="button" 
                                            id="add-source-url" 
                                            style="padding: 8px 12px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.3s ease; flex: 1;"
                                            onclick="addSourceUrlField()">
                                        Add Source
                                    </button>
                                    <button type="button" 
                                            id="validate-urls-btn" 
                                            style="padding: 8px 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.3s ease; flex: 1;"
                                            onclick="validateUrls()">
                                        Validate
                                    </button>
                                </div>
                                <!-- URL Validation Results -->
                                <div id="url-validation-results" style="display: none; margin-top: 12px; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 12px; background: #f9fafb;">
                                    <div id="validation-loading" style="display: none; text-align: center; padding: 12px;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                            <div style="width: 16px; height: 16px; border: 2px solid #3b82f6; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                            <span style="color: #3b82f6; font-weight: 600; font-size: 13px;">Validating...</span>
                                        </div>
                                    </div>
                                    <div id="validation-content" style="display: none;"></div>
                                </div>
                                @error('source_urls')
                                    <p style="color: #dc2626; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- File Uploads Field -->
                            <div>
                                <label style="display: flex; align-items: center; gap: 6px; margin-bottom: 10px; font-weight: 600; color: #374151; font-size: 14px;">
                                    Upload Files
                                    <span class="info-tooltip" data-tooltip="Upload supporting documents such as PDF reports, Excel spreadsheets, CSV data files, or text documents. The AI will extract and analyze data from these files, including numerical data, trends, patterns, and structured information. This data is integrated with your text input to provide more comprehensive predictions.">
                                        <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 16px;"></i>
                                    </span>
                                </label>
                                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 2px dashed #cbd5e1; text-align: center;">
                                    <input type="file" 
                                           id="uploaded_files" 
                                           name="uploaded_files[]" 
                                           multiple
                                           accept=".pdf,.xlsx,.xls,.csv,.txt"
                                           style="width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; transition: all 0.3s ease; background: white; cursor: pointer;"
                                           onchange="handleFileSelection(this)">
                                    <p style="color: #64748b; font-size: 12px; margin-top: 8px; margin-bottom: 0;">
                                        PDF, Excel, CSV, TXT • Max 10MB per file
                                    </p>
                                    <!-- File Preview Area -->
                                    <div id="file-preview-container" style="display: none; margin-top: 12px;">
                                        <div id="file-preview-list" style="max-height: 120px; overflow-y: auto; font-size: 12px;"></div>
                                    </div>
                                </div>
                                @error('uploaded_files')
                                    <p style="color: #dc2626; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="submit-buttons-container" style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 24px; border-top: 2px solid #e2e8f0; margin-top: 32px;">
                    <a href="{{ route('dashboard') }}" style="display: inline-block; padding: 12px 24px; background: transparent; color: #64748b; text-decoration: none; border: 2px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 15px; transition: all 0.3s ease;">
                        Cancel
                    </a>
                    <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px 32px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                        Generate Prediction
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
    
    /* Info Tooltip Styles */
    .info-tooltip {
        position: relative;
        display: inline-block;
    }
    
    .info-tooltip:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 400;
        white-space: normal;
        width: 280px;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        line-height: 1.5;
        pointer-events: none;
    }
    
    .info-tooltip:hover::before {
        content: '';
        position: absolute;
        bottom: 115%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1f2937;
        z-index: 1001;
        pointer-events: none;
    }
    
    @media (max-width: 768px) {
        .info-tooltip:hover::after,
        .info-tooltip.active::after {
            width: calc(100vw - 32px);
            max-width: 320px;
            font-size: 12px;
            left: 50%;
            right: auto;
            transform: translateX(-50%);
            bottom: 125%;
        }
        
        .info-tooltip:hover::before,
        .info-tooltip.active::before {
            left: 50%;
            right: auto;
            transform: translateX(-50%);
            bottom: 115%;
        }
        
        /* For very small screens, position tooltip below icon */
        @media (max-width: 480px) {
            .info-tooltip:hover::after,
            .info-tooltip.active::after {
                bottom: auto;
                top: 125%;
            }
            
            .info-tooltip:hover::before,
            .info-tooltip.active::before {
                bottom: auto;
                top: 115%;
                border-top-color: transparent;
                border-bottom-color: #1f2937;
            }
        }
    }
    
    /* Responsive grid layout */
    @media (max-width: 1024px) {
        div[style*="grid-template-columns: 2fr 1fr"],
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 768px) {
        div[style*="max-width: 900px"] {
            padding: 16px !important;
        }
        
        div[style*="padding: 32px"] {
            padding: 20px !important;
        }
    }
    
    @media (max-width: 768px) {
         .source-url-row {
             flex-direction: column;
             gap: 6px;
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
             padding: 10px 16px;
             font-size: 13px;
             min-height: 44px;
         }
          
         /* Submit buttons responsive improvements */
         .submit-buttons-container a, .submit-buttons-container button[type="submit"] {
             width: 100%;
             justify-content: center;
             padding: 12px 20px;
             font-size: 14px;
             min-height: 44px;
             text-align: center;
         }
         
         /* URL input field mobile optimization */
         input[name="source_urls[]"] {
             font-size: 16px !important;
             padding: 10px 12px !important;
             min-height: 44px;
         }
         
         /* File upload section mobile optimization */
         #uploaded_files {
             font-size: 16px !important;
             padding: 10px 12px !important;
             min-height: 44px;
         }
         
         /* File preview container mobile layout */
         #file-preview-container {
             padding: 8px;
         }
         
         #file-preview-list {
             max-height: 120px;
         }
         
         /* File item mobile layout */
         #file-preview-list > div {
             flex-direction: column;
             gap: 6px;
             align-items: stretch;
         }
         
         #file-preview-list > div > div:first-child {
             justify-content: flex-start;
         }
         
         #file-preview-list button {
             width: 100%;
             padding: 8px 12px;
             font-size: 12px;
             min-height: 40px;
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
// File handling functions
function handleFileSelection(input) {
    const files = input.files;
    const previewContainer = document.getElementById('file-preview-container');
    const previewList = document.getElementById('file-preview-list');
    
    if (files.length > 0) {
        previewContainer.style.display = 'block';
        previewList.innerHTML = '';
        
        Array.from(files).forEach((file, index) => {
            const fileItem = createFilePreviewItem(file, index);
            previewList.appendChild(fileItem);
        });
    } else {
        previewContainer.style.display = 'none';
    }
}

function createFilePreviewItem(file, index) {
    const fileItem = document.createElement('div');
    fileItem.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: white; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 6px;';
    
    const fileInfo = document.createElement('div');
    fileInfo.style.cssText = 'display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0;';
    
    // File icon based on type
    const icon = getFileIcon(file.name);
    const iconDiv = document.createElement('div');
    iconDiv.style.cssText = 'font-size: 18px; flex-shrink: 0;';
    iconDiv.textContent = icon;
    
    const fileDetails = document.createElement('div');
    fileDetails.style.cssText = 'flex: 1; min-width: 0; overflow: hidden;';
    
    const fileName = document.createElement('div');
    fileName.style.cssText = 'font-weight: 600; color: #374151; font-size: 12px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;';
    fileName.textContent = file.name;
    
    const fileSize = document.createElement('div');
    fileSize.style.cssText = 'color: #64748b; font-size: 11px;';
    fileSize.textContent = formatFileSize(file.size);
    
    fileDetails.appendChild(fileName);
    fileDetails.appendChild(fileSize);
    
    fileInfo.appendChild(iconDiv);
    fileInfo.appendChild(fileDetails);
    
    // Remove button
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.style.cssText = 'padding: 6px 10px; background: #ef4444; color: white; border: none; border-radius: 4px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.3s ease; flex-shrink: 0; margin-left: 8px;';
    removeBtn.textContent = 'Remove';
    removeBtn.onclick = function() {
        removeFile(index);
    };
    
    fileItem.appendChild(fileInfo);
    fileItem.appendChild(removeBtn);
    
    return fileItem;
}

function getFileIcon(fileName) {
    const extension = fileName.split('.').pop().toLowerCase();
    switch (extension) {
        case 'pdf': return '📄';
        case 'xlsx': case 'xls': return '📊';
        case 'csv': return '📈';
        case 'txt': return '📝';
        default: return '📁';
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(index) {
    const input = document.getElementById('uploaded_files');
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    handleFileSelection(input);
}

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
        newRow.style.cssText = 'display: flex; gap: 6px; margin-bottom: 6px; align-items: center; border: 2px solid #10b981; background-color: #f0fdf4; padding: 4px; border-radius: 6px;';
        
        newRow.innerHTML = `
            <input type="url" 
                   name="source_urls[]" 
                   style="width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; transition: all 0.3s ease; background: #f9fafb;"
                   placeholder="https://www.thestar.com.my/business"
                   pattern="https?://.+">
            <button type="button" 
                    class="remove-source-url" 
                    style="padding: 10px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.3s ease; min-width: 44px;"
                    onclick="removeSourceUrl(this)">
                ×
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

// Toggle optional section
function toggleOptionalSection() {
    const section = document.getElementById('optional-section');
    const icon = document.getElementById('toggle-icon');
    const button = document.getElementById('toggle-optional');
    
    if (section.style.display === 'none' || !section.style.display) {
        section.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
        button.style.background = '#f1f5f9';
    } else {
        section.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
        button.style.background = '#f8fafc';
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
    
    // Add click/tap support for tooltips on mobile
    const tooltips = document.querySelectorAll('.info-tooltip');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('click', function(e) {
            // Only handle click on mobile devices
            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation();
                // Toggle active class
                const isActive = this.classList.contains('active');
                // Remove active from all tooltips
                tooltips.forEach(t => t.classList.remove('active'));
                // Toggle current tooltip
                if (!isActive) {
                    this.classList.add('active');
                }
            }
        });
    });
    
    // Close tooltips when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!e.target.closest('.info-tooltip')) {
                tooltips.forEach(t => t.classList.remove('active'));
            }
        }
    });
        
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
