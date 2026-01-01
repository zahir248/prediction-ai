@extends('layouts.app')

@section('content')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .cursor-layout {
        display: flex;
        height: calc(100vh - 72px);
        background: #ffffff;
        overflow: hidden;
    }
    
    .cursor-sidebar {
        width: 400px;
        background: #fafafa;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        position: relative;
        z-index: 1;
    }
    
    .cursor-main {
        flex: 1;
        background: #ffffff;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-left: 1px solid #e5e7eb;
        position: relative;
    }
    
    .cursor-main.scrollable {
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    /* AI-Themed Animated Background */
    .animated-ai-background {
        position: relative;
    }
    
    /* Floating Particles (Neural Network Nodes) */
    .ai-particle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, rgba(102, 126, 234, 0.15) 50%, rgba(102, 126, 234, 0.05) 100%);
        pointer-events: none;
        filter: blur(2px);
        z-index: 0;
    }
    
    .ai-particle-1 {
        width: 80px;
        height: 80px;
        top: 10%;
        left: 10%;
        animation: float-particle-1 20s ease-in-out infinite;
    }
    
    .ai-particle-2 {
        width: 60px;
        height: 60px;
        top: 25%;
        right: 15%;
        animation: float-particle-2 25s ease-in-out infinite;
    }
    
    .ai-particle-3 {
        width: 70px;
        height: 70px;
        top: 40%;
        left: 20%;
        animation: float-particle-3 18s ease-in-out infinite;
    }
    
    .ai-particle-4 {
        width: 65px;
        height: 65px;
        top: 55%;
        right: 25%;
        animation: float-particle-4 22s ease-in-out infinite;
    }
    
    .ai-particle-5 {
        width: 55px;
        height: 55px;
        top: 70%;
        left: 15%;
        animation: float-particle-5 30s ease-in-out infinite;
    }
    
    .ai-particle-6 {
        width: 75px;
        height: 75px;
        top: 85%;
        right: 20%;
        animation: float-particle-6 16s ease-in-out infinite;
    }
    
    /* Gradient Waves */
    .ai-wave {
        position: absolute;
        width: 200%;
        height: 150px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(102, 126, 234, 0.06) 25%, 
            rgba(139, 92, 246, 0.1) 50%, 
            rgba(102, 126, 234, 0.06) 75%, 
            transparent 100%);
        border-radius: 50%;
        opacity: 0.8;
        pointer-events: none;
        filter: blur(30px);
        z-index: 0;
    }
    
    .ai-wave-1 {
        top: -75px;
        left: -50%;
        animation: wave-move-1 15s ease-in-out infinite;
    }
    
    .ai-wave-2 {
        bottom: -75px;
        right: -50%;
        animation: wave-move-2 20s ease-in-out infinite;
    }
    
    /* Neural Network Connections */
    .ai-connection {
        position: absolute;
        height: 2px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(102, 126, 234, 0.3) 50%, 
            transparent 100%);
        pointer-events: none;
        opacity: 0.4;
        z-index: 0;
    }
    
    .ai-connection-1 {
        width: 200px;
        top: 30%;
        left: 15%;
        transform: rotate(25deg);
        animation: connection-pulse-1 3s ease-in-out infinite;
    }
    
    .ai-connection-2 {
        width: 180px;
        bottom: 35%;
        right: 20%;
        transform: rotate(-35deg);
        animation: connection-pulse-2 4s ease-in-out infinite;
    }
    
    .ai-connection-3 {
        width: 160px;
        top: 60%;
        left: 45%;
        transform: rotate(45deg);
        animation: connection-pulse-3 3.5s ease-in-out infinite;
    }
    
    /* Particle Animations */
    @keyframes float-particle-1 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(40px, 80px) scale(1.2); }
        50% { transform: translate(-20px, 150px) scale(0.9); }
        75% { transform: translate(30px, 200px) scale(1.1); }
    }
    
    @keyframes float-particle-2 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(-50px, -50px) scale(1.15); }
        50% { transform: translate(30px, -100px) scale(0.85); }
        75% { transform: translate(-40px, -150px) scale(1.2); }
    }
    
    @keyframes float-particle-3 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(60px, -75px) scale(1.3); }
        66% { transform: translate(-45px, -150px) scale(0.9); }
    }
    
    @keyframes float-particle-4 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(-40px, 100px) scale(1.1); }
        50% { transform: translate(50px, 200px) scale(0.9); }
        75% { transform: translate(-30px, 300px) scale(1.15); }
    }
    
    @keyframes float-particle-5 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        30% { transform: translate(-60px, -100px) scale(1.4); }
        60% { transform: translate(40px, -200px) scale(0.8); }
        90% { transform: translate(-50px, -300px) scale(1.2); }
    }
    
    @keyframes float-particle-6 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(50px, 125px) scale(1.25); }
        66% { transform: translate(-35px, 250px) scale(0.9); }
    }
    
    @keyframes wave-move-1 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(50px) rotate(180deg); }
    }
    
    @keyframes wave-move-2 {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        50% { transform: translateX(-50px) rotate(-180deg); }
    }
    
    @keyframes connection-pulse-1 {
        0%, 100% { opacity: 0.2; transform: rotate(25deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(25deg) scaleX(1.2); }
    }
    
    @keyframes connection-pulse-2 {
        0%, 100% { opacity: 0.2; transform: rotate(-35deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(-35deg) scaleX(1.15); }
    }
    
    @keyframes connection-pulse-3 {
        0%, 100% { opacity: 0.2; transform: rotate(45deg) scaleX(1); }
        50% { opacity: 0.4; transform: rotate(45deg) scaleX(1.1); }
    }
    
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }
    
    @keyframes pulse-glow {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
    }
    
    .cursor-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
    }
    
    .cursor-sidebar-content {
        flex: 1;
        padding: 16px;
        padding-bottom: 100px;
        overflow-x: visible;
    }
    
    .cursor-main-content {
        flex: 1;
        padding: 24px;
        max-width: 100%;
        width: 100%;
    }
    
    .cursor-section {
        margin-bottom: 24px;
    }
    
    .cursor-section-title {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        padding: 0 4px;
    }
    
    .cursor-item {
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 4px;
        cursor: pointer;
        transition: background 0.15s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #374151;
    }
    
    .cursor-item:hover {
        background: #f3f4f6;
    }
    
    .cursor-item.active {
        background: #eff6ff;
        color: #2563eb;
    }
    
    .cursor-tip-card {
        padding: 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 8px;
        font-size: 13px;
        line-height: 1.5;
        color: #4b5563;
    }
    
    .cursor-progress-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        font-size: 13px;
        color: #6b7280;
    }
    
    .cursor-progress-item.completed {
        color: #059669;
    }
    
    .cursor-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        text-decoration: none;
        color: #374151;
        font-size: 13px;
        border-radius: 6px;
        transition: background 0.15s ease;
    }
    
    .cursor-link:hover {
        background: #f3f4f6;
        color: #111827;
    }
    
    @media (max-width: 1024px) {
        .cursor-layout {
            flex-direction: column;
            height: auto;
        }
        
        .cursor-sidebar {
            width: 100%;
            border-right: none;
            border-top: 1px solid #e5e7eb;
            order: 1;
        }
        
        .cursor-main {
            border-left: none;
            border-bottom: 1px solid #e5e7eb;
            order: 2;
        }
    }
</style>

