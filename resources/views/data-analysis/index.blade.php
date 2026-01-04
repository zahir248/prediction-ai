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
        overflow-x: hidden;
        position: relative;
        z-index: 1;
        min-height: 0; /* Allows flex child to shrink below content size */
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
    
    .cursor-main-content.scrollable {
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
    }
    
    @keyframes float-particle-6 {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(50px, 120px) scale(1.25); }
        50% { transform: translate(-35px, 240px) scale(0.95); }
        75% { transform: translate(45px, 360px) scale(1.1); }
    }
    
    @keyframes wave-move-1 {
        0%, 100% { transform: translateX(0) translateY(0); }
        50% { transform: translateX(50px) translateY(30px); }
    }
    
    @keyframes wave-move-2 {
        0%, 100% { transform: translateX(0) translateY(0); }
        50% { transform: translateX(-60px) translateY(-40px); }
    }
    
    @keyframes connection-pulse-1 {
        0%, 100% { opacity: 0.2; transform: rotate(25deg) scaleX(1); }
        50% { opacity: 0.6; transform: rotate(25deg) scaleX(1.2); }
    }
    
    @keyframes connection-pulse-2 {
        0%, 100% { opacity: 0.3; transform: rotate(-35deg) scaleX(1); }
        50% { opacity: 0.7; transform: rotate(-35deg) scaleX(1.15); }
    }
    
    @keyframes connection-pulse-3 {
        0%, 100% { opacity: 0.25; transform: rotate(45deg) scaleX(1); }
        50% { opacity: 0.65; transform: rotate(45deg) scaleX(1.3); }
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
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes shine {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .cursor-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
    }
    
    .cursor-sidebar-content {
        flex: 1;
        padding: 16px;
        padding-bottom: 20px; /* Reduced from 100px - only enough space for floating button */
        overflow-x: visible;
        min-height: 0; /* Allows flex child to shrink */
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
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
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
    
    .floating-submit-btn:active {
        transform: translateY(0);
    }
    
    .floating-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .progress-bar-fill {
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }
    
    .progress-bar-shine {
        animation: shine 2s infinite;
    }
    
    @keyframes shine {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    /* File Upload Styles */
    .file-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .file-upload-zone:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }
    
    .file-upload-zone.dragover {
        border-color: #667eea;
        background: #eef2ff;
    }
    
    /* Cursor-style form inputs */
    .cursor-sidebar-content input[type="file"] {
        display: none;
    }
    
    .cursor-sidebar-content label {
        font-size: 12px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        display: block;
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
    
    .cursor-sidebar-content .info-tooltip {
        font-size: 12px;
    }
</style>

<div class="cursor-layout">
    <!-- Left Panel: Upload Form -->
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-header" id="sidebarHeader">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analyze Data</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Upload Excel file to generate analysis</p>
        </div>
        
        <!-- Analysis Input Details Header (hidden by default, shown during analysis) -->
        <div class="cursor-sidebar-header" id="promptDetailsHeader" style="display: none;">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analysis Input Details</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Review the details being analyzed</p>
        </div>
        
        <div class="cursor-sidebar-content">
            @if($errors->any())
                <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12px;">
                    <strong style="display: block; margin-bottom: 8px; font-size: 13px;">❌ Error:</strong>
                    <ul style="margin: 0; padding-left: 16px; line-height: 1.6;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12px;">
                    {{ session('success') }}
                </div>
            @endif

            <form id="uploadForm" action="{{ route('data-analysis.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div id="fileUploadSection" style="margin-bottom: 24px;">
                    <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Raw Data</h2>
                    
                    <label for="excel_file" style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                        Excel File <span style="color: #dc2626;">*</span>
                        <span class="info-tooltip" data-tooltip="Upload an Excel file (.xlsx or .xls) with multiple sheets. NUJUM will analyze all sheets, extract data patterns, and generate comprehensive insights with interactive charts.">
                            <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                        </span>
                    </label>
                    
                    <div class="file-upload-zone" id="dropZone" onclick="document.getElementById('excel_file').click()">
                        <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" required onchange="handleFileSelect(this)">
                        <div id="uploadText" style="margin-bottom: 12px;">
                            <p style="font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Drag and drop your Excel file here</p>
                            <p style="color: #64748b; font-size: 11px; margin: 0;">or click to browse</p>
                        </div>
                        <p id="supportedText" style="color: #64748b; font-size: 10px; margin: 0;">Supported: .xlsx, .xls (Max: 10MB)</p>
                        <div id="fileName" style="margin-top: 10px; font-weight: 600; color: #667eea; font-size: 12px; display: none;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                <span id="fileNameText"></span>
                                <button 
                                    type="button" 
                                    onclick="clearSelectedFile()" 
                                    style="padding: 6px 10px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 4px; font-weight: 500; font-size: 11px; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 4px;"
                                    onmouseover="this.style.background='#e5e7eb'"
                                    onmouseout="this.style.background='#f3f4f6'"
                                >
                                    <i class="bi bi-x-circle" style="font-size: 11px;"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="insightsSection" style="margin-bottom: 24px;">
                    <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Insights Information</h2>
                    
                    <div style="margin-bottom: 12px; padding: 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: #374151;">
                            <input 
                                type="checkbox" 
                                id="use_summary" 
                                name="use_summary" 
                                value="1"
                                onchange="toggleInsightsField()"
                                style="width: 16px; height: 16px; cursor: pointer; accent-color: #667eea;"
                            >
                            <span style="font-weight: 600;">Summary</span>
                            <span class="info-tooltip" data-tooltip="Automatically analyze your Excel data and suggest relevant complete insights. Perfect if you're not sure what insights to generate.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                            </span>
                        </label>
                    </div>
                    
                    <label for="custom_insights" style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                        Insights
                        <span class="info-tooltip" data-tooltip="Specify the types of insights you want to generate. Enter one insight per line. If your insight contains the word 'map', a geographic map visualization will be displayed instead of a chart. For map visualizations, include latitude and longitude data in your Excel file for accurate location pinning.">
                            <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                        </span>
                    </label>
                    
                    <textarea 
                        name="custom_insights" 
                        id="custom_insights" 
                        rows="5"
                        placeholder="Total Certified IBS Products by System
Percentage Distribution by IBS System
Number of Manufacturers by State (Negeri)"
                        style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; transition: border-color 0.15s ease, box-shadow 0.15s ease; background: #ffffff; outline: none;"
                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'; this.style.outline='none';"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'; this.style.outline='none';"
                        oninput="handleInsightsInput(this);"
                    ></textarea>
                </div>
            </form>
            
            <!-- Prompt Details (hidden by default, shown during analysis) -->
            <div id="promptDetailsCard" style="display: none;">
                <div id="promptDetailsContent">
                    <!-- Prompt details will be populated here -->
                </div>
            </div>
        </div>
        
        <!-- Floating Action Buttons for Prompt Details (hidden by default, shown after analysis) -->
        <div class="floating-submit-container" id="promptDetailsActions" style="display: none;">
            <a href="{{ route('data-analysis.index') }}" class="floating-submit-btn" style="text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                New Analysis
            </a>
        </div>
        
        <!-- Floating Submit Button -->
        <div class="floating-submit-container" id="floatingSubmitContainer">
            <button type="submit" form="uploadForm" class="floating-submit-btn" id="submitBtn" onclick="return checkBeforeSubmit(event);">
                <span id="submitText">Generate</span>
                <span id="submitLoader" style="display: none;">⏳ Processing...</span>
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
            <!-- Analysis Progress Card (hidden by default, shown during analysis) -->
            <div id="progressCard" style="display: none; background: transparent; padding: 24px; max-width: 500px; width: 100%; margin: 0 auto;">
                <!-- Title -->
                <h2 style="color: #1e293b; margin-bottom: 8px; font-size: 18px; font-weight: 700; text-align: center; border: none; border-bottom: none; text-decoration: none;">NUJUM Analysis in Progress</h2>
                
                <!-- Description -->
                <p style="color: #64748b; margin-bottom: 20px; font-size: 13px; line-height: 1.5; text-align: center; border: none; border-top: none;">
                    Analyzing your Excel data. This may take 1-3 minutes.
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
            
            <!-- Result Display (hidden by default, shown when analysis completes) -->
            <div id="resultCard" style="display: none; padding: 0; width: 100%; max-width: 100%;">
                <div id="resultContent" style="width: 100%; max-width: 100%;">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Base path for the application (includes subdirectory if deployed in one)
    const APP_BASE_PATH = '{{ url("/") }}';
    
    // Initialize tooltips when page loads
    document.addEventListener('DOMContentLoaded', function() {
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
        
        // Close tooltips when clicking outside (mobile)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!e.target.closest('.info-tooltip')) {
                    tooltips.forEach(t => {
                        t.classList.remove('active');
                        hideTooltip();
                    });
                }
            }
        });
    });
    
    // Check form validity before submit (show validation messages)
    function checkBeforeSubmit(event) {
        if (event) {
            event.preventDefault();
        }
        
        const fileInput = document.getElementById('excel_file');
        const insightsInput = document.getElementById('custom_insights');
        
        // Clear previous validation styling
        if (fileInput) {
            fileInput.style.borderColor = '';
            const dropZone = document.getElementById('dropZone');
            if (dropZone) {
                dropZone.style.borderColor = '';
                dropZone.style.background = '';
            }
        }
        if (insightsInput) {
            insightsInput.style.borderColor = '';
        }
        
        // Check if Summary checkbox is checked
        const useSummary = document.getElementById('use_summary') && document.getElementById('use_summary').checked;
        const summaryCheckbox = document.getElementById('use_summary');
        const summaryContainer = summaryCheckbox ? summaryCheckbox.closest('div') : null;
        
        let isValid = true;
        
        // Clear previous validation styling
        if (summaryContainer) {
            summaryContainer.style.borderColor = '';
            summaryContainer.style.background = '';
        }
        
        // Validate file
        const hasFile = fileInput && fileInput.files && fileInput.files[0];
        if (!hasFile) {
            isValid = false;
            if (fileInput) {
                // Create a temporary validation message
                const originalTitle = fileInput.title;
                fileInput.title = 'Please upload an Excel file.';
                fileInput.setCustomValidity('Please upload an Excel file.');
                
                // Try to show validation - for file inputs, we'll show visual feedback instead
                const dropZone = document.getElementById('dropZone');
                if (dropZone) {
                    dropZone.style.borderColor = '#dc2626';
                    dropZone.style.background = '#fef2f2';
                    setTimeout(() => {
                        dropZone.style.borderColor = '';
                        dropZone.style.background = '';
                    }, 3000);
                }
                
                // File inputs don't show validation messages well, so we use visual feedback only
                fileInput.setCustomValidity('');
                fileInput.title = originalTitle;
            }
        }
        
        // Validate insights (only if Summary is not checked)
        if (!useSummary) {
            const hasInsights = insightsInput && insightsInput.value.trim().length >= 3;
            if (!hasInsights) {
                isValid = false;
                if (insightsInput) {
                    insightsInput.style.borderColor = '#ef4444';
                    insightsInput.setCustomValidity('Please enter at least one insight (minimum 3 characters).');
                    insightsInput.reportValidity();
                }
                // Also show validation on Summary checkbox container
                if (summaryContainer) {
                    summaryContainer.style.borderColor = '#dc2626';
                    summaryContainer.style.background = '#fef2f2';
                    setTimeout(() => {
                        summaryContainer.style.borderColor = '';
                        summaryContainer.style.background = '';
                    }, 3000);
                }
            } else {
                if (insightsInput) {
                    insightsInput.setCustomValidity('');
                }
            }
        } else {
            // Clear any validation errors when Summary is checked
            if (insightsInput) {
                insightsInput.setCustomValidity('');
                insightsInput.style.borderColor = '';
            }
        }
        
        // Old validation code (removed)
        const oldHasInsights = false;
        if (oldHasInsights) {
            if (insightsInput) {
                insightsInput.setCustomValidity('');
                
                // Add visual feedback
                insightsInput.style.borderColor = '#dc2626';
                insightsInput.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.1)';
                setTimeout(() => {
                    insightsInput.style.borderColor = '';
                    insightsInput.style.boxShadow = '';
                }, 3000);
            }
        }
        
        // If valid, trigger form submission
        if (isValid) {
            document.getElementById('uploadForm').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
        
        return false; // Always return false to prevent default button behavior
    }
    
    // File selection handler
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileNameText = document.getElementById('fileNameText');
            if (fileNameText) {
                fileNameText.textContent = 'Selected: ' + fileName;
            }
            document.getElementById('fileName').style.display = 'block';
            
            // Hide drag-and-drop text when file is selected
            const uploadText = document.getElementById('uploadText');
            const supportedText = document.getElementById('supportedText');
            if (uploadText) {
                uploadText.style.display = 'none';
            }
            if (supportedText) {
                supportedText.style.display = 'none';
            }
            
            // Clear validation styling
            const dropZone = document.getElementById('dropZone');
            if (dropZone) {
                dropZone.style.borderColor = '';
                dropZone.style.background = '';
            }
        } else {
            clearSelectedFile();
        }
    }
    
    // Clear selected file
    function clearSelectedFile() {
        const fileInput = document.getElementById('excel_file');
        const fileName = document.getElementById('fileName');
        const fileNameText = document.getElementById('fileNameText');
        
        // Clear file input
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Hide file name display
        if (fileName) {
            fileName.style.display = 'none';
        }
        if (fileNameText) {
            fileNameText.textContent = '';
        }
        
        // Show drag-and-drop text when no file is selected
        const uploadText = document.getElementById('uploadText');
        const supportedText = document.getElementById('supportedText');
        if (uploadText) {
            uploadText.style.display = 'block';
        }
        if (supportedText) {
            supportedText.style.display = 'block';
        }
        
        // Clear validation styling
        const dropZone = document.getElementById('dropZone');
        if (dropZone) {
            dropZone.style.borderColor = '';
            dropZone.style.background = '';
        }
    }

    // Drag and drop functionality
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('excel_file');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('dragover');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(fileInput);
        }
    }, false);

    // Form submission with AJAX (only called when validation passes)
    document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        
        // Show loading state
        submitText.style.display = 'none';
        submitLoader.style.display = 'inline';
        submitBtn.disabled = true; // Disable during processing to prevent double submission
        
        // Show progress in right panel
        showAnalysisProgress();
        
        // Create FormData
        const formData = new FormData(this);
        
        try {
            const response = await fetch('{{ route("data-analysis.upload") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            // Check if response is ok
            if (!response.ok) {
                // Try to get error message from response
                let errorMessage = 'Analysis failed. Please try again.';
                try {
                    const errorResult = await response.json();
                    errorMessage = errorResult.error || errorMessage;
                } catch (e) {
                    // If response is not JSON, use status text
                    errorMessage = response.statusText || errorMessage;
                }
                displayAnalysisError(errorMessage);
                return;
            }
            
            const result = await response.json();
            
            if (result.success && result.analysis_id) {
                // Update progress to 100%
                updateProgress(100, 'Analysis complete!');
                
                // Wait a moment, then fetch the analysis HTML
                setTimeout(async () => {
                    try {
                        const analysisHtmlUrl = `${APP_BASE_PATH}/data-analysis/${result.analysis_id}/analysis-html`;
                        const htmlResponse = await fetch(analysisHtmlUrl, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (!htmlResponse.ok) {
                            throw new Error(`HTTP error! status: ${htmlResponse.status}`);
                        }
                        
                        const htmlResult = await htmlResponse.json();
                        
                        if (htmlResult.success && htmlResult.html) {
                            displayAnalysisResult(htmlResult.html);
                        } else {
                            displayAnalysisError(htmlResult.error || 'Failed to load analysis results');
                        }
                    } catch (error) {
                        console.error('Error fetching analysis HTML:', error);
                        displayAnalysisError('Analysis completed but failed to load results. Please refresh the page.');
                    }
                }, 1000);
            } else {
                // Handle API errors
                const errorMessage = result.error || 'Analysis failed';
                displayAnalysisError(errorMessage);
            }
        } catch (error) {
            console.error('Upload error:', error);
            
            // Check if it's a network error
            if (error instanceof TypeError && error.message.includes('fetch')) {
                displayAnalysisError('Network error. Please check your connection and try again.');
            } else {
                // Try to parse error response if available
                if (error.response) {
                    error.response.json().then(result => {
                        displayAnalysisError(result.error || 'An error occurred during upload. Please try again.');
                    }).catch(() => {
                        displayAnalysisError('An error occurred during upload. Please try again.');
                    });
                } else {
                    displayAnalysisError('An error occurred during upload. Please try again.');
                }
            }
        } finally {
            // Re-enable submit button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitText.style.display = 'inline';
                submitLoader.style.display = 'none';
            }
        }
    });
    
    // Function to show analysis progress
    function showAnalysisProgress() {
        const progressCard = document.getElementById('progressCard');
        const didYouKnowSection = document.getElementById('didYouKnowSection');
        const resultCard = document.getElementById('resultCard');
        const animatedBackground = document.getElementById('animatedBackground');
        const mainPanel = document.querySelector('.cursor-main');
        const mainContent = document.querySelector('.cursor-main-content');
        
        // Update left panel: Hide form, show input details
        const sidebarHeader = document.getElementById('sidebarHeader');
        const promptDetailsHeader = document.getElementById('promptDetailsHeader');
        const fileUploadSection = document.getElementById('fileUploadSection');
        const insightsSection = document.getElementById('insightsSection');
        const promptDetailsCard = document.getElementById('promptDetailsCard');
        const promptDetailsContent = document.getElementById('promptDetailsContent');
        const floatingSubmitContainer = document.getElementById('floatingSubmitContainer');
        const fileName = document.getElementById('fileName');
        const fileInput = document.getElementById('excel_file');
        
        // Hide main header, show analysis input details header
        if (sidebarHeader) sidebarHeader.style.display = 'none';
        if (promptDetailsHeader) promptDetailsHeader.style.display = 'block';
        
        // Hide form sections (file upload and insights input), show prompt details
        if (fileUploadSection) fileUploadSection.style.display = 'none';
        if (insightsSection) insightsSection.style.display = 'none';
        if (promptDetailsCard) {
            promptDetailsCard.style.display = 'block';
            
            // Get file name and display as text
            const selectedFileName = fileInput && fileInput.files && fileInput.files[0] 
                ? fileInput.files[0].name 
                : (fileName ? fileName.textContent.replace('Selected: ', '') : 'Excel file');
            
            // Get custom insights
            const customInsightsInput = document.getElementById('custom_insights');
            const customInsights = customInsightsInput ? customInsightsInput.value.trim() : '';
            
            // Create prompt details content
            if (promptDetailsContent) {
                let insightsHtml = '';
                if (customInsights) {
                    const insightsList = customInsights.split('\n').filter(line => line.trim());
                    if (insightsList.length > 0) {
                        insightsHtml = `
                            <div style="margin-bottom: 24px;">
                                <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Insights Information</h2>
                                <div style="font-size: 13px; color: #111827; line-height: 1.6;">
                                    ${insightsList.map(insight => `<div style="margin-bottom: 6px; color: #64748b;">• ${insight}</div>`).join('')}
                                </div>
                            </div>
                        `;
                    }
                }
                
                promptDetailsContent.innerHTML = `
                    <div style="margin-bottom: 24px;">
                        <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">File Information</h2>
                        <div style="font-size: 13px; color: #111827; font-weight: 500; line-height: 1.5;">
                            <div style="margin-bottom: 4px;"><strong>File Name:</strong></div>
                            <div style="color: #64748b; word-break: break-word;">${selectedFileName}</div>
                        </div>
                    </div>
                    ${insightsHtml}
                `;
            }
        }
        
        // Hide Generate button
        if (floatingSubmitContainer) floatingSubmitContainer.style.display = 'none';
        
        // Hide animated background
        if (animatedBackground) {
            animatedBackground.style.display = 'none';
        }
        
        // Set main panel background to white
        if (mainPanel) {
            mainPanel.style.background = '#ffffff';
            mainPanel.classList.remove('animated-ai-background');
        }
        
        // Ensure cursor-main-content is centered for progress display
        if (mainContent) {
            mainContent.style.display = 'flex';
            mainContent.style.alignItems = 'center';
            mainContent.style.justifyContent = 'center';
            mainContent.style.padding = '24px';
            mainContent.style.minHeight = '100%';
            mainContent.style.background = '#ffffff';
        }
        
        // Hide did you know section and result card, show progress
        if (didYouKnowSection) didYouKnowSection.style.display = 'none';
        if (resultCard) resultCard.style.display = 'none';
        if (progressCard) progressCard.style.display = 'block';
        
        // Start progress animation
        startProgressAnimation();
    }
    
    // Start progress animation
    function startProgressAnimation() {
        const statusSequence = [
            { text: 'Processing your request...', progress: 10 },
            { text: 'Reading Excel file...', progress: 25 },
            { text: 'Extracting data from sheets...', progress: 40 },
            { text: 'Analyzing with NUJUM AI...', progress: 60 },
            { text: 'Generating insights...', progress: 75 },
            { text: 'Creating visualizations...', progress: 85 },
            { text: 'Finalizing results...', progress: 95 }
        ];
        
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressStatus = document.getElementById('progressStatus');
        
        if (!progressBar || !progressText || !progressStatus) return;
        
        let currentStatusIndex = 0;
        let currentProgress = 0;
        
        // Simulate progress
        const progressUpdateInterval = setInterval(() => {
            if (currentProgress < 95) {
                currentProgress += 0.3;
                progressBar.style.width = currentProgress + '%';
                progressText.textContent = Math.round(currentProgress) + '%';
            }
        }, 500);
        
        // Update status messages
        const statusInterval = setInterval(() => {
            if (currentStatusIndex < statusSequence.length) {
                const status = statusSequence[currentStatusIndex];
                progressStatus.textContent = status.text;
                currentStatusIndex++;
            }
        }, 3000);
        
        // Store intervals for cleanup
        const progressCard = document.getElementById('progressCard');
        if (progressCard) {
            progressCard.dataset.progressInterval = progressUpdateInterval;
            progressCard.dataset.statusInterval = statusInterval;
        }
    }
    
    // Update progress
    function updateProgress(percent, status) {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressStatus = document.getElementById('progressStatus');
        
        if (progressBar) progressBar.style.width = percent + '%';
        if (progressText) progressText.textContent = Math.round(percent) + '%';
        if (progressStatus) progressStatus.textContent = status || 'Processing...';
        
        // Clear intervals
        const progressCard = document.getElementById('progressCard');
        if (progressCard) {
            if (progressCard.dataset.progressInterval) {
                clearInterval(parseInt(progressCard.dataset.progressInterval));
            }
            if (progressCard.dataset.statusInterval) {
                clearInterval(parseInt(progressCard.dataset.statusInterval));
            }
        }
    }
    
    // Display analysis result
    function displayAnalysisResult(html) {
        const progressCard = document.getElementById('progressCard');
        const didYouKnowSection = document.getElementById('didYouKnowSection');
        const resultCard = document.getElementById('resultCard');
        const resultContent = document.getElementById('resultContent');
        const mainContent = document.querySelector('.cursor-main-content');
        const promptDetailsActions = document.getElementById('promptDetailsActions');
        
        // Check if this is a fallback/error response
        const isFallbackResponse = html.includes('Analysis Failed') && 
                                   (html.includes('Fallback Response') || 
                                    html.includes('Due to technical difficulties') ||
                                    html.includes('comprehensive analysis could not be generated'));
        
        // Set global flag to indicate analysis failed
        window.analysisFailed = isFallbackResponse;
        
        if (isFallbackResponse) {
            // Hide progress card
            if (progressCard) progressCard.style.display = 'none';
            if (didYouKnowSection) didYouKnowSection.style.display = 'none';
            
            // Center the right panel for error display
            if (mainContent) {
                mainContent.style.display = 'flex';
                mainContent.style.alignItems = 'center';
                mainContent.style.justifyContent = 'center';
                mainContent.style.padding = '24px';
                mainContent.style.background = '#ffffff';
                mainContent.classList.remove('scrollable');
            }
            
            // Make main panel not scrollable for error
            const mainPanel = document.querySelector('.cursor-main');
            if (mainPanel) {
                mainPanel.classList.remove('scrollable');
            }
            
            // Hide prompt details action buttons for error
            if (promptDetailsActions) {
                promptDetailsActions.style.display = 'none';
            }
            
            // Hide animated background
            const animatedBackground = document.getElementById('animatedBackground');
            if (animatedBackground) {
                animatedBackground.style.display = 'none';
            }
            
            // Display simple error message directly in mainContent
            if (mainContent) {
                mainContent.innerHTML = `
                    <div style="max-width: 500px; width: 100%; text-align: center; padding: 40px 24px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Service Unavailable</h2>
                        <p style="font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">The service is not available now. Please try again.</p>
                        <p style="font-size: 14px; color: #94a3b8;">We are experiencing technical difficulties. Please try again later.</p>
                    </div>
                `;
            }
            
            return; // Exit early for fallback responses
        }
        
        // Hide progress and did you know
        if (progressCard) progressCard.style.display = 'none';
        if (didYouKnowSection) didYouKnowSection.style.display = 'none';
        
        // Show result card
        if (resultCard) {
            resultCard.style.display = 'block';
        }
        
        // Show New Analysis button in left panel
        if (promptDetailsActions) {
            promptDetailsActions.style.display = 'block';
        }
        
        // Update main content to allow scrolling
        if (mainContent) {
            mainContent.style.display = 'block';
            mainContent.style.alignItems = 'flex-start';
            mainContent.style.justifyContent = 'flex-start';
            mainContent.style.padding = '24px';
        }
        
        // Set result content
        if (resultContent) {
            resultContent.innerHTML = html;
            
            // Load Chart.js if not already loaded and initialize charts
            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                script.onload = function() {
                    initializeChartsFromHTML(html);
                };
                document.head.appendChild(script);
            } else {
                // Chart.js already loaded, initialize charts
                setTimeout(() => {
                    initializeChartsFromHTML(html);
                }, 100);
            }
        }
        
        // Make main panel scrollable
        const mainPanel = document.querySelector('.cursor-main');
        if (mainPanel) {
            mainPanel.classList.add('scrollable');
        }
    }
    
    // Initialize charts from HTML content
    function initializeChartsFromHTML(html) {
        // The charts are already initialized in the partial view's script tag
        // This function is here in case we need to reinitialize
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const scripts = tempDiv.querySelectorAll('script');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            if (script.src) {
                newScript.src = script.src;
            } else {
                newScript.textContent = script.textContent;
            }
            document.body.appendChild(newScript);
        });
    }
    
    // Display analysis error
    function displayAnalysisError(errorMessage) {
        const progressCard = document.getElementById('progressCard');
        const didYouKnowSection = document.getElementById('didYouKnowSection');
        const resultCard = document.getElementById('resultCard');
        const resultContent = document.getElementById('resultContent');
        const mainContent = document.querySelector('.cursor-main-content');
        const promptDetailsActions = document.getElementById('promptDetailsActions');
        const animatedBackground = document.getElementById('animatedBackground');
        
        // Hide progress
        if (progressCard) progressCard.style.display = 'none';
        if (didYouKnowSection) didYouKnowSection.style.display = 'none';
        
        // Center the right panel for error display
        if (mainContent) {
            mainContent.style.display = 'flex';
            mainContent.style.alignItems = 'center';
            mainContent.style.justifyContent = 'center';
            mainContent.style.padding = '24px';
            mainContent.style.background = '#ffffff';
            mainContent.classList.remove('scrollable');
        }
        
        // Make main panel not scrollable for error
        const mainPanel = document.querySelector('.cursor-main');
        if (mainPanel) {
            mainPanel.classList.remove('scrollable');
        }
        
        // Hide prompt details action buttons for error
        if (promptDetailsActions) {
            promptDetailsActions.style.display = 'none';
        }
        
        // Hide animated background
        if (animatedBackground) {
            animatedBackground.style.display = 'none';
        }
        
        // Set main panel background to white
        if (mainPanel) {
            mainPanel.style.background = '#ffffff';
            mainPanel.classList.remove('animated-ai-background');
        }
        
        // Display error message
        if (mainContent) {
            // Check if error is about service unavailability or any AI API failure
            // All AI API-related errors should show the same "Service Unavailable" message
            const isServiceUnavailable = errorMessage.includes('technical difficulties') || 
                                       errorMessage.includes('not available') ||
                                       errorMessage.includes('Service Unavailable') ||
                                       errorMessage.includes('Analysis failed');
            
            if (isServiceUnavailable) {
                // Display simple error message directly in mainContent (matching prediction page style)
                mainContent.innerHTML = `
                    <div style="max-width: 500px; width: 100%; text-align: center; padding: 40px 24px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Service Unavailable</h2>
                        <p style="font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">The service is not available now. Please try again.</p>
                        <p style="font-size: 14px; color: #94a3b8;">We are experiencing technical difficulties. Please try again later.</p>
                    </div>
                `;
                
                // Show toast notification immediately
                const toastMessage = 'The service is not available now. Please try again.';
                if (typeof showToast === 'function') {
                    showToast(toastMessage, 'error');
                }
                
                // Show second toast message after first toast (with delay to allow first toast to be visible)
                // Use global flag to prevent duplicate second toast
                if (!window.analysisSecondToastShown) {
                    window.analysisSecondToastShown = true; // Set flag immediately to prevent duplicates
                    setTimeout(() => {
                        // Check flag again inside setTimeout to prevent race conditions
                        if (window.analysisSecondToastShown) {
                            // Remove any existing toast first to allow second toast to show
                            const existingToast = document.querySelector('.toast-notification');
                            if (existingToast) {
                                existingToast.style.opacity = '0';
                                existingToast.style.transform = 'translateX(100%)';
                                setTimeout(() => {
                                    if (existingToast.parentNode) {
                                        existingToast.parentNode.removeChild(existingToast);
                                    }
                                }, 300);
                            }
                            // Reset the global toast flag to allow second toast
                            if (typeof window.isToastShowing !== 'undefined') {
                                window.isToastShowing = false;
                            }
                            
                            // Wait a bit for the first toast to be removed, then show second toast
                            setTimeout(() => {
                                if (typeof showToast === 'function') {
                                    showToast('Page will redirect in 5 seconds...', 'error');
                                    
                                    // After second toast appears, wait 5 seconds then refresh page
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 5000); // 5 seconds after second toast appears
                                }
                            }, 350); // Wait for toast removal animation to complete
                        }
                    }, 1500); // 1.5 seconds delay to let first toast be visible first
                }
            } else {
                // Other errors - show simple error message
                mainContent.innerHTML = `
                    <div style="max-width: 500px; width: 100%; text-align: center; padding: 40px 24px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">❌</div>
                        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Analysis Failed</h2>
                        <p style="font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">${errorMessage}</p>
                        <button onclick="location.reload()" style="margin-top: 16px; padding: 10px 24px; background: #667eea; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                            Try Again
                        </button>
                    </div>
                `;
            }
        }
    }

    // Did You Know Facts for Data Analysis
    const facts = [
        "Data analysis can reveal hidden patterns in your Excel sheets that might not be immediately visible. NUJUM's AI examines relationships between columns, identifies trends, and suggests optimal visualizations.",
        "Excel files with multiple sheets are processed comprehensively. Each sheet is analyzed independently, and cross-sheet relationships are identified when present.",
        "NUJUM automatically determines the best chart types for your data. Bar charts for comparisons, line charts for trends, and pie charts for distributions are selected based on data characteristics.",
        "AI-powered data analysis goes beyond basic statistics. NUJUM identifies anomalies, predicts trends, and provides actionable recommendations based on your data patterns.",
        "The analysis includes statistical summaries, key findings, trends identification, and strategic recommendations tailored to your specific dataset."
    ];

    // Typing animation
    let currentFactIndex = 0;
    let currentCharIndex = 0;
    let isDeleting = false;
    let typingSpeed = 50;
    let deletingSpeed = 30;
    let pauseTime = 2000;

    function typeFact() {
        const typingText = document.getElementById('typingText');
        const currentFact = facts[currentFactIndex];
        
        if (isDeleting) {
            typingText.textContent = currentFact.substring(0, currentCharIndex - 1);
            currentCharIndex--;
            typingSpeed = deletingSpeed;
        } else {
            typingText.textContent = currentFact.substring(0, currentCharIndex + 1);
            currentCharIndex++;
            typingSpeed = 50;
        }
        
        if (!isDeleting && currentCharIndex === currentFact.length) {
            typingSpeed = pauseTime;
            isDeleting = true;
        } else if (isDeleting && currentCharIndex === 0) {
            isDeleting = false;
            currentFactIndex = (currentFactIndex + 1) % facts.length;
        }
        
        setTimeout(typeFact, typingSpeed);
    }

    // Start typing animation when page loads
    if (document.getElementById('typingText')) {
        typeFact();
    }

    // Handle insights input - disable Summary checkbox when user types
    function handleInsightsInput(input) {
        const useSummary = document.getElementById('use_summary');
        
        // Clear validation styling
        input.style.borderColor = '';
        
        // If user enters text, disable Summary checkbox and uncheck it
        if (input.value.trim().length > 0) {
            if (useSummary) {
                useSummary.checked = false;
                useSummary.disabled = true;
                useSummary.style.cursor = 'not-allowed';
                useSummary.style.opacity = '0.5';
                // Also update the insights field state
                toggleInsightsField();
            }
        } else {
            // If input is cleared, enable Summary checkbox
            if (useSummary) {
                useSummary.disabled = false;
                useSummary.style.cursor = 'pointer';
                useSummary.style.opacity = '1';
            }
        }
    }
    
    // Toggle insights field based on Summary checkbox
    function toggleInsightsField() {
        const useSummary = document.getElementById('use_summary');
        const insightsInput = document.getElementById('custom_insights');
        
        if (useSummary && insightsInput) {
            if (useSummary.checked) {
                // Summary is checked - make insights optional
                insightsInput.removeAttribute('required');
                insightsInput.disabled = true;
                insightsInput.style.background = '#f3f4f6';
                insightsInput.style.cursor = 'not-allowed';
                insightsInput.placeholder = 'Insights will be automatically generated based on your Excel data...';
                insightsInput.value = ''; // Clear any existing value
            } else {
                // Summary is unchecked - make insights required
                insightsInput.setAttribute('required', 'required');
                insightsInput.disabled = false;
                insightsInput.style.background = '#ffffff';
                insightsInput.style.cursor = 'text';
                insightsInput.placeholder = 'Total Certified IBS Products by System\nPercentage Distribution by IBS System\nNumber of Manufacturers by State (Negeri)';
            }
        }
    }
    
    // Initialize on page load
    if (document.getElementById('use_summary')) {
        toggleInsightsField();
    }

    // Toast notification function
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
            line-height: 1.5;
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        `;
        
        // Add icon (using text icons for compatibility)
        const icon = type === 'success' ? '✓' : '⚠';
        toast.innerHTML = `
            <span style="font-size: 18px; font-weight: bold;">${icon}</span>
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
        
        // Auto remove after 4 seconds (unless it's the redirect message)
        if (!message.includes('redirect')) {
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
    }
</script>

@endsection