<div class="cursor-layout">
    <!-- Left Panel: Form -->
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-header" id="sidebarHeader">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analyze Prediction</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Fill in the form to generate analysis</p>
                </div>
        
        <!-- Prompt Details Header (hidden by default, shown during analysis) -->
        <div class="cursor-sidebar-header" id="promptDetailsHeader" style="display: none;">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analysis Input Details</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Review the details being analyzed</p>
        </div>
        
        <div class="cursor-sidebar-content">
            <!-- Form Card (shown by default) -->
            <div id="formCard">
                <form id="prediction-form" action="{{ route('predictions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                
                <!-- Basic Information Section -->
                    <div style="margin-bottom: 24px;">
                    <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Basic Information</h2>
                    
                    <!-- Report Title Field -->
                    <div style="margin-bottom: 20px;">
                        <label for="topic" style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                            Report Title <span style="color: #dc2626;">*</span>
                            <span class="info-tooltip" data-tooltip="The title that will appear on your prediction report. This helps identify and organize your analysis reports. Choose a clear, descriptive title that summarizes the main topic of your prediction.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 16px;"></i>
                            </span>
                        </label>
                        <input type="text" 
                               id="topic" 
                               name="topic" 
                               value="{{ old('topic') }}"
                               style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; transition: all 0.15s ease; background: #ffffff;"
                               placeholder="Malaysia E-commerce Market Growth 2024"
                               required>
                        @error('topic')
                            <p style="color: #dc2626; font-size: 13px; margin-top: 6px; margin-bottom: 0;">{{ $message }}</p>
                        @enderror
                    </div>

                        <!-- Prediction Period Field -->
                    <div style="margin-bottom: 20px;">
                        <label for="prediction_horizon" style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                                Prediction Period <span style="color: #dc2626;">*</span>
                                <span class="info-tooltip" data-tooltip="The time horizon for your prediction analysis. This determines how far into the future NUJUM will forecast. Shorter periods (days/weeks) focus on immediate trends, while longer periods (months/years) analyze broader patterns and strategic implications.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                                </span>
                            </label>
                            <select id="prediction_horizon" 
                                    name="prediction_horizon" 
                                style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; transition: all 0.15s ease; background: #ffffff; cursor: pointer;"
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
                            <p style="color: #dc2626; font-size: 12px; margin-top: 4px; margin-bottom: 0;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Field -->
                    <div style="margin-bottom: 20px;">
                        <label for="target" style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                            Target Audience/Subject <span style="color: #64748b; font-weight: 400; font-size: 11px;">(Optional)</span>
                                <span class="info-tooltip" data-tooltip="The specific entity, group, industry, or demographic that your prediction focuses on. This helps NUJUM tailor the analysis to the relevant context. Examples: specific companies, market sectors, demographic groups, or geographic regions.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                                </span>
                            </label>
                            <input type="text" 
                                   id="target" 
                                   name="target" 
                                   value="{{ old('target') }}"
                               style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; transition: all 0.15s ease; background: #ffffff;"
                                   placeholder="Malaysian SMEs, Tech Startups, E-commerce Sector">
                            @error('target')
                            <p style="color: #dc2626; font-size: 12px; margin-top: 4px; margin-bottom: 0;">{{ $message }}</p>
                            @enderror
                    </div>
                </div>

                <!-- Issue Details Section -->
                <div style="margin-bottom: 24px;">
                    <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Issue Details</h2>
                    <div>
                        <label for="input_data" style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                            Describe the issue or topic <span style="color: #dc2626;">*</span>
                            <span class="info-tooltip" data-tooltip="Provide detailed information about the issue, situation, or topic you want to analyze. Include relevant data, statistics, current trends, challenges, and context. The more detailed information you provide, the more accurate and comprehensive the NUJUM prediction will be. This is the core input that drives the entire analysis.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                            </span>
                        </label>
                        <textarea id="input_data" 
                                  name="input_data" 
                                  rows="6"
                                  style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; transition: all 0.15s ease; background: #ffffff; resize: vertical; font-family: inherit; line-height: 1.5;"
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
                            <!-- Source URLs Field -->
                        <div style="margin-bottom: 20px;">
                                <label style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                                    Source URLs
                                    <span class="info-tooltip" data-tooltip="Add URLs to articles, reports, or web pages that provide additional context for your analysis. NUJUM will automatically fetch and read the content from these URLs, then cite specific facts, numbers, and insights from the source material in the analysis. This enhances the accuracy and credibility of predictions.">
                                        <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                                    </span>
                                </label>
                                <div id="source-urls-container">
                                    <div class="source-url-row" style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                                        <input type="url" 
                                               name="source_urls[]" 
                                               style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; transition: all 0.15s ease; background: #ffffff;"
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
                                    <p style="color: #dc2626; font-size: 12px; margin-top: 4px; margin-bottom: 0;">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- File Uploads Field -->
                        <div style="margin-bottom: 20px;">
                                <label style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                                    Upload Files
                                    <span class="info-tooltip" data-tooltip="Upload supporting documents such as PDF reports, Excel spreadsheets, CSV data files, or text documents. NUJUM will extract and analyze data from these files, including numerical data, trends, patterns, and structured information. This data is integrated with your text input to provide more comprehensive predictions.">
                                        <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
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
                                    <p style="color: #dc2626; font-size: 12px; margin-top: 4px; margin-bottom: 0;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </form>
                </div>

            <!-- Prompt Details (hidden by default, shown during analysis) -->
            <div id="promptDetailsCard" style="display: none;">
                <div id="promptDetailsContent">
                    <!-- Prompt details will be populated here -->
                </div>
            </div>
        </div>
        
        <!-- Floating Action Buttons for Prompt Details (hidden by default, same position as Generate button) -->
        <div class="floating-submit-container" id="promptDetailsActions" style="display: none;">
            <div style="display: flex; gap: 12px;">
                <a href="#" id="exportBtn" onclick="event.preventDefault(); confirmExportFromCreate(); return false;" class="floating-submit-btn" style="flex: 1; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    Export
                </a>
                <a href="{{ route('predictions.create') }}" class="floating-submit-btn" style="flex: 1; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    New Prediction
                </a>
            </div>
                </div>
                
        <!-- Floating Submit Button -->
        <div class="floating-submit-container" id="floatingSubmitContainer">
            <button type="submit" form="prediction-form" class="floating-submit-btn" id="floatingSubmitBtn">
                        Generate
                    </button>
        </div>
                </div>
                
    <!-- Right Panel: Did You Know Section -->
    <div class="cursor-main animated-ai-background">
        <!-- Animated Background Elements -->
        <div id="animatedBackground" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;">
            <div class="ai-particle ai-particle-1"></div>
            <div class="ai-particle ai-particle-2"></div>
            <div class="ai-particle ai-particle-3"></div>
            <div class="ai-particle ai-particle-4"></div>
            <div class="ai-particle ai-particle-5"></div>
            <div class="ai-particle ai-particle-6"></div>
            <div class="ai-wave ai-wave-1"></div>
            <div class="ai-wave ai-wave-2"></div>
            <div class="ai-connection ai-connection-1"></div>
            <div class="ai-connection ai-connection-2"></div>
            <div class="ai-connection ai-connection-3"></div>
        </div>
        
        <div class="cursor-main-content" style="position: relative; z-index: 10; padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 100%;">
            <!-- Did You Know Section (shown by default, centered) -->
            <div id="didYouKnowSection" style="max-width: 600px; width: 100%; text-align: center;">
                <div style="margin-bottom: 32px; border: none; border-bottom: none; padding-bottom: 0;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 50%; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2); animation: pulse-glow 2s ease-in-out infinite;">
                        <i class="bi bi-lightbulb-fill" style="color: #f59e0b; font-size: 32px;"></i>
                    </div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0; padding: 0; border: none; border-bottom: none; letter-spacing: -0.5px;">
                        Did you know?
                    </h2>
                </div>
                <div id="typingFacts" style="min-height: 200px; color: #374151; font-size: 16px; line-height: 2; font-family: 'Georgia', 'Times New Roman', serif; padding: 24px 0;">
                    <span id="typingText" style="white-space: pre-wrap; word-wrap: break-word; display: inline;"></span><span id="typingCursor" style="display: inline-block; width: 3px; height: 22px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin-left: 4px; animation: blink 1s infinite; vertical-align: middle; border-radius: 2px;"></span>
    </div>
</div>

            <!-- Instructions Card (hidden by default, removed Quick Steps) -->
            <div id="instructionsCard" style="display: none;"></div>
            
            <!-- Progress Card (hidden by default) -->
            <div id="progressCard" style="display: none; background: transparent; padding: 24px; max-width: 500px; width: 100%; margin: 0 auto;">
                <!-- Title -->
                <h2 style="color: #1e293b; margin-bottom: 8px; font-size: 18px; font-weight: 700; text-align: center; border: none; border-bottom: none; text-decoration: none;">NUJUM Analysis in Progress</h2>
                
                <!-- Description -->
                <p style="color: #64748b; margin-bottom: 20px; font-size: 13px; line-height: 1.5; text-align: center; border: none; border-top: none;">
                    Generating your comprehensive analysis. This may take 2-5 minutes.
                </p>
                
                <!-- Progress Bar Container -->
                <div style="margin-bottom: 16px;">
                    <div style="background: #e2e8f0; border-radius: 8px; height: 10px; overflow: hidden; position: relative;">
                        <div id="progressBar" class="progress-bar-fill" style="background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%); height: 100%; border-radius: 8px; width: 0%; transition: width 0.3s ease; position: relative; overflow: hidden;">
                            <div class="progress-bar-shine" style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent); animation: shine 2s infinite;"></div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                        <span id="progressText" style="color: #64748b; font-size: 12px; font-weight: 500;">0%</span>
                        <span id="progressStatus" style="color: #0ea5e9; font-size: 12px; font-weight: 600;">Initializing...</span>
                    </div>
                </div>
                
                <!-- Note -->
                <p style="color: #94a3b8; margin-top: 16px; font-size: 11px; line-height: 1.4; text-align: center;">
                    Please do not close this window. Results will appear here when complete.
                </p>
            </div>
            
            <!-- Result Display (hidden by default, shown when analysis completes) -->
            <div id="resultCard" style="display: none; padding: 0;">
                <div id="resultContent">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NUJUM Analysis Progress Modal (Hidden - using left panel instead) -->
<div id="analysisProgressModal" class="analysis-modal-overlay" style="display: none !important; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div class="analysis-modal-content" style="background: white; border-radius: 20px; padding: 40px; max-width: 500px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); position: relative; animation: modalFadeIn 0.3s ease-out;">
        <!-- Close button (optional, but disabled during processing) -->
        <button id="closeModalBtn" onclick="closeAnalysisModal()" style="display: none; position: absolute; top: 16px; right: 16px; background: transparent; border: none; color: #64748b; font-size: 24px; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; transition: all 0.3s ease; padding: 0;">
            ×
        </button>
        
        <!-- Icon/Animation -->
        <div style="margin-bottom: 24px;">
            <div class="analysis-spinner" style="width: 64px; height: 64px; border: 4px solid #e0f2fe; border-top: 4px solid #0ea5e9; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto;"></div>
        </div>
        
        <!-- Title -->
        <h2 style="color: #1e293b; margin-bottom: 12px; font-size: 24px; font-weight: 700;">NUJUM Analysis in Progress</h2>
        
        <!-- Description -->
        <p style="color: #64748b; margin-bottom: 32px; font-size: 15px; line-height: 1.6;">
            NUJUM is reading your source URLs and generating a comprehensive analysis. This may take 2-5 minutes.
        </p>
        
        <!-- Progress Bar Container -->
        <div style="margin-bottom: 24px;">
            <div style="background: #e2e8f0; border-radius: 10px; height: 12px; overflow: hidden; position: relative;">
                <div id="progressBar" class="progress-bar-fill" style="background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%); height: 100%; border-radius: 10px; width: 0%; transition: width 0.3s ease; position: relative; overflow: hidden;">
                    <div class="progress-bar-shine" style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent); animation: shine 2s infinite;"></div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                <span id="progressText" style="color: #64748b; font-size: 13px; font-weight: 500;">0%</span>
                <span id="progressStatus" style="color: #0ea5e9; font-size: 13px; font-weight: 600;">Initializing...</span>
            </div>
        </div>
        
        <!-- Status Messages -->
        <div id="statusMessages" style="min-height: 60px; margin-top: 24px;">
            <div class="status-message active" style="padding: 12px 16px; background: #f0f9ff; border-radius: 8px; border-left: 3px solid #0ea5e9; margin-bottom: 8px; text-align: left;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="status-dot" style="width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%; animation: pulse 2s infinite;"></div>
                    <span style="color: #0369a1; font-size: 14px; font-weight: 500;">Processing your request...</span>
                </div>
            </div>
        </div>
        
        <!-- Note -->
        <p style="color: #94a3b8; margin-top: 24px; font-size: 12px; line-height: 1.5;">
            Please do not close this window. You will be redirected automatically when the analysis is complete.
        </p>
    </div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    @keyframes shine {
        0% {
            left: -100%;
        }
        100% {
            left: 100%;
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.2);
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .analysis-modal-overlay {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .progress-bar-fill {
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }
    
    .status-message {
        transition: all 0.3s ease;
    }
    
    .status-message.active {
        animation: slideIn 0.3s ease-out;
    }
    
    /* Cursor-style buttons */
    .cursor-sidebar-content button[type="submit"] {
        width: 100%;
        background: #2563eb;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease;
        margin-top: 8px;
    }
    
    .cursor-sidebar-content button[type="submit"]:hover {
        background: #1d4ed8;
    }
    
    .cursor-sidebar-content .submit-buttons-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }
    
    .cursor-sidebar-content .submit-buttons-container a {
        width: 100%;
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #d1d5db;
        color: #374151;
        text-align: center;
        text-decoration: none;
        transition: all 0.15s ease;
        display: block;
    }
    
    .cursor-sidebar-content .submit-buttons-container a:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }
    
    /* Floating Submit Button */
    .floating-submit-container {
        position: sticky;
        bottom: 0;
        width: 100%;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 16px;
        z-index: 100;
        box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.05);
        flex-shrink: 0;
    }
    
    .floating-submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .floating-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }
    
    /* Export button hover effect */
    #exportBtn:hover {
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }
    
    .floating-submit-btn:active {
        transform: translateY(0);
    }
    
    /* Floating action buttons for prompt details */
    #promptDetailsActions .floating-submit-btn {
        min-width: 0;
    }
    
    #promptDetailsActions .floating-submit-btn i {
        font-size: 14px;
    }
    
    .cursor-sidebar-content {
        padding-bottom: 0;
    }
    
    .cursor-main-content button[type="submit"] {
        background: #2563eb;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    
    .cursor-main-content button[type="submit"]:hover {
        background: #1d4ed8;
    }
    
    .cursor-main-content a[style*="Cancel"] {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid #d1d5db;
        color: #374151;
        transition: all 0.15s ease;
    }
    
    .cursor-main-content a[style*="Cancel"]:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }
    
    /* Modal Responsive Styles */
    @media (max-width: 768px) {
        .analysis-modal-content {
            padding: 32px 24px !important;
            max-width: 90% !important;
            border-radius: 16px !important;
        }
        
        .analysis-modal-content h2 {
            font-size: 20px !important;
        }
        
        .analysis-modal-content p {
            font-size: 14px !important;
        }
        
        .analysis-spinner {
            width: 48px !important;
            height: 48px !important;
            border-width: 3px !important;
        }
        
        #statusMessages {
            min-height: 50px !important;
        }
        
        .status-message {
            padding: 10px 12px !important;
            font-size: 13px !important;
        }
    }
    
    @media (max-width: 480px) {
        .analysis-modal-content {
            padding: 24px 20px !important;
            max-width: 95% !important;
            border-radius: 12px !important;
        }
        
        .analysis-modal-content h2 {
            font-size: 18px !important;
            margin-bottom: 8px !important;
        }
        
        .analysis-modal-content p {
            font-size: 13px !important;
            margin-bottom: 24px !important;
        }
        
        .analysis-spinner {
            width: 40px !important;
            height: 40px !important;
            margin-bottom: 16px !important;
        }
        
        #statusMessages {
            min-height: 40px !important;
            margin-top: 16px !important;
        }
        
        .status-message {
            padding: 8px 10px !important;
            font-size: 12px !important;
            margin-bottom: 6px !important;
        }
        
        .status-dot {
            width: 6px !important;
            height: 6px !important;
        }
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
        cursor: help;
    }
    
    .info-tooltip-tooltip {
        position: fixed;
        background: #1f2937;
        color: white;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 400;
        white-space: normal;
        width: 280px;
        z-index: 99999;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        line-height: 1.5;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    
    .info-tooltip-tooltip.show {
        opacity: 1;
        visibility: visible;
    }
    
    .info-tooltip-arrow {
        position: fixed;
        border: 6px solid transparent;
        z-index: 99999;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    
    .info-tooltip-arrow.show {
        opacity: 1;
        visibility: visible;
    }
    
    /* Ensure tooltips appear above all panels */
    .cursor-layout {
        position: relative;
    }
    
    .cursor-main {
        z-index: 1;
    }
    
    
    /* Cursor-style form inputs */
    .cursor-sidebar-content input[type="text"],
    .cursor-sidebar-content input[type="url"],
    .cursor-sidebar-content textarea,
    .cursor-sidebar-content select {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 8px 10px;
        font-size: 13px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        background: #ffffff;
    }
    
    .cursor-sidebar-content input[type="text"]:focus,
    .cursor-sidebar-content input[type="url"]:focus,
    .cursor-sidebar-content textarea:focus,
    .cursor-sidebar-content select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .cursor-sidebar-content label {
            font-size: 12px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        display: block;
    }
    
    .cursor-sidebar-content .info-tooltip {
        font-size: 12px;
    }
    
    .cursor-sidebar-content textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    /* Progress Card Styles - Centered */
    #progressCard {
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
    }
    
    /* Result Display Styles - No card, full display */
    #resultCard {
        width: 100%;
        padding: 0;
        display: block;
    }
    
    #resultContent {
        width: 100%;
        max-width: 100%;
    }
    
    /* When results are shown, ensure cursor-main-content displays properly */
    .cursor-main.scrollable .cursor-main-content {
        display: block !important;
        align-items: flex-start !important;
        justify-content: flex-start !important;
    }
    
    /* Ensure progress card is centered when displayed */
    .cursor-main-content:has(#progressCard[style*="display: block"]) {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    /* Result styles matching history page */
    #resultContent .prediction-topic {
        font-size: 24px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin: 0 0 8px 0 !important;
    }
    
    #resultContent h2 {
        font-size: 16px !important;
        font-weight: 600 !important;
        color: #374151 !important;
        margin-bottom: 16px !important;
        padding-bottom: 8px !important;
        border-bottom: 1px solid #e5e7eb !important;
        text-transform: none !important;
        letter-spacing: normal !important;
    }
    
    #resultContent h3,
    #resultContent h4 {
        font-size: 14px !important;
        margin-bottom: 8px !important;
        text-transform: none !important;
    }
    
    #resultContent p {
        font-size: 14px !important;
        line-height: 1.6 !important;
        text-transform: none !important;
    }
    
    #resultContent ul,
    #resultContent ol {
        padding-left: 16px !important;
        margin: 8px 0 !important;
    }
    
    #resultContent li {
        font-size: 14px !important;
        margin-bottom: 6px !important;
        text-transform: none !important;
    }
    
    /* Remove uppercase from all text in result content */
    #resultContent * {
        text-transform: none !important;
    }
    
    /* Header container style */
    #resultContent > div > div[style*="border-bottom: 2px"] {
        border-bottom: 2px solid #e2e8f0 !important;
        padding-bottom: 20px !important;
        margin-bottom: 32px !important;
    }
    
    
    .cursor-main-content input[type="text"],
    .cursor-main-content input[type="url"],
    .cursor-main-content textarea,
    .cursor-main-content select {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        background: #ffffff;
    }
    
    .cursor-main-content input[type="text"]:focus,
    .cursor-main-content input[type="url"]:focus,
    .cursor-main-content textarea:focus,
    .cursor-main-content select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .cursor-main-content label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }
    
    .cursor-main-content h2 {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    /* Override for Did you know section */
    #didYouKnowSection h2 {
        text-transform: none !important;
        border: none !important;
        border-bottom: none !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }
    
    #didYouKnowSection > div {
        border: none !important;
        border-bottom: none !important;
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
                    <div style="padding: 8px 12px; background: #f0fdf4; border: 1px solid #10b981; border-radius: 6px; margin-bottom: 4px; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                        <div style="flex: 1; min-width: 0; word-break: break-all; font-size: 12px; color: #166534; line-height: 1.5;">
                            ${url.url}
                        </div>
                        <div style="flex-shrink: 0; font-size: 11px; color: #059669; font-weight: 600; white-space: nowrap;">
                            ${url.response_time}ms
                        </div>
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
                 
                 <!-- NUJUM General Knowledge Notice -->
                 <div style="margin-top: 12px; padding: 12px; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; color: #0369a1; font-size: 13px;">
                     <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                         <span style="font-size: 16px;">🤖</span>
                         <strong>NUJUM Analysis Note:</strong>
                     </div>
                     <p style="margin: 0; line-height: 1.4;">
                         For inaccessible URLs, NUJUM will use its general knowledge and training data to provide relevant insights. 
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

// Form Progress Tracking
function updateFormProgress() {
    const progressItems = [
        { id: 'progress-topic', field: 'topic' },
        { id: 'progress-horizon', field: 'prediction_horizon' },
        { id: 'progress-input', field: 'input_data' }
    ];
    
    progressItems.forEach(({ id, field }) => {
        const item = document.getElementById(id);
        if (!item) return;
        
        const fieldElement = document.querySelector(`[name="${field}"], #${field}`);
        const icon = item.querySelector('i');
        const text = item.querySelector('span');
        
        if (fieldElement && fieldElement.value && fieldElement.value.trim() !== '') {
            icon.className = 'bi bi-check-circle-fill';
            icon.style.color = '#059669';
            item.classList.add('completed');
            text.style.color = '#059669';
        } else {
            icon.className = 'bi bi-circle';
            icon.style.color = '#d1d5db';
            item.classList.remove('completed');
            text.style.color = '#6b7280';
        }
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
    
    // Dynamic tooltip positioning with fixed positioning
    let tooltipElement = null;
    let tooltipArrow = null;
    
    function createTooltipElements() {
        if (!tooltipElement) {
            tooltipElement = document.createElement('div');
            tooltipElement.className = 'info-tooltip-tooltip';
            document.body.appendChild(tooltipElement);
        }
        if (!tooltipArrow) {
            tooltipArrow = document.createElement('div');
            tooltipArrow.className = 'info-tooltip-arrow';
            document.body.appendChild(tooltipArrow);
        }
    }
    
    function showTooltip(element, text) {
        createTooltipElements();
        
        const rect = element.getBoundingClientRect();
        const isInSidebar = element.closest('.cursor-sidebar');
        const tooltipWidth = 280;
        const arrowSize = 6;
        const spacing = 12;
        
        // Set tooltip content first to calculate height
        tooltipElement.textContent = text;
        tooltipElement.style.width = tooltipWidth + 'px';
        tooltipElement.style.visibility = 'hidden';
        tooltipElement.style.display = 'block';
        const tooltipHeight = tooltipElement.offsetHeight;
        tooltipElement.style.display = '';
        tooltipElement.style.visibility = '';
        
        let tooltipLeft, tooltipTop, arrowLeft, arrowTop;
        let arrowDirection = 'top';
        
        if (isInSidebar) {
            // Show tooltip to the left (towards center)
            tooltipLeft = rect.left - tooltipWidth - spacing;
            tooltipTop = rect.top + (rect.height / 2) - (tooltipHeight / 2);
            
            // Arrow pointing right
            arrowLeft = rect.left - spacing;
            arrowTop = rect.top + (rect.height / 2);
            arrowDirection = 'right';
            
            // Ensure tooltip doesn't go off screen
            if (tooltipLeft < 10) {
                tooltipLeft = rect.right + spacing;
                arrowLeft = rect.right;
                arrowDirection = 'left';
            }
        } else {
            // Show tooltip above
            tooltipLeft = rect.left + (rect.width / 2) - (tooltipWidth / 2);
            tooltipTop = rect.top - tooltipHeight - spacing;
            
            // Arrow pointing down
            arrowLeft = rect.left + (rect.width / 2);
            arrowTop = rect.top - spacing;
            arrowDirection = 'top';
            
            // Ensure tooltip doesn't go off screen
            if (tooltipTop < 10) {
                tooltipTop = rect.bottom + spacing;
                arrowTop = rect.bottom;
                arrowDirection = 'bottom';
            }
            if (tooltipLeft < 10) {
                tooltipLeft = 10;
            }
            if (tooltipLeft + tooltipWidth > window.innerWidth - 10) {
                tooltipLeft = window.innerWidth - tooltipWidth - 10;
            }
        }
        
        // Set tooltip position
        tooltipElement.style.left = tooltipLeft + 'px';
        tooltipElement.style.top = tooltipTop + 'px';
        tooltipElement.classList.add('show');
        
        // Set arrow position and direction
        tooltipArrow.style.left = arrowLeft + 'px';
        tooltipArrow.style.top = arrowTop + 'px';
        tooltipArrow.style.transform = 'translate(-50%, -50%)';
        
        // Set arrow direction
        tooltipArrow.style.borderTopColor = arrowDirection === 'top' ? '#1f2937' : 'transparent';
        tooltipArrow.style.borderBottomColor = arrowDirection === 'bottom' ? '#1f2937' : 'transparent';
        tooltipArrow.style.borderLeftColor = arrowDirection === 'left' ? '#1f2937' : 'transparent';
        tooltipArrow.style.borderRightColor = arrowDirection === 'right' ? '#1f2937' : 'transparent';
        
        tooltipArrow.classList.add('show');
    }
    
    function hideTooltip() {
        if (tooltipElement) {
            tooltipElement.classList.remove('show');
        }
        if (tooltipArrow) {
            tooltipArrow.classList.remove('show');
        }
    }
    
    // Add hover support for tooltips
    const tooltips = document.querySelectorAll('.info-tooltip');
    tooltips.forEach(tooltip => {
        const tooltipText = tooltip.getAttribute('data-tooltip');
        
        tooltip.addEventListener('mouseenter', function(e) {
            if (tooltipText) {
                showTooltip(this, tooltipText);
            }
        });
        
        tooltip.addEventListener('mouseleave', function() {
            hideTooltip();
        });
        
        // Mobile click support
        tooltip.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation();
                const isActive = this.classList.contains('active');
                tooltips.forEach(t => t.classList.remove('active'));
                if (!isActive && tooltipText) {
                    this.classList.add('active');
                    showTooltip(this, tooltipText);
                } else {
                    hideTooltip();
                }
            }
        });
    });
    
    // Close tooltips when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!e.target.closest('.info-tooltip')) {
                tooltips.forEach(t => t.classList.remove('active'));
                hideTooltip();
            }
        }
    });
    
    // Hide tooltip on scroll
    window.addEventListener('scroll', hideTooltip, true);
        
        // Set up initial remove button visibility
        updateRemoveButtonVisibility();

    // Track form progress
    const formFields = document.querySelectorAll('input, textarea, select');
    formFields.forEach(field => {
        field.addEventListener('input', updateFormProgress);
        field.addEventListener('change', updateFormProgress);
    });
    
    // Initial progress update
    updateFormProgress();

    // Show analysis progress in left panel when form is submitted
    const form = document.getElementById('prediction-form');
    const instructionsCard = document.getElementById('instructionsCard');
    const progressCard = document.getElementById('progressCard');
    const resultCard = document.getElementById('resultCard');
    const formCard = document.getElementById('formCard');
    const promptDetailsCard = document.getElementById('promptDetailsCard');
    const promptDetailsContent = document.getElementById('promptDetailsContent');
    const didYouKnowSection = document.getElementById('didYouKnowSection');
    const typingText = document.getElementById('typingText');
    const typingCursor = document.getElementById('typingCursor');
    
    // Make currentPredictionId globally accessible
    window.currentPredictionId = null;
    let pollingInterval = null;
    let typingAnimationInterval = null;
    let currentFactIndex = 0;
    let isDeleting = false;
    let currentText = '';
    
    // AI Facts for "Did you know?" section
    const aiFacts = [
        "AI copilots become standard in most jobs. Writing, coding, design, accounting, and customer support roles will commonly include AI copilots, similar to how spreadsheets became standard tools.",
        "Most software will be partially AI-generated. Not fully autonomous—but large portions of code, tests, UI layouts, and documentation will be created by AI and reviewed by humans.",
        "AI models shift from \"general\" to task-specialized. More companies will use smaller, cheaper, domain-specific models (legal AI, medical AI, finance AI) instead of one massive model.",
        "AI runs more on local devices (edge AI). Phones, laptops, cars, and cameras will increasingly run AI offline for privacy, speed, and lower cloud costs.",
        "Search engines become answer engines. Traditional \"10 blue links\" search will keep declining, replaced by AI-generated summaries with source citations.",
        "AI regulation becomes unavoidable and fragmented. Different regions (EU, US, China, ASEAN) will enforce different AI rules, forcing companies to customize models by country.",
        "Fake content detection becomes mandatory. Watermarking, content provenance, and AI-detection systems will be required for news, elections, and education platforms.",
        "AI increases productivity—but not equally. Individuals and companies that know how to use AI well will gain large advantages, widening skill and income gaps.",
        "Customer service is mostly AI-first. Humans will handle only complex or emotional cases; AI will manage the majority of chats, calls, and tickets.",
        "AI literacy becomes a core education skill. Schools and universities will teach how to work with AI (prompting, verifying, ethics), not just how to avoid it."
    ];
    
    // Typing animation function
    function startTypingAnimation() {
        if (!typingText || !typingCursor) return;
        
        // Clear any existing animation
        if (typingAnimationInterval) {
            clearTimeout(typingAnimationInterval);
        }
        
        function animate() {
            // Get current fact dynamically (not captured in closure)
            const currentFact = aiFacts[currentFactIndex];
            
            if (isDeleting) {
                // Delete characters
                if (currentText.length > 0) {
                    currentText = currentText.substring(0, currentText.length - 1);
                    typingText.textContent = currentText;
                    typingCursor.style.display = 'inline-block';
                    typingAnimationInterval = setTimeout(animate, 30); // Fast deletion
                } else {
                    // Finished deleting, move to next fact
                    isDeleting = false;
                    currentFactIndex = (currentFactIndex + 1) % aiFacts.length;
                    typingAnimationInterval = setTimeout(animate, 500); // Pause before typing new fact
                }
            } else {
                // Type characters
                if (currentText.length < currentFact.length) {
                    currentText = currentFact.substring(0, currentText.length + 1);
                    typingText.textContent = currentText;
                    typingCursor.style.display = 'inline-block';
                    
                    // Random delay between 20-50ms for natural typing effect
                    const delay = Math.random() * 30 + 20;
                    typingAnimationInterval = setTimeout(animate, delay);
                } else {
                    // Finished typing, wait then start deleting
                    typingCursor.style.display = 'inline-block';
                    typingAnimationInterval = setTimeout(() => {
                        isDeleting = true;
                        typingAnimationInterval = setTimeout(animate, 3000); // Wait 3 seconds before deleting
                    }, 3000);
                }
            }
        }
        
        // Start animation
        animate();
    }
    
    // Start typing animation on page load
    if (didYouKnowSection && typingText) {
        // Initialize with first fact
        currentText = '';
        isDeleting = false;
        currentFactIndex = 0;
        setTimeout(() => {
            startTypingAnimation();
        }, 500);
    }
    
    if (form && instructionsCard && progressCard) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show progress and prompt details
            showAnalysisProgress();
            showPromptDetails();
            
            // Disable the submit button
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';
                submitButton.style.opacity = '0.7';
                submitButton.style.cursor = 'not-allowed';
            }
            
            // Submit form via AJAX
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok && response.status !== 422) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.prediction_id) {
                    // Validate and store prediction ID
                    const predictionId = parseInt(data.prediction_id);
                    if (isNaN(predictionId) || predictionId <= 0) {
                        console.error('Invalid prediction ID received:', data.prediction_id);
                        showError('Invalid prediction ID received. Please try again.');
                        // Re-enable submit button on error
                        const submitButton = form.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = 'Generate';
                            submitButton.style.opacity = '1';
                        }
                        return;
                    }
                    
                    window.currentPredictionId = predictionId; // Store globally
                    
                    // Store prediction ID for export
                    // Export button will use currentPredictionId when clicked
                    
                    // Start polling immediately since processing happens synchronously
                    setTimeout(() => {
                        checkPredictionStatus(window.currentPredictionId);
                    }, 2000); // Wait 2 seconds before first check
                } else if (data.error) {
                    showError(data.error);
                    // Re-enable submit button on error
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Generate';
                        submitButton.style.opacity = '1';
                    }
                } else if (data.errors) {
                    // Validation errors
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    showError('Validation error: ' + errorMessages);
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Generate';
                        submitButton.style.opacity = '1';
                    }
                }
            })
            .catch(error => {
                console.error('Form submission error:', error);
                showError('An error occurred. Please try again.');
                // Re-enable submit button on error
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Generate';
                    submitButton.style.opacity = '1';
                }
            });
        });
    }
    
    function showPromptDetails() {
        if (!formCard || !promptDetailsCard) return;
        
        // Hide form, header, and submit button, show prompt details
        formCard.style.display = 'none';
        promptDetailsCard.style.display = 'block';
        
        // Hide prompt details action buttons during analysis
        const promptDetailsActions = document.getElementById('promptDetailsActions');
        if (promptDetailsActions) {
            promptDetailsActions.style.display = 'none';
        }
        
        // Hide Analyze Prediction header, show Analysis Input Details header
        const sidebarHeader = document.getElementById('sidebarHeader');
        if (sidebarHeader) {
            sidebarHeader.style.display = 'none';
        }
        const promptDetailsHeader = document.getElementById('promptDetailsHeader');
        if (promptDetailsHeader) {
            promptDetailsHeader.style.display = 'block';
        }
        
        // Hide floating submit button
        const floatingSubmitContainer = document.getElementById('floatingSubmitContainer');
        if (floatingSubmitContainer) {
            floatingSubmitContainer.style.display = 'none';
        }
        
        // Get form values
        const topic = document.getElementById('topic')?.value || 'N/A';
        const horizon = document.getElementById('prediction_horizon')?.value || 'N/A';
        const inputData = document.getElementById('input_data')?.value || 'N/A';
        const target = document.getElementById('target')?.value || 'N/A';
        const sourceUrls = Array.from(document.querySelectorAll('input[name="source_urls[]"]')).map(input => input.value).filter(url => url);
        const files = document.getElementById('uploaded_files')?.files || [];
        
        // Build prompt details HTML - informational display style (not form fields)
        let detailsHtml = `
            <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Report Title</div>
                <div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${topic}</div>
            </div>
            <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Prediction Period</div>
                <div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${horizon.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
            </div>
        `;
        
        if (target && target !== 'N/A') {
            detailsHtml += `
                <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Target</div>
                    <div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${target}</div>
                </div>
            `;
        }
        
        // Add Issue Description
        if (inputData && inputData !== 'N/A') {
            detailsHtml += `
                <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Issue Details</div>
                    <div style="font-size: 13px; color: #374151; line-height: 1.6; white-space: pre-wrap; word-wrap: break-word;">${inputData}</div>
                </div>
            `;
        }
        
        if (sourceUrls.length > 0) {
            detailsHtml += `
                <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Source URLs (${sourceUrls.length})</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        ${sourceUrls.map(url => `<div style="font-size: 12px; color: #2563eb; word-break: break-all; line-height: 1.5;">${url}</div>`).join('')}
                    </div>
                </div>
            `;
        }
        
        if (files.length > 0) {
            detailsHtml += `
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Uploaded Files (${files.length})</div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        ${Array.from(files).map(file => `<div style="font-size: 12px; color: #4b5563; line-height: 1.5;">${file.name} <span style="color: #9ca3af;">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span></div>`).join('')}
                    </div>
                </div>
            `;
        }
        
        promptDetailsContent.innerHTML = detailsHtml;
    }
    
    
    function checkPredictionStatus(predictionId) {
        // Validate predictionId before making request
        if (!predictionId || predictionId === 'null' || predictionId === 'undefined') {
            console.error('Status check error: Invalid prediction ID', predictionId);
            // Don't continue polling if ID is invalid
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
            showError('Invalid prediction ID. Please refresh the page and try again.');
            return;
        }
        
        // Generate URL using base URL to handle subdirectory deployments (cPanel)
        const baseUrl = '{{ url("/") }}';
        const statusUrl = `${baseUrl}/predictions/${predictionId}/model-info`;
        
        // Check status via API endpoint
        fetch(statusUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            // Log response details for debugging
            if (!response.ok) {
                console.error('Status check failed:', {
                    status: response.status,
                    statusText: response.statusText,
                    url: statusUrl,
                    predictionId: predictionId
                });
                throw new Error(`Failed to fetch status: ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'completed' || data.status === 'completed_with_warnings') {
                // Clear polling interval
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
                
                // Update progress to 100%
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');
                if (progressBar) {
                    progressBar.style.width = '100%';
                }
                if (progressText) {
                    progressText.textContent = '100%';
                }
                
                // Show success toast
                showToast('Analysis completed successfully!', 'success');
                
                // Fetch full result HTML
                setTimeout(() => {
                    fetchPredictionResult(predictionId);
                }, 500);
            } else if (data.status === 'failed') {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
                showError('Analysis failed. Please try again.');
            } else {
                // Still processing, continue polling
                if (!pollingInterval) {
                    pollingInterval = setInterval(() => {
                        checkPredictionStatus(predictionId);
                    }, 3000);
                }
            }
        })
        .catch(error => {
            console.error('Status check error:', error);
            console.error('Error details:', {
                message: error.message,
                predictionId: predictionId,
                url: statusUrl
            });
            
            // Only continue polling if it's a network error and we have a valid ID
            // Don't poll indefinitely on 404/403 errors
            if (predictionId && !error.message.includes('404') && !error.message.includes('403')) {
                if (!pollingInterval) {
                    pollingInterval = setInterval(() => {
                        checkPredictionStatus(predictionId);
                    }, 3000);
                }
            } else {
                // Stop polling on authentication/authorization errors
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
                // Don't show error for network issues during processing, but log it
                if (error.message.includes('404') || error.message.includes('403')) {
                    showError('Unable to check prediction status. Please refresh the page.');
                }
            }
        });
    }
    
    function fetchPredictionResult(predictionId) {
        // Validate predictionId
        if (!predictionId || predictionId === 'null' || predictionId === 'undefined') {
            console.error('Fetch result error: Invalid prediction ID', predictionId);
            showError('Invalid prediction ID. Please refresh the page and try again.');
            return;
        }
        
        // Clear polling
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
        
        // Store prediction ID globally for export
        window.currentPredictionId = predictionId;
        
        // Generate URL using base URL to handle subdirectory deployments (cPanel)
        const baseUrl = '{{ url("/") }}';
        const resultUrl = `${baseUrl}/predictions/${predictionId}`;
        
        // Fetch prediction HTML
        fetch(resultUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract the main content area
            const mainContent = doc.querySelector('.prediction-main-card');
            if (mainContent) {
                // Remove header actions (back button, export, etc.) for cleaner display
                const headerActions = mainContent.querySelector('.header-actions');
                if (headerActions) {
                    headerActions.remove();
                }
                
                displayResult(mainContent.innerHTML);
            } else {
                showError('Could not load results. Please refresh the page.');
            }
        })
        .catch(error => {
            console.error('Error fetching result:', error);
            console.error('Error details:', {
                message: error.message,
                predictionId: predictionId,
                url: resultUrl
            });
            showError('Error loading results. Please refresh the page.');
        });
    }
    
    function displayResult(resultHtml) {
        if (!progressCard || !resultCard) return;
        
        // Hide progress, did you know section, and animated background, show result
        progressCard.style.display = 'none';
        if (didYouKnowSection) {
            didYouKnowSection.style.display = 'none';
        }
        resultCard.style.display = 'block';
        
        // Stop typing animation
        if (typingAnimationInterval) {
            clearInterval(typingAnimationInterval);
            typingAnimationInterval = null;
        }
        
        // Show prompt details action buttons after results are complete
        const promptDetailsActions = document.getElementById('promptDetailsActions');
        if (promptDetailsActions) {
            promptDetailsActions.style.display = 'block';
        }
        
        // Hide animated background
        const animatedBackground = document.getElementById('animatedBackground');
        if (animatedBackground) {
            animatedBackground.style.display = 'none';
        }
        
        // Make scrollable when showing results (background is already white)
        const mainPanel = document.querySelector('.cursor-main');
        if (mainPanel) {
            mainPanel.classList.add('scrollable');
        }
        
        // Update cursor-main-content styling for result display
        const mainContent = document.querySelector('.cursor-main-content');
        if (mainContent) {
            mainContent.style.display = 'block';
            mainContent.style.alignItems = 'flex-start';
            mainContent.style.justifyContent = 'flex-start';
            mainContent.style.padding = '24px';
            mainContent.style.minHeight = 'auto';
        }
        
        // Populate result content
        const resultContent = document.getElementById('resultContent');
        if (resultContent) {
            // Create a temporary container to parse and modify the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = resultHtml;
            
            // Remove header actions (back button, export, etc.) for cleaner display
            const headerActions = tempDiv.querySelector('.header-actions');
            if (headerActions) {
                headerActions.remove();
            }
            
            // Adjust styles for full display in right panel
            const allElements = tempDiv.querySelectorAll('*');
            allElements.forEach(el => {
                // Remove card-like backgrounds and borders from main content
                if (el.classList.contains && el.classList.contains('prediction-main-card')) {
                    el.style.background = 'transparent';
                    el.style.border = 'none';
                    el.style.boxShadow = 'none';
                    el.style.borderRadius = '0';
                    el.style.padding = '0';
                }
                
                // Ensure header container has correct border and padding
                if (el.style && el.style.borderBottom && el.style.borderBottom.includes('2px')) {
                    el.style.borderBottom = '2px solid #e2e8f0';
                    el.style.paddingBottom = '20px';
                    el.style.marginBottom = '32px';
                }
                
                // Ensure h1 (prediction-topic) has correct size
                if (el.classList && (el.classList.contains('prediction-topic') || (el.tagName === 'H1' && el.classList.contains('prediction-topic')))) {
                    el.style.fontSize = '24px';
                    el.style.fontWeight = '700';
                    el.style.color = '#1e293b';
                    el.style.margin = '0 0 8px 0';
                }
                
                // Ensure h2 section headers have correct style (no uppercase)
                if (el.tagName === 'H2' && el.classList && !el.classList.contains('prediction-topic')) {
                    el.style.fontSize = '16px';
                    el.style.fontWeight = '600';
                    el.style.color = '#374151';
                    el.style.marginBottom = '16px';
                    el.style.paddingBottom = '8px';
                    el.style.textTransform = 'none';
                    el.style.letterSpacing = 'normal';
                    if (!el.style.borderBottom || !el.style.borderBottom.includes('1px solid')) {
                        el.style.borderBottom = '1px solid #e5e7eb';
                    }
                }
                
                // Reduce excessive padding (but keep header padding)
                if (el.style && el.style.padding && (!el.style.borderBottom || !el.style.borderBottom.includes('2px'))) {
                    const padding = el.style.padding;
                    if (padding.includes('32px')) {
                        el.style.padding = padding.replace(/32px/g, '24px');
                    }
                }
            });
            
            resultContent.innerHTML = tempDiv.innerHTML;
            
            // Scroll to top of right panel
            if (mainPanel) {
                mainPanel.scrollTop = 0;
            }
        }
    }
    
    // Toast notification functions
    function showToast(message, type = 'success') {
        // Remove existing toasts with animation
        const existingToasts = document.querySelectorAll('.toast-notification');
        existingToasts.forEach(toast => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        });
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        `;
        
        // Add icon
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
        toast.innerHTML = `
            <i class="bi ${icon}" style="font-size: 20px;"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Trigger animation
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            });
        });
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 4000);
    }

    function showError(message) {
        if (!progressCard) return;
        
        const statusMessages = document.getElementById('statusMessages');
        if (statusMessages) {
            statusMessages.innerHTML = `
                <div style="padding: 12px; background: #fee2e2; border-radius: 6px; border-left: 3px solid #ef4444; color: #991b1b; font-size: 12px;">
                    <strong>Error:</strong> ${message}
                </div>
            `;
        }
        
        // Show error toast notification
        showToast(message, 'error');
    }
    
    function showInstructionsAgain() {
        if (progressCard && resultCard) {
            resultCard.style.display = 'none';
            progressCard.style.display = 'none';
            
            // Show did you know section again
            if (didYouKnowSection) {
                didYouKnowSection.style.display = 'block';
                // Restart typing animation (don't reset index, continue from where it was)
                currentText = '';
                isDeleting = false;
                // Keep currentFactIndex as is, or reset to 0 if you want to start from beginning
                // currentFactIndex = 0; // Uncomment to restart from first fact
                setTimeout(() => {
                    startTypingAnimation();
                }, 500);
            }
            
            // Show animated background again
            const animatedBackground = document.getElementById('animatedBackground');
            if (animatedBackground) {
                animatedBackground.style.display = 'block';
            }
            
            // Remove scrollable class (background stays white)
            const mainPanel = document.querySelector('.cursor-main');
            if (mainPanel) {
                mainPanel.classList.remove('scrollable');
            }
            
            // Restore cursor-main-content styling for centered display
            const mainContent = document.querySelector('.cursor-main-content');
            if (mainContent) {
                mainContent.style.display = 'flex';
                mainContent.style.alignItems = 'center';
                mainContent.style.justifyContent = 'center';
                mainContent.style.padding = '24px';
                mainContent.style.minHeight = '100%';
            }
            
            // Reset form
            form.reset();
            
            // Re-enable submit button
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Generate';
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
            }
            
            // Show form, header, and submit button again
            if (formCard && promptDetailsCard) {
                formCard.style.display = 'block';
                promptDetailsCard.style.display = 'none';
            }
            
            // Hide prompt details action buttons
            const promptDetailsActions = document.getElementById('promptDetailsActions');
            if (promptDetailsActions) {
                promptDetailsActions.style.display = 'none';
            }
            
            // Show Analyze Prediction header, hide Analysis Input Details header
            const sidebarHeader = document.getElementById('sidebarHeader');
            if (sidebarHeader) {
                sidebarHeader.style.display = 'block';
            }
            const promptDetailsHeader = document.getElementById('promptDetailsHeader');
            if (promptDetailsHeader) {
                promptDetailsHeader.style.display = 'none';
            }
            
            // Show floating submit button
            const floatingSubmitContainer = document.getElementById('floatingSubmitContainer');
            if (floatingSubmitContainer) {
                floatingSubmitContainer.style.display = 'block';
            }
            
            // Scroll to top of left panel
            if (mainPanel) {
                mainPanel.scrollTop = 0;
            }
            
            // Clear polling
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }
    }
    
    // Progress bar animation in left panel
    function showAnalysisProgress() {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressStatus = document.getElementById('progressStatus');
        
        if (!progressCard) return;
        
        // Hide animated background
        const animatedBackground = document.getElementById('animatedBackground');
        if (animatedBackground) {
            animatedBackground.style.display = 'none';
        }
        
        // Set main panel background to white
        const mainPanel = document.querySelector('.cursor-main');
        if (mainPanel) {
            mainPanel.style.background = '#ffffff';
        }
        
        // Ensure cursor-main-content is centered for progress display
        const mainContent = document.querySelector('.cursor-main-content');
        if (mainContent) {
            mainContent.style.display = 'flex';
            mainContent.style.alignItems = 'center';
            mainContent.style.justifyContent = 'center';
            mainContent.style.padding = '24px';
            mainContent.style.minHeight = '100%';
            mainContent.style.background = '#ffffff';
        }
        
        // Hide did you know section, show progress
        if (didYouKnowSection) {
            didYouKnowSection.style.display = 'none';
        }
        progressCard.style.display = 'block';
        
        // Status messages sequence
        const statusSequence = [
            { text: 'Processing your request...', progress: 10 },
            { text: 'Reading source URLs...', progress: 25 },
            { text: 'Extracting content from sources...', progress: 40 },
            { text: 'Analyzing data with NUJUM...', progress: 60 },
            { text: 'Generating predictions...', progress: 75 },
            { text: 'Finalizing analysis...', progress: 90 },
            { text: 'Almost done...', progress: 95 }
        ];
        
        let currentStatusIndex = 0;
        let currentProgress = 0;
        
        // Simulate progress (since we don't have real-time updates)
        // Start with initial progress
        let progressUpdateInterval = setInterval(() => {
            if (currentProgress < 95) {
                currentProgress += 0.3;
                progressBar.style.width = currentProgress + '%';
                progressText.textContent = Math.round(currentProgress) + '%';
            }
        }, 500); // Smooth progress bar updates every 500ms
        
        // Update status messages
        const statusInterval = setInterval(() => {
            if (currentStatusIndex < statusSequence.length) {
                const status = statusSequence[currentStatusIndex];
                currentProgress = status.progress;
                
                // Update progress bar to match status
                progressBar.style.width = currentProgress + '%';
                progressText.textContent = currentProgress + '%';
                progressStatus.textContent = status.text;
                
                currentStatusIndex++;
            } else {
                // Slow down near completion
                if (currentProgress < 98) {
                    currentProgress += 0.2;
                    progressBar.style.width = currentProgress + '%';
                    progressText.textContent = Math.round(currentProgress) + '%';
                }
            }
        }, 20000); // Update status every 20 seconds (adjust based on expected processing time)
        
        // Store interval IDs for cleanup if needed
        progressCard.dataset.progressInterval = progressUpdateInterval;
        progressCard.dataset.statusInterval = statusInterval;
    }
    
    console.log('Initialization complete');
    
    // Export modal functions
    let currentExportId = null;
    
    window.confirmExportFromCreate = function() {
        // Get prediction ID from global variable
        const predictionId = window.currentPredictionId;
        
        if (!predictionId) {
            showToast('Prediction ID not available. Please wait for the analysis to complete.', 'error');
            return;
        }
        
        // Get topic from the form
        const topic = document.getElementById('topic')?.value || 'Untitled Prediction';
        
        // Store the prediction ID for export
        currentExportId = predictionId;
        
        const exportTopicElement = document.getElementById('exportTopic');
        if (exportTopicElement) {
            exportTopicElement.textContent = topic.length > 50 ? topic.substring(0, 50) + '...' : topic;
        }
        const exportModal = document.getElementById('exportModal');
        if (exportModal) {
            exportModal.style.display = 'flex';
        }
    };
    
    window.closeExportModal = function() {
        const exportModal = document.getElementById('exportModal');
        if (exportModal) {
            exportModal.style.display = 'none';
        }
        currentExportId = null;
    };
    
    window.exportPrediction = function() {
        // Use currentExportId if available, otherwise try window.currentPredictionId
        const predictionId = currentExportId || window.currentPredictionId;
        
        if (!predictionId || predictionId === 'null' || predictionId === null || predictionId === 'undefined') {
            showToast('Error: Prediction ID not found', 'error');
            closeExportModal();
            return;
        }
        
        // Validate prediction ID is a valid number
        const predictionIdNum = parseInt(predictionId);
        if (isNaN(predictionIdNum) || predictionIdNum <= 0) {
            console.error('Invalid prediction ID for export:', predictionId);
            showToast('Error: Invalid prediction ID', 'error');
            closeExportModal();
            return;
        }
        
        // Store the ID before closing the modal
        const predictionIdToExport = predictionIdNum;
        
        // Close the modal first
        closeExportModal();
        
        // Show loading message
        showToast('Exporting PDF...', 'success');
        
        // Generate absolute URL to handle subdirectory deployments (cPanel)
        const baseUrl = '{{ url("/") }}';
        const exportUrl = `${baseUrl}/predictions/${predictionIdToExport}/export`;
        
        // Redirect to the export route
        // The download will start automatically
        // Show success message after a short delay (optimistic)
        setTimeout(() => {
            showToast('PDF exported successfully!', 'success');
        }, 1000);
        
        window.location.href = exportUrl;
    };
    
    // Set up the confirm export button
    const confirmExportBtn = document.getElementById('confirmExportBtn');
    if (confirmExportBtn) {
        confirmExportBtn.onclick = exportPrediction;
    }
    
    // Close export modal when clicking outside
    const exportModal = document.getElementById('exportModal');
    if (exportModal) {
        exportModal.onclick = function(e) {
        if (e.target === this) {
                closeExportModal();
            }
        };
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const exportModal = document.getElementById('exportModal');
            if (exportModal && exportModal.style.display === 'flex') {
                closeExportModal();
            }
        }
    });
});
</script>

<!-- Export Confirmation Modal -->
<div id="exportModal" class="export-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="export-modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #10b981;">📄</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Export PDF Report</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this prediction analysis as a PDF report?</p>
        <p id="exportTopic" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
        <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">The report will include all analysis details and NUJUM insights.</p>
        
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button onclick="closeExportModal()" 
                    style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer;">
                Cancel
            </button>
            <button id="confirmExportBtn" 
                    style="padding: 12px 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                Export PDF
            </button>
        </div>
    </div>
</div>

<style>
    /* Export Modal Styles */
    .export-modal-overlay {
        display: flex;
    }
    
    .export-modal-content {
        animation: modalFadeIn 0.3s ease-out;
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @media (max-width: 768px) {
        .export-modal-overlay {
            padding: 16px !important;
            align-items: flex-start !important;
            padding-top: 20vh !important;
        }
        
        .export-modal-content {
            padding: 24px 20px !important;
            max-width: 100% !important;
        }
        
        .export-modal-content h3 {
            font-size: 18px !important;
        }
        
        .export-modal-content p {
            font-size: 14px !important;
        }
        
        .export-modal-content button {
            padding: 12px 20px !important;
            font-size: 14px !important;
            min-height: 44px !important;
        }
        
        .export-modal-content div[style*="display: flex; gap: 16px"] {
            flex-direction: column !important;
            gap: 10px !important;
        }
        
        .export-modal-content div[style*="display: flex; gap: 16px"] button {
            width: 100% !important;
        }
    }
</style>

@endsection
