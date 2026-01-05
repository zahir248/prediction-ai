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
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
</style>

<div class="cursor-layout">
    <!-- Left Panel: Form -->
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-header" id="sidebarHeader">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analyze Profile</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Fill in the form to generate analysis</p>
            </div>
        
        <!-- Analysis Input Details Header (hidden by default, shown during analysis) -->
        <div class="cursor-sidebar-header" id="promptDetailsHeader" style="display: none;">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Analysis Input Details</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Review the details being analyzed</p>
            </div>

        <div class="cursor-sidebar-content">
            <!-- Error Messages -->
            @if($errors->any() || session('analysis_error'))
                <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12px;">
                    <strong style="display: block; margin-bottom: 8px; font-size: 13px;">❌ Error:</strong>
                    @if(session('analysis_error'))
                        @php $error = session('analysis_error'); @endphp
                        <p style="margin: 0; line-height: 1.5; font-weight: 500;">{{ $error['error'] ?? 'Unknown error' }}</p>
                    @else
                        <ul style="margin: 0; padding-left: 16px; line-height: 1.6;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <!-- JavaScript Base Path Configuration --> 
            <script>
                // Base path for the application (includes subdirectory if deployed in one)
                const APP_BASE_PATH = '{{ url("/") }}';
            </script>

            <!-- Search Form -->
            <div id="searchSection">
                <form id="searchForm">
                    @csrf
                    
                    <div style="margin-bottom: 24px;">
                        <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Profile Information</h2>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="username" style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                                Username <span style="color: #dc2626;">*</span>
                                <span class="info-tooltip" data-tooltip="Enter the social media username you want to analyze. This should be the handle without the @ symbol (e.g., 'lensa3d' not '@lensa3d'). NUJUM will search for this username across the selected platforms and gather profile data, posts, and engagement metrics for comprehensive analysis.">
                                    <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                                </span>
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="{{ old('username') }}"
                                placeholder="lensa3d"
                            required
                                style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; transition: border-color 0.15s ease, box-shadow 0.15s ease; background: #ffffff;"
                            onkeyup="checkExistingData(this.value)"
                        >
                    </div>

                    <!-- Existing Data Notification -->
                        <div id="existingDataNotification" style="display: none; margin-bottom: 16px; padding: 12px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; font-size: 12px;">
                                <div style="font-weight: 600; color: #166534; margin-bottom: 4px;">Previous Search Found</div>
                            <div style="color: #166534; margin-bottom: 4px;">We found previous search data for this username. You can use it to skip searching.</div>
                            <div class="date-info" style="color: #059669; font-size: 11px; font-style: italic; margin-bottom: 12px;"></div>
                            <div style="display: flex; gap: 8px;">
                                <button 
                                    type="button" 
                                    onclick="useExistingData()"
                                    style="flex: 1; padding: 6px 12px; background: #10b981; color: white; border: none; border-radius: 4px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#059669';"
                                    onmouseout="this.style.background='#10b981';"
                                >
                                    Use Previous Data
                                </button>
                                <button 
                                    type="button" 
                                    onclick="searchAgain()"
                                    style="flex: 1; padding: 6px 12px; background: transparent; color: #166534; border: 1px solid #86efac; border-radius: 4px; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#dcfce7';"
                                    onmouseout="this.style.background='transparent';"
                                >
                                    Search Again
                                </button>
                            </div>
                    </div>

                    <!-- Platform Selection -->
                    <div id="platformSelectionSection" style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                            Platforms <span style="color: #dc2626;">*</span>
                            <span class="info-tooltip" data-tooltip="Select one or more social media platforms to analyze. NUJUM will search for the username on each selected platform and gather profile data, posts, engagement metrics, and other relevant information for comprehensive personality and communication analysis.">
                                <i class="bi bi-info-circle" style="color: #3b82f6; cursor: help; font-size: 14px;"></i>
                            </span>
                        </label>
                        <div id="inlinePlatformSelection" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <!-- Platforms will be populated by JavaScript -->
                        </div>
                        <input type="text" id="platforms-required" name="platforms-required" required style="position: absolute; opacity: 0; pointer-events: none; height: 0; width: 0; border: none; padding: 0; margin: 0;" tabindex="-1">
                    </div>

                    <!-- Platform Status -->
                    <div id="platformStatus" style="display: none; margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 12px;">
                            Platforms
                        </label>
                        <div id="platformStatusGrid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <!-- Platform status items will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Analysis Type Selection -->
                    <div id="analysisTypeSection" style="margin-bottom: 24px;">
                        <h2 style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb;">Analysis Type</h2>
                        <div style="display: flex; gap: 8px; margin-bottom: 20px;">
                            <div style="flex: 1; padding: 12px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 6px; cursor: pointer; transition: all 0.3s ease;" 
                                 onclick="selectAnalysisTypeInline('professional')" 
                                 id="inlineAnalysisTypeProfessional"
                                 onmouseover="if(window.selectedAnalysisTypeInModal !== 'professional') this.style.borderColor='#9ca3af';" 
                                 onmouseout="if(window.selectedAnalysisTypeInModal !== 'professional') this.style.borderColor='#e2e8f0';">
                                <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                                    <input type="radio" name="inlineAnalysisType" id="inlineAnalysisType_professional" value="professional" onchange="selectAnalysisTypeInline('professional')" 
                                           style="width: 18px; height: 18px; margin-right: 8px; cursor: pointer; accent-color: #667eea;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 2px;">Professional</div>
                                        <div style="color: #64748b; font-size: 11px;">Career, work ethic</div>
                                    </div>
                                </label>
                            </div>
                            <div style="flex: 1; padding: 12px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 6px; cursor: pointer; transition: all 0.3s ease;" 
                                 onclick="selectAnalysisTypeInline('political')" 
                                 id="inlineAnalysisTypePolitical"
                                 onmouseover="if(window.selectedAnalysisTypeInModal !== 'political') this.style.borderColor='#9ca3af';" 
                                 onmouseout="if(window.selectedAnalysisTypeInModal !== 'political') this.style.borderColor='#e2e8f0';">
                                <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                                    <input type="radio" name="inlineAnalysisType" id="inlineAnalysisType_political" value="political" onchange="selectAnalysisTypeInline('political')" 
                                           style="width: 18px; height: 18px; margin-right: 8px; cursor: pointer; accent-color: #667eea;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 2px;">Political</div>
                                        <div style="color: #64748b; font-size: 11px;">Alignment, engagement</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <input type="text" id="analysis-type-required" name="analysis-type-required" required style="position: absolute; opacity: 0; pointer-events: none; height: 0; width: 0; border: none; padding: 0; margin: 0;" tabindex="-1">
                        </div>
            </div>

            <!-- Results Container -->
            <div id="resultsContainer" style="display: none;"></div>
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
                <a href="#" id="exportBtn" onclick="event.preventDefault(); confirmExportFromSocialMedia(); return false;" class="floating-submit-btn" style="flex: 1; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    Export
                </a>
                <a href="{{ route('social-media.index') }}" class="floating-submit-btn" style="flex: 1; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    New Analysis
                </a>
    </div>
</div>

        <!-- Floating Submit Button -->
        <div class="floating-submit-container" id="floatingSubmitContainer">
            <button 
                type="submit"
                form="searchForm"
                id="searchButton"
                onclick="return checkBeforeSearch(event);"
                class="floating-submit-btn"
                @guest disabled @endguest
            >
                @auth
                    Generate
                @else
                    Login to Generate
                @endauth
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
        </div>
    </div>
</div>

<!-- Username Validation Modal -->
<div id="usernameValidationModal" onclick="if(event.target === this) closeUsernameValidationModal();" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10001; align-items: center; justify-content: center;">
    <div onclick="event.stopPropagation();" style="background: white; border-radius: 12px; padding: 32px; max-width: 450px; width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #f59e0b;">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
            </div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Username Required</h2>
            <p style="color: #64748b; font-size: 14px; line-height: 1.6;">Please enter a username before searching.</p>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button 
                onclick="closeUsernameValidationModal()" 
                style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                onmouseover="this.style.opacity='0.9'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)';"
                onmouseout="this.style.opacity='1'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)';">
                OK
            </button>
        </div>
    </div>
</div>

<style>
    /* Info Tooltip Styles - Matching Create Predictions Page */
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
    
    .cursor-sidebar-content .info-tooltip {
        font-size: 12px;
    }
    
    /* Cursor-style form inputs - Matching Create Predictions Page */
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
    
    .floating-submit-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }
    
    .floating-submit-btn:disabled {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        cursor: not-allowed;
        opacity: 0.7;
        box-shadow: none;
    }
    
    .floating-submit-btn:active:not(:disabled) {
        transform: translateY(0);
    }
    
    .cursor-sidebar-content {
        padding-bottom: 0;
    }
</style>

<script>
let existingDataInfo = null;
let useExistingDataFlag = false;
let checkDataTimeout = null;
let proceedWithSearchFlag = false; // Flag to allow search to proceed after user clicks "Search Again"
let isCheckingExistingData = false; // Flag to track if we're currently checking for existing data
let modalOpenedFromSearchAgain = false; // Flag to track if modal was opened from "Search Again" button

// Store selected platforms
let selectedPlatforms = []; // Default: no platforms selected

// Store selected analysis type from modal
let selectedAnalysisTypeInModal = null; // No default selection

// Function to update selected platforms from inline checkboxes
function updateSelectedPlatformsFromInline() {
    selectedPlatforms = [];
    const platformCheckboxes = document.querySelectorAll('input[id^="inline_platform_"]');
    platformCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const platformKey = checkbox.value;
            if (!selectedPlatforms.includes(platformKey)) {
                selectedPlatforms.push(platformKey);
            }
        }
    });
}

// Function to validate platforms and set hidden input
function validatePlatforms() {
    const platformsRequired = document.getElementById('platforms-required');
    if (!platformsRequired) return;
    
    // If using existing data or we already have platform data, skip validation
    if (useExistingDataFlag || (window.searchResultsData && window.searchResultsData.platforms)) {
        platformsRequired.removeAttribute('required');
        platformsRequired.setCustomValidity('');
        platformsRequired.value = 'selected';
        return;
    }
    
    // Ensure required attribute is set when not using existing data
    if (!platformsRequired.hasAttribute('required')) {
        platformsRequired.setAttribute('required', 'required');
    }
    
    updateSelectedPlatformsFromInline();
    const platformContainer = document.getElementById('inlinePlatformSelection');
    
    if (selectedPlatforms && selectedPlatforms.length > 0) {
        platformsRequired.value = 'selected';
        platformsRequired.setCustomValidity('');
        // Clear red styling from platform boxes - restore normal styling
        if (platformContainer) {
            const platformItems = platformContainer.children;
            Array.from(platformItems).forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    if (checkbox.checked) {
                        item.style.borderColor = '#667eea';
                        item.style.background = '#f8faff';
                    } else {
                        item.style.borderColor = '#e5e7eb';
                        item.style.background = '#ffffff';
                    }
                }
            });
        }
    } else {
        platformsRequired.value = '';
        platformsRequired.setCustomValidity('Please select at least one platform to analyze.');
        // Add red styling to all platform boxes
        if (platformContainer) {
            const platformItems = platformContainer.children;
            Array.from(platformItems).forEach(item => {
                item.style.borderColor = '#dc2626';
                item.style.background = '#fef2f2';
            });
        }
    }
}

// Function to validate analysis type and set hidden input
function validateAnalysisType() {
    const selectedAnalysisType = document.querySelector('input[name="inlineAnalysisType"]:checked');
    const analysisTypeRequired = document.getElementById('analysis-type-required');
    const professionalDiv = document.getElementById('inlineAnalysisTypeProfessional');
    const politicalDiv = document.getElementById('inlineAnalysisTypePolitical');
    
    if (analysisTypeRequired) {
        if (selectedAnalysisType && window.selectedAnalysisTypeForAnalysis) {
            analysisTypeRequired.value = 'selected';
            analysisTypeRequired.setCustomValidity('');
            // Clear red styling - boxes will have their normal selected/unselected styling
        } else {
            analysisTypeRequired.value = '';
            analysisTypeRequired.setCustomValidity('Please select an analysis type.');
            // Add red styling to both analysis type boxes (lighter red border)
            if (professionalDiv) {
                professionalDiv.style.borderColor = '#f87171';
                professionalDiv.style.background = '#fef2f2';
            }
            if (politicalDiv) {
                politicalDiv.style.borderColor = '#f87171';
                politicalDiv.style.background = '#fef2f2';
            }
        }
    }
}

// Check before allowing search to proceed
async function checkBeforeSearch(event) {
    // Validate custom fields before native validation
    // If using existing data, skip platform validation (platforms are already available)
    if (useExistingDataFlag || (window.searchResultsData && window.searchResultsData.platforms)) {
        // Ensure platforms-required is not required when using existing data
        const platformsRequired = document.getElementById('platforms-required');
        if (platformsRequired) {
            platformsRequired.removeAttribute('required');
            platformsRequired.setCustomValidity('');
            platformsRequired.value = 'selected';
        }
    } else {
        validatePlatforms();
    }
    validateAnalysisType();
    
    // Get the form element
    const form = document.getElementById('searchForm');
    if (!form) {
        return false;
    }
    
    // Check HTML5 validation
    if (!form.checkValidity()) {
        // Find first invalid field and show native validation
        // Skip platforms-required if using existing data
        let firstInvalid = form.querySelector(':invalid');
        if (firstInvalid && firstInvalid.id === 'platforms-required' && (useExistingDataFlag || (window.searchResultsData && window.searchResultsData.platforms))) {
            // Skip this invalid field and find the next one
            const allInvalid = form.querySelectorAll(':invalid');
            firstInvalid = Array.from(allInvalid).find(inv => inv.id !== 'platforms-required') || null;
        }
        
        if (firstInvalid) {
            // For hidden validation inputs, position them near their associated containers
            if (firstInvalid.id === 'platforms-required') {
                const platformContainer = document.getElementById('inlinePlatformSelection');
                if (platformContainer) {
                    const rect = platformContainer.getBoundingClientRect();
                    const originalStyle = firstInvalid.style.cssText;
                    
                    // Temporarily position the input near the container
                    firstInvalid.style.position = 'fixed';
                    firstInvalid.style.top = (rect.top + rect.height / 2) + 'px';
                    firstInvalid.style.left = (rect.left + rect.width / 2) + 'px';
                    firstInvalid.style.width = '1px';
                    firstInvalid.style.height = '1px';
                    firstInvalid.style.opacity = '0.01';
                    firstInvalid.style.zIndex = '10000';
                    
                    // Show validation
                    firstInvalid.reportValidity();
                    
                    // Restore original style after validation message appears
                    setTimeout(() => {
                        firstInvalid.style.cssText = originalStyle;
                    }, 100);
                } else {
                    firstInvalid.reportValidity();
                }
            } else if (firstInvalid.id === 'analysis-type-required') {
                const analysisContainer = document.getElementById('inlineAnalysisTypeProfessional')?.parentElement;
                if (analysisContainer) {
                    const rect = analysisContainer.getBoundingClientRect();
                    const originalStyle = firstInvalid.style.cssText;
                    
                    // Temporarily position the input near the container
                    firstInvalid.style.position = 'fixed';
                    firstInvalid.style.top = (rect.top + rect.height / 2) + 'px';
                    firstInvalid.style.left = (rect.left + rect.width / 2) + 'px';
                    firstInvalid.style.width = '1px';
                    firstInvalid.style.height = '1px';
                    firstInvalid.style.opacity = '0.01';
                    firstInvalid.style.zIndex = '10000';
                    
                    // Show validation
                    firstInvalid.reportValidity();
                    
                    // Restore original style after validation message appears
                    setTimeout(() => {
                        firstInvalid.style.cssText = originalStyle;
                    }, 100);
                } else {
                    firstInvalid.reportValidity();
                }
            } else {
                // For visible fields like username, show validation normally
                firstInvalid.reportValidity();
            }
        }
        // Reset button (keep as Generate, don't change text)
        const searchButton = document.getElementById('searchButton');
        if (searchButton) {
            searchButton.disabled = false;
            // Keep button text as "Generate" - don't change it
        }
        return false;
    }
    
    // Additional custom validation for username length
    const usernameInput = document.getElementById('username');
    const username = usernameInput ? usernameInput.value.trim() : '';
    
    if (username.length < 2) {
        usernameInput.setCustomValidity('Please enter a username (minimum 2 characters).');
        usernameInput.reportValidity();
        // Reset button (keep as Generate)
        const searchButton = document.getElementById('searchButton');
        if (searchButton) {
            searchButton.disabled = false;
            // Keep button text as "Generate" - don't change it
        }
        return false;
    } else {
        usernameInput.setCustomValidity('');
    }
    
    const notification = document.getElementById('existingDataNotification');
    const isNotificationVisible = notification && 
                                   notification.style.display !== 'none' && 
                                   getComputedStyle(notification).display !== 'none';
    
    // If notification is visible and user hasn't made a choice, prevent search
    if (isNotificationVisible && existingDataInfo && !useExistingDataFlag && !proceedWithSearchFlag) {
        console.log('checkBeforeSearch: BLOCKING form submission - Previous search found, user must choose');
        // Make sure platform status is hidden
        const platformStatus = document.getElementById('platformStatus');
        if (platformStatus) {
            platformStatus.style.display = 'none';
        }
        // Reset button (keep as Generate)
        const searchButton = document.getElementById('searchButton');
        if (searchButton) {
            searchButton.disabled = false;
            // Keep button text as "Generate" - don't change it
        }
        return false; // Prevent form submission - user must choose from notification first
    }
    
    // If we're currently checking for existing data, wait for it to complete
    if (isCheckingExistingData) {
        console.log('checkBeforeSearch: Waiting for existing data check to complete...');
        // Wait for the check to complete (max 2 seconds)
        let waitCount = 0;
        while (isCheckingExistingData && waitCount < 20) {
            await new Promise(resolve => setTimeout(resolve, 100));
            waitCount++;
            
            // Check if notification appeared while waiting
            const notificationNow = document.getElementById('existingDataNotification');
            const isNotificationVisibleNow = notificationNow && 
                                             notificationNow.style.display !== 'none' && 
                                             getComputedStyle(notificationNow).display !== 'none';
            
            if (isNotificationVisibleNow && existingDataInfo && !useExistingDataFlag && !proceedWithSearchFlag) {
                console.log('checkBeforeSearch: Notification appeared during wait, blocking modal');
                return false; // Don't show modal, notification takes priority
            }
        }
    }
    
    // Double-check notification state after waiting
    const notificationAfterWait = document.getElementById('existingDataNotification');
    const isNotificationVisibleAfterWait = notificationAfterWait && 
                                          notificationAfterWait.style.display !== 'none' && 
                                          getComputedStyle(notificationAfterWait).display !== 'none';
    
    // If notification is now visible, don't show modal
    if (isNotificationVisibleAfterWait && existingDataInfo && !useExistingDataFlag && !proceedWithSearchFlag) {
        console.log('checkBeforeSearch: Notification visible after wait, blocking modal');
        return false;
    }
    
    // If no previous data or user chose to search again, proceed directly with search
    // Reset the flag since this is a normal search (not from "Search Again")
    modalOpenedFromSearchAgain = false;
    
    // Get selected platforms from inline checkboxes
    updateSelectedPlatformsFromInline();
    
    // Clear any displayed platform data from right panel before starting analysis
    clearPlatformDataFromRightPanel();
    
    // Store the selected analysis type
    const selectedType = document.querySelector('input[name="inlineAnalysisType"]:checked')?.value;
    window.selectedAnalysisTypeForAnalysis = selectedType;
    selectedAnalysisTypeInModal = selectedType;
    
    // Check if we already have platform data (from previous search or use existing data)
    const hasPlatformData = window.searchResultsData && window.searchResultsData.platforms;
    const hasFoundPlatforms = hasPlatformData && Object.values(window.searchResultsData.platforms).some(p => p.found);
    
    // If we have platform data and analysis type, start analysis directly
    if (hasFoundPlatforms && selectedType) {
        // Clear any displayed platform data from right panel before starting analysis
        clearPlatformDataFromRightPanel();
        
        // Set up analysis data
        if (window.searchResultsData && window.searchResultsData.platforms) {
            currentAnalysisData = {};
            Object.keys(window.searchResultsData.platforms).forEach(p => {
                currentAnalysisData[p] = {
                    found: window.searchResultsData.platforms[p].found || false,
                    data: window.searchResultsData.platforms[p].data || null,
                    error: window.searchResultsData.platforms[p].error || null
                };
            });
        }
        
        // Show prompt details and progress
        showPromptDetails();
        showAnalysisProgress();
        
        // Start analysis
        startAnalysis(selectedType);
        return false;
    }
    
    // For new usernames: if analysis type is selected, show progress modal and prompt details before search
    if (selectedType && !hasFoundPlatforms) {
        // Clear any displayed platform data from right panel before starting search/analysis
        clearPlatformDataFromRightPanel();
        
        // Show prompt details with username and platforms (before search completes)
        const username = document.getElementById('username').value.trim();
        const platformNames = {
            'facebook': 'Facebook',
            'instagram': 'Instagram',
            'tiktok': 'TikTok',
            'twitter': 'X (Twitter)'
        };
        
        // Hide search section
        const searchSection = document.getElementById('searchSection');
        if (searchSection) {
            searchSection.style.display = 'none';
        }
        
        // Hide Analyze Profile header, show Analysis Input Details header
        const sidebarHeader = document.getElementById('sidebarHeader');
        if (sidebarHeader) {
            sidebarHeader.style.display = 'none';
        }
        
        // Hide floating submit button
        const floatingSubmitContainer = document.getElementById('floatingSubmitContainer');
        if (floatingSubmitContainer) {
            floatingSubmitContainer.style.display = 'none';
        }
        
        // Show prompt details with selected platforms
        const promptDetailsHeader = document.getElementById('promptDetailsHeader');
        const promptDetailsCard = document.getElementById('promptDetailsCard');
        if (promptDetailsHeader && promptDetailsCard) {
            promptDetailsHeader.style.display = 'block';
            promptDetailsCard.style.display = 'block';
            
            const promptDetailsContent = document.getElementById('promptDetailsContent');
            if (promptDetailsContent) {
                const analysisTypeName = selectedType === 'professional' ? 'Professional' : 'Political';
                
                let detailsHtml = `
                    <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                        <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Username</div>
                        <div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500; margin-bottom: 12px;">${username}</div>
                        <div style="border-bottom: 1px solid #e5e7eb; margin-bottom: 12px; padding-bottom: 12px;"></div>
                        <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; margin-top: 12px;">Platforms</div>
                        <div id="promptDetailsPlatforms" style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500; margin-top: 8px;">
                `;
                
                // Display platforms as comma-separated text
                const platformNamesList = selectedPlatforms.map(platformKey => platformNames[platformKey] || platformKey).join(', ');
                detailsHtml += platformNamesList;
                
                detailsHtml += `
                        </div>
                    </div>
                    <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                        <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Analysis Type</div>
                        <div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${analysisTypeName}</div>
                    </div>
                `;
                
                promptDetailsContent.innerHTML = detailsHtml;
            }
        }
        
        // Hide platform status section (we'll show progress modal instead)
        const platformStatus = document.getElementById('platformStatus');
        if (platformStatus) {
            platformStatus.style.display = 'none';
        }
        
        // Show progress modal (will show during both search and analysis)
        showAnalysisProgress();
    }
    
    // All validations passed - proceed with search
    // Check if search is already in progress
    if (isSearchInProgress) {
        console.log('BLOCKING SEARCH: Search already in progress (from checkBeforeSearch)');
        return false; // Prevent form submission
    }
    
    // Prevent form from submitting (we'll handle it manually)
    if (event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
    }
    
    proceedWithSearchFlag = true;
    performSearch();
    return false; // Prevent form submission
}

// Open platform selection modal
// Select analysis type in modal
function selectAnalysisTypeInModal(type) {
    selectedAnalysisTypeInModal = type;
    
    const professionalDiv = document.getElementById('modalAnalysisTypeProfessional');
    const politicalDiv = document.getElementById('modalAnalysisTypePolitical');
    const professionalRadio = document.getElementById('modalAnalysisType_professional');
    const politicalRadio = document.getElementById('modalAnalysisType_political');
    
    if (type === 'professional') {
        if (professionalDiv) {
            professionalDiv.style.borderColor = '#667eea';
            professionalDiv.style.background = '#f8faff';
        }
        if (politicalDiv) {
            politicalDiv.style.borderColor = '#e2e8f0';
            politicalDiv.style.background = '#ffffff';
        }
        if (professionalRadio) professionalRadio.checked = true;
        if (politicalRadio) politicalRadio.checked = false;
    } else {
        if (professionalDiv) {
            professionalDiv.style.borderColor = '#e2e8f0';
            professionalDiv.style.background = '#ffffff';
        }
        if (politicalDiv) {
            politicalDiv.style.borderColor = '#667eea';
            politicalDiv.style.background = '#f8faff';
        }
        if (professionalRadio) professionalRadio.checked = false;
        if (politicalRadio) politicalRadio.checked = true;
    }
    
    updateProceedButtonState();
}

// Modal functions removed - using inline selections now

// Show username validation modal
function showUsernameValidationModal() {
    const modal = document.getElementById('usernameValidationModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Close username validation modal
function closeUsernameValidationModal() {
    const modal = document.getElementById('usernameValidationModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Select analysis type in modal
function selectAnalysisTypeInModal(type) {
    selectedAnalysisTypeInModal = type;
    
    const professionalDiv = document.getElementById('modalAnalysisTypeProfessional');
    const politicalDiv = document.getElementById('modalAnalysisTypePolitical');
    const professionalRadio = document.getElementById('modalAnalysisType_professional');
    const politicalRadio = document.getElementById('modalAnalysisType_political');
    
    if (type === 'professional') {
        if (professionalDiv) {
            professionalDiv.style.borderColor = '#667eea';
            professionalDiv.style.background = '#f8faff';
        }
        if (politicalDiv) {
            politicalDiv.style.borderColor = '#e2e8f0';
            politicalDiv.style.background = '#ffffff';
        }
        if (professionalRadio) professionalRadio.checked = true;
        if (politicalRadio) politicalRadio.checked = false;
    } else {
        if (professionalDiv) {
            professionalDiv.style.borderColor = '#e2e8f0';
            professionalDiv.style.background = '#ffffff';
        }
        if (politicalDiv) {
            politicalDiv.style.borderColor = '#667eea';
            politicalDiv.style.background = '#f8faff';
        }
        if (professionalRadio) professionalRadio.checked = false;
        if (politicalRadio) politicalRadio.checked = true;
    }
    
    updateProceedButtonState();
}

// Initialize inline platform selection on page load
let platformInitializationInProgress = false;
function initializeInlinePlatformSelection() {
    // Prevent multiple simultaneous initializations
    if (platformInitializationInProgress) {
        return;
    }
    
    const platformContainer = document.getElementById('inlinePlatformSelection');
    if (!platformContainer) {
        console.log('Platform container not found, retrying...');
        // Retry after a short delay in case DOM isn't ready
        setTimeout(function() {
            const retryContainer = document.getElementById('inlinePlatformSelection');
            if (retryContainer && !platformInitializationInProgress) {
                initializeInlinePlatformSelection();
            }
        }, 100);
        return;
    }
    
    // Check if already initialized (has children)
    if (platformContainer.children.length > 0) {
        console.log('Platforms already initialized');
        return;
    }
    
    platformInitializationInProgress = true;
    
    const platforms = [
        { key: 'facebook', name: 'Facebook', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>' },
        { key: 'instagram', name: 'Instagram', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px;"><defs><linearGradient id="instagram-gradient-inline" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-inline)"/></svg>' },
        { key: 'tiktok', name: 'TikTok', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>' },
        { key: 'twitter', name: 'X (Twitter)', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' }
    ];
    
    platformContainer.innerHTML = '';
    selectedPlatforms = [];
    
    // Create checkboxes for each platform (all checked by default)
    platforms.forEach(platform => {
        const platformItem = document.createElement('div');
        platformItem.style.cssText = 'padding: 12px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s ease;';
        platformItem.onmouseover = function() {
            if (!this.querySelector('input').checked) {
            this.style.borderColor = '#667eea';
                this.style.background = '#f8faff';
            }
        };
        platformItem.onmouseout = function() {
            if (!this.querySelector('input').checked) {
                this.style.borderColor = '#e5e7eb';
                this.style.background = '#ffffff';
            }
        };
        platformItem.onclick = function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('input');
                checkbox.checked = !checkbox.checked;
                toggleInlinePlatformSelection(platform.key);
            }
        };
        
        platformItem.innerHTML = `
            <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                <input type="checkbox" id="inline_platform_${platform.key}" value="${platform.key}" onchange="toggleInlinePlatformSelection('${platform.key}')" 
                       style="width: 18px; height: 18px; margin-right: 8px; cursor: pointer; accent-color: #667eea;">
                <div style="width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; margin-right: 8px; flex-shrink: 0;">${platform.icon}</div>
                <span style="font-weight: 600; color: #1e293b; font-size: 12px; flex: 1;">${platform.name}</span>
            </label>
        `;
        
        platformItem.style.borderColor = '#e5e7eb';
        platformItem.style.background = '#ffffff';
        
        platformContainer.appendChild(platformItem);
    });
    
    platformInitializationInProgress = false;
    
    // Re-attach tooltip listeners after platforms are initialized
    setTimeout(function() {
        if (typeof window.attachTooltipListeners === 'function') {
            window.attachTooltipListeners();
        }
    }, 50);
    
    console.log('Platform selection initialized successfully');
}

// Select analysis type inline
function selectAnalysisTypeInline(type) {
    selectedAnalysisTypeInModal = type;
    window.selectedAnalysisTypeForAnalysis = type;
    
    const professionalDiv = document.getElementById('inlineAnalysisTypeProfessional');
    const politicalDiv = document.getElementById('inlineAnalysisTypePolitical');
    const professionalRadio = document.getElementById('inlineAnalysisType_professional');
    const politicalRadio = document.getElementById('inlineAnalysisType_political');
    
    if (type === 'professional') {
        if (professionalDiv) {
            professionalDiv.style.borderColor = '#667eea';
            professionalDiv.style.background = '#f8faff';
        }
        if (politicalDiv) {
            politicalDiv.style.borderColor = '#e2e8f0';
            politicalDiv.style.background = '#ffffff';
        }
        if (professionalRadio) professionalRadio.checked = true;
        if (politicalRadio) politicalRadio.checked = false;
    } else {
        if (professionalDiv) {
            professionalDiv.style.borderColor = '#e2e8f0';
            professionalDiv.style.background = '#ffffff';
        }
        if (politicalDiv) {
            politicalDiv.style.borderColor = '#667eea';
            politicalDiv.style.background = '#f8faff';
        }
        if (professionalRadio) professionalRadio.checked = false;
        if (politicalRadio) politicalRadio.checked = true;
    }
    
    // Validate analysis type when selection changes (this will clear red styling)
    validateAnalysisType();
}

// Toggle inline platform selection
function toggleInlinePlatformSelection(platformKey) {
    const checkbox = document.getElementById(`inline_platform_${platformKey}`);
    if (!checkbox) return;
    
    if (checkbox.checked) {
        if (!selectedPlatforms.includes(platformKey)) {
            selectedPlatforms.push(platformKey);
        }
    } else {
        selectedPlatforms = selectedPlatforms.filter(p => p !== platformKey);
    }
    
    // Validate platforms when selection changes (this will clear red styling if valid)
    validatePlatforms();
    
    // Update visual state (only if validation passed, otherwise validatePlatforms handles it)
    if (selectedPlatforms && selectedPlatforms.length > 0) {
    const platformItem = checkbox.closest('div');
    if (platformItem) {
        if (checkbox.checked) {
            platformItem.style.borderColor = '#667eea';
            platformItem.style.background = '#f8faff';
        } else {
            platformItem.style.borderColor = '#e5e7eb';
            platformItem.style.background = '#ffffff';
            }
        }
    }
}

// Update proceed button state
function updateProceedButtonState() {
    const proceedButton = document.getElementById('proceedSearchButton');
    if (proceedButton) {
        if (selectedPlatforms.length === 0) {
            proceedButton.disabled = true;
            proceedButton.style.opacity = '0.5';
            proceedButton.style.cursor = 'not-allowed';
            proceedButton.textContent = 'Select at least one platform';
        } else {
            proceedButton.disabled = false;
            proceedButton.style.opacity = '1';
            proceedButton.style.cursor = 'pointer';
            const analysisTypeLabel = selectedAnalysisTypeInModal === 'professional' ? 'Professional' : 'Political';
            proceedButton.textContent = `Start ${analysisTypeLabel} Analysis`;
        }
    }
}

// Store the selected analysis type globally for use in analysis
window.selectedAnalysisTypeForAnalysis = null;

// Function to show prompt details in left panel
function showPromptDetails() {
    const formCard = document.getElementById('searchSection');
    const promptDetailsCard = document.getElementById('promptDetailsCard');
    const promptDetailsContent = document.getElementById('promptDetailsContent');
    
    if (!formCard || !promptDetailsCard) return;
    
    // Hide form, show prompt details
    formCard.style.display = 'none';
    promptDetailsCard.style.display = 'block';
    
    // Hide Analyze Profile header, show Analysis Input Details header
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
    
    // Hide prompt details action buttons during analysis (show them after analysis completes)
    const promptDetailsActions = document.getElementById('promptDetailsActions');
    if (promptDetailsActions) {
        promptDetailsActions.style.display = 'none';
    }
    
    // Get form values
    const username = document.getElementById('username')?.value.trim() || 'N/A';
    const selectedType = document.querySelector('input[name="inlineAnalysisType"]:checked')?.value || 'N/A';
    const analysisTypeLabel = selectedType === 'professional' ? 'Professional' : selectedType === 'political' ? 'Political' : 'N/A';
    
    // Get platform data
    const platformData = window.searchResultsData?.platforms || {};
    const foundPlatforms = Object.keys(platformData).filter(p => platformData[p]?.found);
    
    // Build prompt details HTML
    const platformNames = {
        'facebook': 'Facebook',
        'instagram': 'Instagram',
        'tiktok': 'TikTok',
        'twitter': 'X (Twitter)'
    };
    
    const platformIcons = {
        'facebook': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'instagram': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px;"><defs><linearGradient id="instagram-gradient-details" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-details)"/></svg>',
        'tiktok': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
        'twitter': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
    };
    
    let detailsHtml = `
        <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
            <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Username</div>
            <div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500; margin-bottom: ${foundPlatforms.length > 0 ? '12px' : '0'};">${username}</div>
            ${foundPlatforms.length > 0 ? `
                <div style="border-bottom: 1px solid #e5e7eb; margin-bottom: 12px; padding-bottom: 12px;"></div>
                <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; margin-top: 12px;">Platforms</div>
                <div id="promptDetailsPlatforms" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <!-- Platform boxes will be populated here -->
                </div>
            ` : ''}
        </div>
        <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
            <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Analysis Type</div>
            <div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${analysisTypeLabel}</div>
        </div>
    `;
    
    if (promptDetailsContent) {
        promptDetailsContent.innerHTML = detailsHtml;
        
        // Create platform boxes if there are found platforms
        if (foundPlatforms.length > 0) {
            const platformsContainer = document.getElementById('promptDetailsPlatforms');
            if (platformsContainer) {
                // Check if analysis failed - if so, display as plain text
                if (window.analysisFailed) {
                    // Display platforms as plain text (comma-separated)
                    const platformText = foundPlatforms.map(key => platformNames[key] || key).join(', ');
                    platformsContainer.style.display = 'block';
                    platformsContainer.style.gridTemplateColumns = 'none';
                    platformsContainer.style.gap = '0';
                    platformsContainer.innerHTML = `<div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${platformText}</div>`;
                } else {
                    // Display platforms as clickable boxes (normal flow)
                    const platformIcons = {
                        'facebook': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                        'instagram': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px;"><defs><linearGradient id="instagram-gradient-details" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-details)"/></svg>',
                        'tiktok': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
                        'twitter': '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
                    };
                    
                    foundPlatforms.forEach(platformKey => {
                        const platformItem = document.createElement('div');
                        platformItem.id = `prompt-platform-${platformKey}`;
                        platformItem.style.cssText = 'padding: 12px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; cursor: pointer; transition: all 0.2s ease;';
                        
                        platformItem.onclick = function() {
                            togglePlatformViewInAnalysis(platformKey, this);
                        };
                        
                        platformItem.onmouseover = function() {
                            if (!this.classList.contains('active')) {
                                this.style.borderColor = '#667eea';
                                this.style.background = '#f8faff';
                            }
                        };
                        
                        platformItem.onmouseout = function() {
                            if (!this.classList.contains('active')) {
                                this.style.borderColor = '#86efac';
                                this.style.background = '#f0fdf4';
                            }
                        };
                        
                        platformItem.innerHTML = `
                            <div style="display: flex; align-items: center; margin: 0;">
                                <div style="width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; margin-right: 8px; flex-shrink: 0;">${platformIcons[platformKey] || ''}</div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 12px;">${platformNames[platformKey] || platformKey}</div>
                                    <div style="color: #059669; font-size: 11px; margin-top: 2px;">Click to view</div>
                                </div>
                            </div>
                        `;
                        
                        platformsContainer.appendChild(platformItem);
                    });
                }
                
                // If analysis is in progress, disable the platform boxes immediately
                const progressCard = document.getElementById('progressCard');
                if (progressCard && progressCard.style.display !== 'none') {
                    setPlatformBoxesEnabled(false);
                }
            }
        }
    }
}

// Function to disable/enable platform boxes in Analysis Input Details
function setPlatformBoxesEnabled(enabled) {
    const allPlatformBoxes = document.querySelectorAll('#promptDetailsPlatforms > div');
    allPlatformBoxes.forEach(box => {
        if (enabled) {
            box.style.cursor = 'pointer';
            box.style.opacity = '1';
            box.style.pointerEvents = 'auto';
            // Restore hover effects
            const platformKey = box.id.replace('prompt-platform-', '');
            box.onmouseover = function() {
                if (!this.classList.contains('active')) {
                    this.style.borderColor = '#667eea';
                    this.style.background = '#f8faff';
                }
            };
            box.onmouseout = function() {
                if (!this.classList.contains('active')) {
                    this.style.borderColor = '#86efac';
                    this.style.background = '#f0fdf4';
                }
            };
        } else {
            box.style.cursor = 'not-allowed';
            box.style.opacity = '0.6';
            box.style.pointerEvents = 'none';
            // Remove hover effects
            box.onmouseover = null;
            box.onmouseout = null;
            // Reset to default styling if not active
            if (!box.classList.contains('active')) {
                box.style.borderColor = '#86efac';
                box.style.background = '#f0fdf4';
            }
        }
    });
}

// Function to show analysis progress in right panel
function showAnalysisProgress() {
    const progressCard = document.getElementById('progressCard');
    const didYouKnowSection = document.getElementById('didYouKnowSection');
    
    if (!progressCard || !didYouKnowSection) return;
    
    // Convert platform boxes to plain text during loading
    const platformsContainer = document.getElementById('promptDetailsPlatforms');
    if (platformsContainer && window.searchResultsData && window.searchResultsData.platforms) {
        const platformNames = {
            'facebook': 'Facebook',
            'instagram': 'Instagram',
            'tiktok': 'TikTok',
            'twitter': 'X (Twitter)'
        };
        const foundPlatforms = Object.keys(window.searchResultsData.platforms).filter(key => 
            window.searchResultsData.platforms[key] && window.searchResultsData.platforms[key].found
        );
        if (foundPlatforms.length > 0) {
            const platformText = foundPlatforms.map(key => platformNames[key] || key).join(', ');
            platformsContainer.style.display = 'block';
            platformsContainer.style.gridTemplateColumns = 'none';
            platformsContainer.style.gap = '0';
            platformsContainer.innerHTML = `<div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${platformText}</div>`;
        }
    }
    
    // Disable platform boxes during analysis (if they still exist)
    setPlatformBoxesEnabled(false);
    
    // Hide animated background
    const animatedBackground = document.getElementById('animatedBackground');
    if (animatedBackground) {
        animatedBackground.style.display = 'none';
    }
    
    // Set main panel background to white
    const mainPanel = document.querySelector('.cursor-main');
    if (mainPanel) {
        mainPanel.style.background = '#ffffff';
        mainPanel.classList.remove('animated-ai-background');
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
    didYouKnowSection.style.display = 'none';
    progressCard.style.display = 'block';
    
    // Status messages sequence
    const statusSequence = [
        { text: 'Processing your request...', progress: 10 },
        { text: 'Analyzing profile data...', progress: 25 },
        { text: 'Extracting insights from platforms...', progress: 40 },
        { text: 'Analyzing with NUJUM AI...', progress: 60 },
        { text: 'Generating analysis...', progress: 75 },
        { text: 'Finalizing results...', progress: 90 },
        { text: 'Almost done...', progress: 95 }
    ];
    
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressStatus = document.getElementById('progressStatus');
    
    if (!progressBar || !progressText || !progressStatus) return;
    
    let currentStatusIndex = 0;
    let currentProgress = 0;
    
    // Simulate progress
    let progressUpdateInterval = setInterval(() => {
        if (currentProgress < 95) {
            currentProgress += 0.3;
            progressBar.style.width = currentProgress + '%';
            progressText.textContent = Math.round(currentProgress) + '%';
        }
    }, 500);
    
    // Update status messages
    const statusMessages = document.getElementById('statusMessages');
    const statusInterval = setInterval(() => {
        if (currentStatusIndex < statusSequence.length) {
            const status = statusSequence[currentStatusIndex];
            currentProgress = status.progress;
            
            // Update progress bar to match status
            progressBar.style.width = currentProgress + '%';
            progressText.textContent = currentProgress + '%';
            progressStatus.textContent = status.text;
            
            // Status messages are not displayed in the progress card (matching analyze prediction page)
            
            currentStatusIndex++;
        } else {
            // Slow down near completion
            if (currentProgress < 98) {
                currentProgress += 0.2;
                progressBar.style.width = currentProgress + '%';
                progressText.textContent = Math.round(currentProgress) + '%';
            }
        }
    }, 20000);
    
    // Store interval IDs for cleanup
    progressCard.dataset.progressInterval = progressUpdateInterval;
    progressCard.dataset.statusInterval = statusInterval;
}

// Initialize platform status grid (matching platform selection design)
function initializePlatformStatusGrid(platformData = null, showOnlyFound = false) {
    const platformStatusGrid = document.getElementById('platformStatusGrid');
    if (!platformStatusGrid) return;
    
    // Clear existing content
    platformStatusGrid.innerHTML = '';
    
    const platforms = [
        { key: 'facebook', name: 'Facebook', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>' },
        { key: 'instagram', name: 'Instagram', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px;"><defs><linearGradient id="instagram-gradient-status" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-status)"/></svg>' },
        { key: 'tiktok', name: 'TikTok', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>' },
        { key: 'twitter', name: 'X (Twitter)', icon: '<svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' }
    ];
    
    platforms.forEach(platform => {
        const data = platformData ? platformData[platform.key] : null;
        const isFound = data && data.found;
        
        // If showOnlyFound is true, skip platforms that are not found
        if (showOnlyFound && !isFound) {
            return;
        }
        
        const platformItem = document.createElement('div');
        platformItem.id = `status-${platform.key}`;
        platformItem.style.cssText = 'padding: 12px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s ease;';
        
        if (isFound) {
            platformItem.style.borderColor = '#86efac';
            platformItem.style.background = '#f0fdf4';
            platformItem.onclick = () => showPlatformResults(platform.key);
        } else {
            platformItem.style.borderColor = '#fecaca';
            platformItem.style.background = '#fef2f2';
            platformItem.style.cursor = 'not-allowed';
        }
        
        platformItem.onmouseover = function() {
            if (isFound) {
                this.style.borderColor = '#667eea';
                this.style.background = '#f8faff';
            }
        };
        platformItem.onmouseout = function() {
            if (isFound) {
                this.style.borderColor = '#86efac';
                this.style.background = '#f0fdf4';
            }
        };
        
        // Show status text for both found and not found platforms
        const statusText = isFound ? 'Click to view' : 'Not Found';
        const statusColor = isFound ? '#059669' : '#dc2626';
        
        platformItem.innerHTML = `
            <div style="display: flex; align-items: center; margin: 0;">
                <div style="width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; margin-right: 8px; flex-shrink: 0;">${platform.icon}</div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #1e293b; font-size: 12px;">${platform.name}</div>
                    <div style="color: ${statusColor}; font-size: 11px; margin-top: 2px;">${statusText}</div>
                </div>
            </div>
        `;
        
        platformStatusGrid.appendChild(platformItem);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded fired');
    
    // Ensure platform section is visible on page load
    const platformSection = document.getElementById('platformSelectionSection');
    if (platformSection) {
        platformSection.style.display = 'block';
        console.log('Platform section made visible');
    } else {
        console.error('Platform section not found!');
    }
    
    // Ensure analysis type section is visible on page load
    const analysisTypeSection = document.getElementById('analysisTypeSection');
    if (analysisTypeSection) {
        analysisTypeSection.style.display = 'block';
    }
    
    // Check if platform container exists
    const platformContainer = document.getElementById('inlinePlatformSelection');
    if (!platformContainer) {
        console.error('Platform container not found in DOMContentLoaded!');
    } else {
        console.log('Platform container found, initializing...');
    }
    
    // Initialize platform selection first
    if (typeof initializeInlinePlatformSelection === 'function') {
        console.log('Calling initializeInlinePlatformSelection');
    initializeInlinePlatformSelection();
    } else {
        console.error('initializeInlinePlatformSelection function not found!');
    }
    
    // Attach tooltip listeners after platforms are initialized
    setTimeout(function() {
        if (typeof window.attachTooltipListeners === 'function') {
            window.attachTooltipListeners();
        }
    }, 100);
    
    // Dynamic tooltip positioning with fixed positioning - Matching Create Predictions Page
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
    
    window.showTooltip = function(element, text) {
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
    };
    
    window.hideTooltip = function() {
        if (tooltipElement) {
            tooltipElement.classList.remove('show');
        }
        if (tooltipArrow) {
            tooltipArrow.classList.remove('show');
        }
    };
    
    // Function to attach tooltip event listeners (make it globally accessible)
    window.attachTooltipListeners = function() {
    const tooltips = document.querySelectorAll('.info-tooltip');
    tooltips.forEach(tooltip => {
            const tooltipText = tooltip.getAttribute('data-tooltip');
            
            // Remove existing listeners by cloning (if any were added before)
            const newTooltip = tooltip.cloneNode(true);
            tooltip.parentNode.replaceChild(newTooltip, tooltip);
        });
        
        // Re-query after cloning to get fresh elements
        const freshTooltips = document.querySelectorAll('.info-tooltip');
        freshTooltips.forEach(tooltip => {
        const tooltipText = tooltip.getAttribute('data-tooltip');
        
        tooltip.addEventListener('mouseenter', function(e) {
                if (tooltipText && typeof window.showTooltip === 'function') {
                    window.showTooltip(this, tooltipText);
            }
        });
        
        tooltip.addEventListener('mouseleave', function() {
                if (typeof window.hideTooltip === 'function') {
                    window.hideTooltip();
                }
        });
        
        // Mobile click support
        tooltip.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation();
                const isActive = this.classList.contains('active');
                    freshTooltips.forEach(t => t.classList.remove('active'));
                    if (!isActive && tooltipText && typeof window.showTooltip === 'function') {
                    this.classList.add('active');
                        window.showTooltip(this, tooltipText);
                } else {
                        if (typeof window.hideTooltip === 'function') {
                            window.hideTooltip();
                        }
                }
            }
        });
    });
    };
    
    // Attach tooltip listeners after a small delay to ensure DOM is ready
    setTimeout(function() {
        if (typeof window.attachTooltipListeners === 'function') {
            window.attachTooltipListeners();
        }
    }, 200);
    
    // Also ensure platforms are initialized after tooltips
    setTimeout(function() {
        if (typeof initializeInlinePlatformSelection === 'function') {
            const container = document.getElementById('inlinePlatformSelection');
            if (container && container.children.length === 0) {
                initializeInlinePlatformSelection();
            }
        }
        // Re-attach tooltips after platforms are initialized
        if (typeof window.attachTooltipListeners === 'function') {
            window.attachTooltipListeners();
            }
    }, 300);
    
    // Close tooltips when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!e.target.closest('.info-tooltip')) {
                const allTooltips = document.querySelectorAll('.info-tooltip');
                allTooltips.forEach(t => t.classList.remove('active'));
                if (typeof window.hideTooltip === 'function') {
                    window.hideTooltip();
                }
            }
        }
    });
    
    // Hide tooltip on scroll
    window.addEventListener('scroll', hideTooltip, true);
});

// Check for existing data when user types (with debounce)
function checkExistingData(username) {
    // If notification is already visible and user hasn't made a choice, don't re-check
    const notification = document.getElementById('existingDataNotification');
    const isNotificationVisible = notification && 
                                   notification.style.display !== 'none' && 
                                   getComputedStyle(notification).display !== 'none';
    
    // If notification is visible and we have existing data, don't re-check unless username changed significantly
    if (isNotificationVisible && existingDataInfo) {
        const currentUsername = username.trim();
        const existingUsername = existingDataInfo.username ? existingDataInfo.username.trim() : '';
        
        // Only re-check if username changed significantly (more than just whitespace)
        if (currentUsername === existingUsername || currentUsername.length < 2) {
            return; // Don't re-check, keep notification visible
        }
    }
    
    // Clear previous timeout
    if (checkDataTimeout) {
        clearTimeout(checkDataTimeout);
    }

    if (!username || username.length < 2) {
        // Reset form when username is cleared
            document.getElementById('existingDataNotification').style.display = 'none';
            existingDataInfo = null;
        useExistingDataFlag = false;
        proceedWithSearchFlag = false;
        
        // Hide platform status section
        const platformStatus = document.getElementById('platformStatus');
        if (platformStatus) {
            platformStatus.style.display = 'none';
        }
        
        // Show search section
        const searchSection = document.getElementById('searchSection');
        if (searchSection) {
            searchSection.style.display = 'block';
        }
        
        // Hide results container
        const resultsContainer = document.getElementById('resultsContainer');
        if (resultsContainer) {
            resultsContainer.style.display = 'none';
        }
        
        // Show "Did You Know" section again in right panel
        const didYouKnowSection = document.getElementById('didYouKnowSection');
        if (didYouKnowSection) {
            didYouKnowSection.style.display = 'block';
        }
        
        // Remove platform results from right panel
        const rightPanelContent = document.querySelector('.cursor-main-content');
        const rightPanelMain = document.querySelector('.cursor-main');
        const animatedBackground = document.getElementById('animatedBackground');
        
        if (rightPanelContent) {
            const platformResultsContainer = rightPanelContent.querySelector('#platformResultsContainer');
            if (platformResultsContainer) {
                platformResultsContainer.remove();
            }
            // Reset right panel layout to centered
            rightPanelContent.style.alignItems = 'center';
            rightPanelContent.style.justifyContent = 'center';
            rightPanelContent.classList.remove('scrollable');
        }
        
        // Show animated background again
        if (rightPanelMain) {
            rightPanelMain.classList.add('animated-ai-background');
        }
        if (animatedBackground) {
            animatedBackground.style.display = 'block';
        }
        
        currentDisplayedPlatform = null;
        
        // Show search button
            const searchBtn = document.getElementById('searchButton');
            if (searchBtn) {
                searchBtn.style.display = 'block';
            }
            
            // Show platform text again
            const platformTextEl = document.querySelector('#searchForm p');
            if (platformTextEl) {
                platformTextEl.style.display = 'block';
            }
        
        // Show Platforms and Analysis Type sections
        const platformSection = document.getElementById('platformSelectionSection');
        if (platformSection) {
            platformSection.style.display = 'block';
        }
        
        const analysisTypeSection = document.getElementById('analysisTypeSection');
        if (analysisTypeSection) {
            analysisTypeSection.style.display = 'block';
        }
        
        // Clear search results data
        window.searchResultsData = null;
        
        // Restore required attribute for platforms-required when username is cleared
        const platformsRequired = document.getElementById('platforms-required');
        if (platformsRequired) {
            platformsRequired.setAttribute('required', 'required');
            platformsRequired.setCustomValidity('');
            platformsRequired.value = '';
        }
        
        // Reset platform selections
        selectedPlatforms = [];
        const platformCheckboxes = document.querySelectorAll('input[id^="inline_platform_"]');
        platformCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            const platformItem = checkbox.closest('div');
            if (platformItem) {
                platformItem.style.borderColor = '#e5e7eb';
                platformItem.style.background = '#ffffff';
            }
        });
        
        // Reset analysis type
        window.selectedAnalysisTypeForAnalysis = null;
        const analysisTypeRadios = document.querySelectorAll('input[name="inlineAnalysisType"]');
        analysisTypeRadios.forEach(radio => {
            radio.checked = false;
        });
        const professionalDiv = document.getElementById('inlineAnalysisTypeProfessional');
        const politicalDiv = document.getElementById('inlineAnalysisTypePolitical');
        if (professionalDiv) {
            professionalDiv.style.borderColor = '#e2e8f0';
            professionalDiv.style.background = '#ffffff';
        }
        if (politicalDiv) {
            politicalDiv.style.borderColor = '#e2e8f0';
            politicalDiv.style.background = '#ffffff';
        }
        
        return;
    }

    // Debounce: wait 500ms after user stops typing
    checkDataTimeout = setTimeout(async () => {
        isCheckingExistingData = true; // Mark that we're checking
        try {
            const response = await fetch('{{ route("social-media.get-existing-data") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ username: username.trim() })
            });

            const result = await response.json();
            
            if (result.success && result.exists) {
                existingDataInfo = result;
                const notification = document.getElementById('existingDataNotification');
                notification.style.display = 'block';
                
                // Hide search button when notification appears
                const searchBtn = document.getElementById('searchButton');
                if (searchBtn) {
                    searchBtn.style.display = 'none';
                }
                
                // Hide platform text when notification appears
                const platformTextEl = document.querySelector('#searchForm p');
                if (platformTextEl) {
                    platformTextEl.style.display = 'none';
                }
                
                // Hide Platforms and Analysis Type sections when notification appears
                const platformSection = document.getElementById('platformSelectionSection');
                if (platformSection) {
                    platformSection.style.display = 'none';
                }
                
                const analysisTypeSection = document.getElementById('analysisTypeSection');
                if (analysisTypeSection) {
                    analysisTypeSection.style.display = 'none';
                }
                
                // IMPORTANT: Hide platform status section when notification appears
                const platformStatus = document.getElementById('platformStatus');
                if (platformStatus) {
                    platformStatus.style.display = 'none';
                }
                
                // Update notification with date info
                if (result.created_at) {
                    const date = new Date(result.created_at);
                    const dateStr = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const dateInfo = notification.querySelector('.date-info');
                    if (dateInfo) {
                        dateInfo.textContent = `Last searched: ${dateStr}`;
                    }
                }
            } else {
                // Only hide notification if it was showing for a different username
                if (isNotificationVisible && existingDataInfo) {
                    existingDataInfo = null;
                    document.getElementById('existingDataNotification').style.display = 'none';
                    
                    // Show search button again
                    const searchBtn = document.getElementById('searchButton');
                    if (searchBtn) {
                        searchBtn.style.display = 'block';
                    }
                    
                    // Show Platforms and Analysis Type sections again
                    const platformSection = document.getElementById('platformSelectionSection');
                    if (platformSection) {
                        platformSection.style.display = 'block';
                    }
                    
                    const analysisTypeSection = document.getElementById('analysisTypeSection');
                    if (analysisTypeSection) {
                        analysisTypeSection.style.display = 'block';
                    }
                    
                    // Show platform text again
                    const platformTextEl = document.querySelector('#searchForm p');
                    if (platformTextEl) {
                        platformTextEl.style.display = 'block';
                    }
                } else {
                    existingDataInfo = null;
                }
            }
        } catch (error) {
            console.error('Error checking existing data:', error);
            // Don't show error to user, just continue
        } finally {
            isCheckingExistingData = false; // Mark that check is complete
        }
    }, 500);
}

// Use existing data
function useExistingData() {
    if (!existingDataInfo || !existingDataInfo.platform_data) {
        alert('No existing data available');
        return;
    }

    // Set the search results data from existing data
    window.searchResultsData = {
        username: document.getElementById('username').value.trim(),
        platforms: existingDataInfo.platform_data,
        total_found: Object.values(existingDataInfo.platform_data).filter(p => p.found).length
    };

    useExistingDataFlag = true;
    
    // Hide the notification
    document.getElementById('existingDataNotification').style.display = 'none';
    
    // Show the form again (return to usual form)
    const searchSection = document.getElementById('searchSection');
    if (searchSection) {
        searchSection.style.display = 'block';
    }

    // Keep Platforms section hidden (no need to search again when using previous data)
    const platformSection = document.getElementById('platformSelectionSection');
    if (platformSection) {
        platformSection.style.display = 'none';
    }
    
    // Show Analysis Type section (user still needs to select analysis type)
    const analysisTypeSection = document.getElementById('analysisTypeSection');
    if (analysisTypeSection) {
        analysisTypeSection.style.display = 'block';
        }
    
    // Show Generate button
    const searchBtn = document.getElementById('searchButton');
    if (searchBtn) {
        searchBtn.style.display = 'block';
    }
    
    // Show platform status section
    const platformStatus = document.getElementById('platformStatus');
    if (platformStatus) {
        platformStatus.style.display = 'block';
    }

    // Initialize and update platform status grid (show only found platforms)
    initializePlatformStatusGrid(existingDataInfo.platform_data, true);
    
    // Remove required validation from platforms-required since we're using existing data
    const platformsRequired = document.getElementById('platforms-required');
    if (platformsRequired) {
        platformsRequired.removeAttribute('required');
        platformsRequired.setCustomValidity('');
        platformsRequired.value = 'selected';
    }
    
}

// Search again (ignore existing data)
function searchAgain() {
    useExistingDataFlag = false;
    proceedWithSearchFlag = true; // Allow search to proceed
    existingDataInfo = null;
    modalOpenedFromSearchAgain = true; // Mark that modal is being opened from "Search Again"
    document.getElementById('existingDataNotification').style.display = 'none';
    
    // Show Platforms and Analysis Type sections again
    const platformSection = document.getElementById('platformSelectionSection');
    if (platformSection) {
        platformSection.style.display = 'block';
    }
    
    const analysisTypeSection = document.getElementById('analysisTypeSection');
    if (analysisTypeSection) {
        analysisTypeSection.style.display = 'block';
    }
    
    // Show search button again
    const searchBtn = document.getElementById('searchButton');
    if (searchBtn) {
        searchBtn.style.display = 'block';
    }
    
    // Show platform text again
    const platformTextEl = document.querySelector('#searchForm p');
    if (platformTextEl) {
        platformTextEl.style.display = 'block';
    }
    
    // Reset platform status grid
    const platformStatus = document.getElementById('platformStatus');
    if (platformStatus) {
        platformStatus.style.display = 'none';
        }
    
    // Reset and show inline selections
    initializeInlinePlatformSelection();
}

// Global flag to prevent duplicate concurrent searches
let isSearchInProgress = false;

async function performSearch() {
    // Prevent duplicate concurrent searches
    if (isSearchInProgress) {
        console.log('performSearch: BLOCKED - Search already in progress');
        return;
    }
    
    const username = document.getElementById('username').value.trim();
    if (!username) {
        console.log('performSearch: No username, aborting');
        return;
    }

    // CRITICAL: Check if we should block the search
    const notification = document.getElementById('existingDataNotification');
    const isNotificationVisible = notification && 
                                   notification.style.display !== 'none' && 
                                   getComputedStyle(notification).display !== 'none';
    
    if (isNotificationVisible && existingDataInfo && !useExistingDataFlag && !proceedWithSearchFlag) {
        console.log('performSearch: BLOCKED - Notification visible and user has not chosen');
        return; // Don't make backend call
    }
    
    // Set flag to prevent duplicate searches
    isSearchInProgress = true;

    // Hide "Did You Know" section when search starts
    const didYouKnowSection = document.getElementById('didYouKnowSection');
    if (didYouKnowSection) {
        didYouKnowSection.style.display = 'none';
    }

    const searchButton = document.getElementById('searchButton');
    const platformStatus = document.getElementById('platformStatus');
    const resultsContainer = document.getElementById('resultsContainer');
    
    // Only show platform status if progress modal is not already showing (for new usernames with analysis type)
    const progressCard = document.getElementById('progressCard');
    const isProgressModalShowing = progressCard && progressCard.style.display === 'block';
    
    if (!isProgressModalShowing && platformStatus) {
        platformStatus.style.display = 'block';
        // Initialize with empty data (will show "Searching..." state)
        initializePlatformStatusGrid({});
    } else if (platformStatus) {
        // Hide platform status if progress modal is showing
        platformStatus.style.display = 'none';
    }
    
    console.log('performSearch: Making backend call to search-all with platforms:', selectedPlatforms);
    let errorToastShown = false; // Flag to prevent duplicate toast notifications
    // Reset global second toast flag at start of function
    window.secondToastShown = false;
    
    try {
        const response = await fetch('{{ route("social-media.search-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ 
                username: username,
                platforms: selectedPlatforms
            })
        });

        const data = await response.json();
        
        // Collect all error messages from data and platforms
        let allErrorMessages = [];
        if (data.error) allErrorMessages.push(data.error);
        if (data.message) allErrorMessages.push(data.message);
        if (data.platforms) {
            Object.values(data.platforms).forEach(p => {
                if (p.error) allErrorMessages.push(p.error);
                if (p.message) allErrorMessages.push(p.message);
            });
        }
        const combinedErrorText = allErrorMessages.join(' ').toLowerCase();
        
        // Check if search failed - only fail if NO platforms were found
        // If at least one platform is found, proceed with analysis even if some platforms failed
        const hasFoundPlatforms = data.platforms && Object.values(data.platforms).some(p => p && p.found === true);
        const hasPlatforms = data.platforms && Object.keys(data.platforms).length > 0;
        
        // Only fail if:
        // 1. No platforms exist at all, OR
        // 2. Platforms exist but NONE were found (all platforms failed)
        const searchFailed = !hasPlatforms || (hasPlatforms && !hasFoundPlatforms);
        
        if (searchFailed) {
            // Stop progress modal
            const progressCard = document.getElementById('progressCard');
            if (progressCard) {
                progressCard.style.display = 'none';
                // Clear progress intervals
                if (progressCard.dataset.progressInterval) {
                    clearInterval(parseInt(progressCard.dataset.progressInterval));
                }
                if (progressCard.dataset.statusInterval) {
                    clearInterval(parseInt(progressCard.dataset.statusInterval));
                }
            }
            
            // Show error message in right panel for 10 seconds
            const mainContent = document.querySelector('.cursor-main-content');
            if (mainContent) {
                mainContent.style.display = 'flex';
                mainContent.style.alignItems = 'center';
                mainContent.style.justifyContent = 'center';
                mainContent.style.padding = '24px';
                mainContent.style.background = '#ffffff';
                
                // Determine specific error message based on error type
                let errorMessage = data.error || data.message || '';
                let errorTitle = 'Search Failed';
                let errorSubtext = 'Please try again with a different username or check your platform selections.';
                
                // Check if it's an Apify API error (monthly usage limit, API issues, etc.)
                // Use combined error text from all sources (data.error, data.message, platform errors)
                const errorLower = combinedErrorText || errorMessage.toLowerCase();
                const isApifyError = errorLower.includes('apify') || 
                                   errorLower.includes('monthly') || 
                                   errorLower.includes('usage limit') || 
                                   errorLower.includes('quota') || 
                                   errorLower.includes('rate limit') ||
                                   errorLower.includes('api token') ||
                                   errorLower.includes('authentication') ||
                                   errorLower.includes('exceeded') ||
                                   (data.error_code && (data.error_code === 429 || data.error_code === 402));
                
                if (isApifyError) {
                    // Type 1: Apify API error (monthly usage limit, authentication, etc.)
                    errorTitle = 'Service Unavailable';
                    errorMessage = 'The service is not available now. Please try again.';
                    errorSubtext = 'We are experiencing technical difficulties. Please try again later.';
            } else {
                    // Type 2: Username not found (default for all non-Apify errors)
                    errorTitle = 'Search Failed';
                    errorMessage = 'The search failed. No platforms were found for this username.';
                    errorSubtext = 'Please try again with a different username or check your platform selections.';
            }
                
                mainContent.innerHTML = `
                    <div style="max-width: 500px; width: 100%; text-align: center; padding: 40px 24px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">${errorTitle}</h2>
                        <p style="font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">${errorMessage}</p>
                        <p style="font-size: 14px; color: #94a3b8;">${errorSubtext}</p>
                    </div>
                `;
        }
        
            // Show toast notification immediately (only once)
            if (!errorToastShown) {
                // Use the same error message logic for toast
                // Use combined error text from all sources
                let toastMessage = data.error || data.message || '';
                const errorLower = combinedErrorText || toastMessage.toLowerCase();
                const isApifyError = errorLower.includes('apify') || 
                                   errorLower.includes('monthly') || 
                                   errorLower.includes('usage limit') || 
                                   errorLower.includes('quota') || 
                                   errorLower.includes('rate limit') ||
                                   errorLower.includes('api token') ||
                                   errorLower.includes('authentication') ||
                                   (data.error_code && (data.error_code === 429 || data.error_code === 402));
                
                if (isApifyError) {
                    // Type 1: Apify API error
                    toastMessage = 'The service is not available now. Please try again.';
            } else {
                    // Type 2: Username not found
                    toastMessage = 'Search failed. No platforms were found for this username.';
                }
                
                if (typeof showToast === 'function') {
                    showToast(toastMessage, 'error');
                    errorToastShown = true;
                }
            }
            
            // Show second toast message after first toast (with delay to allow first toast to be visible)
            // Use global flag to prevent duplicate second toast
            if (!window.secondToastShown) {
                window.secondToastShown = true; // Set flag immediately to prevent duplicates
                setTimeout(() => {
                    // Reset the toast flag to allow second toast
                    isToastShowing = false;
                    if (typeof showToast === 'function') {
                        showToast('Page will redirect in 5 seconds...', 'error');
                        
                        // After second toast appears, wait 5 seconds then refresh page
                        setTimeout(() => {
                            window.location.reload();
                        }, 5000); // 5 seconds after second toast appears
                    }
                }, 1500); // 1.5 seconds delay to let first toast be visible first
            }
            
            // Don't proceed with AI analysis
                return;
            }
            
        // Store search results globally for platform card clicks
        window.searchResultsData = data;

        // Update platform status grid with search results
        // Filter to only show selected platforms
        const filteredPlatformData = {};
        selectedPlatforms.forEach(platform => {
            if (data.platforms[platform]) {
                filteredPlatformData[platform] = data.platforms[platform];
            }
        });
        
        initializePlatformStatusGrid(filteredPlatformData);
            
        // Check if analysis type is selected and we have found platforms - if so, start analysis
        // Note: hasFoundPlatforms is already declared above (line 2451), reuse it
        const selectedType = document.querySelector('input[name="inlineAnalysisType"]:checked')?.value || window.selectedAnalysisTypeForAnalysis;
            
        if (selectedType && hasFoundPlatforms) {
            // Set up analysis data
            if (!currentAnalysisData) {
                currentAnalysisData = {};
            }
            Object.keys(data.platforms).forEach(p => {
                currentAnalysisData[p] = {
                    found: data.platforms[p].found || false,
                    data: data.platforms[p].data || null,
                    error: data.platforms[p].error || null
                };
            });
            
            // Update prompt details with found platforms (if not already shown)
            const promptDetailsCard = document.getElementById('promptDetailsCard');
            if (promptDetailsCard && promptDetailsCard.style.display === 'block') {
                // Prompt details already shown, just update with found platforms
                showPromptDetails();
            } else {
                // Show prompt details and progress (if not already shown)
                showPromptDetails();
                showAnalysisProgress();
            }
            
            // Start analysis automatically (progress modal should already be showing)
            window.selectedAnalysisTypeForAnalysis = selectedType;
            startAnalysis(selectedType);
        } else {
            // Hide search section after search completes (only if not starting analysis)
        const searchSection = document.getElementById('searchSection');
        if (searchSection) {
            searchSection.style.display = 'none';
        }
        }

        // Don't automatically show results - wait for platform card clicks
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'none';
        }

    } catch (error) {
        console.error('Error:', error);
        
        // Stop progress modal
        const progressCard = document.getElementById('progressCard');
        if (progressCard) {
            progressCard.style.display = 'none';
            // Clear progress intervals
            if (progressCard.dataset.progressInterval) {
                clearInterval(parseInt(progressCard.dataset.progressInterval));
            }
            if (progressCard.dataset.statusInterval) {
                clearInterval(parseInt(progressCard.dataset.statusInterval));
            }
        }
        
        // Show error message in right panel for 10 seconds
        const mainContent = document.querySelector('.cursor-main-content');
        if (mainContent) {
            mainContent.style.display = 'flex';
            mainContent.style.alignItems = 'center';
            mainContent.style.justifyContent = 'center';
            mainContent.style.padding = '24px';
            mainContent.style.background = '#ffffff';
            
            // Determine specific error message based on error type
            let errorMessage = error.message || 'An error occurred while searching. Please try again.';
            let errorTitle = 'Search Failed';
            let errorSubtext = 'Please check your connection and try again.';
            
            // Check if it's an Apify API error
            const errorLower = errorMessage.toLowerCase();
            const isApifyError = errorLower.includes('apify') || 
                               errorLower.includes('monthly') || 
                               errorLower.includes('usage limit') || 
                               errorLower.includes('quota') || 
                               errorLower.includes('rate limit') ||
                               errorLower.includes('api token') ||
                               errorLower.includes('authentication');
            
            if (isApifyError) {
                // Type 1: Apify API error (monthly usage limit, authentication, etc.)
                errorTitle = 'Service Unavailable';
                errorMessage = 'The service is not available now. Please try again.';
                errorSubtext = 'We are experiencing technical difficulties. Please try again later.';
            } else {
                // Type 2: Username not found (default for all non-Apify errors)
                errorTitle = 'Search Failed';
                errorMessage = 'The search failed. No platforms were found for this username.';
                errorSubtext = 'Please try again with a different username or check your platform selections.';
            }
            
            mainContent.innerHTML = `
                <div style="max-width: 500px; width: 100%; text-align: center; padding: 40px 24px;">
                    <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">${errorTitle}</h2>
                    <p style="font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">${errorMessage}</p>
                    <p style="font-size: 14px; color: #94a3b8;">${errorSubtext}</p>
                </div>
            `;
        }
        
        // Show toast notification immediately (only once)
        if (!errorToastShown) {
            // Use the same error message logic for toast
            let toastMessage = error.message || 'An error occurred while searching. Please try again.';
            const errorLower = toastMessage.toLowerCase();
            const isApifyError = errorLower.includes('apify') || 
                               errorLower.includes('monthly') || 
                               errorLower.includes('usage limit') || 
                               errorLower.includes('quota') || 
                               errorLower.includes('rate limit') ||
                               errorLower.includes('api token') ||
                               errorLower.includes('authentication');
            
            if (isApifyError) {
                // Type 1: Apify API error
                toastMessage = 'The service is not available now. Please try again.';
            } else {
                // Type 2: Username not found
                toastMessage = 'Search failed. No platforms were found for this username.';
            }
            
            if (typeof showToast === 'function') {
                showToast(toastMessage, 'error');
                errorToastShown = true;
            }
        }
        
        // Show second toast message after first toast (with delay to allow first toast to be visible)
        // Use global flag to prevent duplicate second toast
        if (!window.secondToastShown) {
            window.secondToastShown = true; // Set flag immediately to prevent duplicates
            setTimeout(() => {
                // Reset the toast flag to allow second toast
                isToastShowing = false;
                if (typeof showToast === 'function') {
                    showToast('Page will redirect in 5 seconds...', 'error');
                    
                    // After second toast appears, wait 5 seconds then refresh page
                    setTimeout(() => {
                        window.location.reload();
                    }, 5000); // 5 seconds after second toast appears
                }
            }, 1500); // 1.5 seconds delay to let first toast be visible first
        }
    } finally {
        // Reset search in progress flag
        isSearchInProgress = false;
        
        if (searchButton) {
            searchButton.disabled = false;
            searchButton.textContent = 'Generate';
        }
        // Reset proceed flag after search completes
        proceedWithSearchFlag = false;
    }
}

document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    e.stopPropagation(); // Stop event from bubbling
    e.stopImmediatePropagation(); // Stop other handlers from running
    
    // Declare variables once at the top of the function
    const searchButton = document.getElementById('searchButton');
    const platformStatus = document.getElementById('platformStatus');
    const resultsContainer = document.getElementById('resultsContainer');
    const username = document.getElementById('username').value.trim();
    
    if (!username) {
        return false; // Stop execution
    }

    // ===== CHECK FIRST - BEFORE ANY OTHER LOGIC =====
    const notification = document.getElementById('existingDataNotification');
    const isNotificationVisible = notification && 
                                   notification.style.display !== 'none' && 
                                   getComputedStyle(notification).display !== 'none';
    
    // FIRST CHECK: If notification is visible and user hasn't made a choice, BLOCK IMMEDIATELY
    if (isNotificationVisible && existingDataInfo && !useExistingDataFlag && !proceedWithSearchFlag) {
        console.log('BLOCKING SEARCH: Notification visible, user has not chosen option - BLOCKING BEFORE ANY SEARCH');
        // Hide platform status section
        if (platformStatus) {
            platformStatus.style.display = 'none';
        }
        // Reset button state
        if (searchButton) {
            searchButton.disabled = false;
            searchButton.textContent = 'Generate';
        }
        // Stop here - don't proceed, don't make backend call
        return false; // Stop execution completely - NO BACKEND CALL
    }
    
    // SECOND CHECK: If existing data found but proceed flag not set, block
    if (!proceedWithSearchFlag && existingDataInfo) {
        console.log('BLOCKING SEARCH: Existing data found but proceed flag not set - BLOCKING BEFORE ANY SEARCH');
        // Reset button state
        if (searchButton) {
            searchButton.disabled = false;
            searchButton.textContent = 'Generate';
        }
        // Make sure platform status is hidden
        if (platformStatus) {
            platformStatus.style.display = 'none';
        }
        return false; // Stop execution completely - NO BACKEND CALL
    }
    
    // THIRD CHECK: If using existing data, skip search
    if (useExistingDataFlag && existingDataInfo) {
        console.log('BLOCKING SEARCH: Using existing data flag set');
        // Data already loaded by useExistingData(), just return
        return false; // Stop execution - no backend call
    }
    
    // ===== ONLY REACH HERE IF ALL CHECKS PASS =====
    console.log('ALL CHECKS PASSED - Proceeding with search');
    
    // Check if search is already in progress
    if (isSearchInProgress) {
        console.log('BLOCKING SEARCH: Search already in progress');
        return false;
    }
    
    // Reset proceed flag after check
    proceedWithSearchFlag = false;

    // Reset UI for new search
    searchButton.disabled = true;
    searchButton.textContent = 'Searching...';
    if (platformStatus) {
        platformStatus.style.display = 'block';
        
        // Show only selected platforms, hide others
        const allPlatforms = ['facebook', 'instagram', 'tiktok', 'twitter'];
        allPlatforms.forEach(platform => {
            const statusEl = document.getElementById(`status-${platform}`);
            if (statusEl) {
                if (selectedPlatforms.includes(platform)) {
                    statusEl.style.display = 'flex';
                    statusEl.querySelector('.status-text').textContent = 'Searching...';
                    statusEl.style.background = '#f8fafc';
                    statusEl.style.borderColor = '#e2e8f0';
                } else {
                    statusEl.style.display = 'none';
                }
            }
        });
    }
    if (resultsContainer) {
        resultsContainer.style.display = 'none';
        resultsContainer.innerHTML = '';
    }
    
    // Reset all platform statuses
    ['facebook', 'instagram', 'tiktok', 'twitter'].forEach(platform => {
        const statusEl = document.getElementById(`status-${platform}`);
        if (statusEl) {
            statusEl.querySelector('.status-text').textContent = 'Searching...';
            statusEl.style.background = '#f8fafc';
            statusEl.style.borderColor = '#e2e8f0';
        }
    });

    // Perform the search (only reaches here if all checks pass)
    // Double-check if search is already in progress (might have been started by checkBeforeSearch)
    if (isSearchInProgress) {
        console.log('BLOCKING SEARCH: Search already in progress (from form submit)');
        return false;
    }
    
    console.log('PROCEEDING WITH SEARCH - All checks passed');
    await performSearch();
    return false; // Prevent any default form submission
});

// Store current displayed platform
let currentDisplayedPlatform = null;
// Store analysis result HTML to restore when toggling back
window.storedAnalysisResultHtml = null;

// Function to clear platform data from right panel
function clearPlatformDataFromRightPanel() {
    const rightPanelContent = document.querySelector('.cursor-main-content');
    const rightPanelMain = document.querySelector('.cursor-main');
    const animatedBackground = document.getElementById('animatedBackground');
    const didYouKnowSection = document.getElementById('didYouKnowSection');
    const resultsContainer = document.getElementById('analysisResultsContainer');
    
    // Clear platform results container (for "Use Previous Data" scenario)
    if (rightPanelContent) {
        const platformResultsContainer = rightPanelContent.querySelector('#platformResultsContainer');
        if (platformResultsContainer) {
            platformResultsContainer.remove();
        }
    }
    
    // Restore analysis results if they were replaced with platform data
    if (resultsContainer && window.storedAnalysisResultHtml) {
        resultsContainer.innerHTML = window.storedAnalysisResultHtml;
    }
    
    // Reset right panel layout
    if (rightPanelContent) {
        rightPanelContent.style.alignItems = 'center';
        rightPanelContent.style.justifyContent = 'center';
        rightPanelContent.classList.remove('scrollable');
    }
    
    // Show animated background again (if not in analysis progress)
    const progressCard = document.getElementById('progressCard');
    if (progressCard && progressCard.style.display === 'none') {
        if (rightPanelMain) {
            rightPanelMain.classList.add('animated-ai-background');
        }
        if (animatedBackground) {
            animatedBackground.style.display = 'block';
        }
        if (didYouKnowSection) {
            didYouKnowSection.style.display = 'block';
        }
    }
    
    // Reset current displayed platform
    currentDisplayedPlatform = null;
    
    // Remove active styling from platform boxes in Analysis Input Details
    const allPlatformBoxes = document.querySelectorAll('#promptDetailsPlatforms > div');
    allPlatformBoxes.forEach(box => {
        box.classList.remove('active');
        if (!box.style.pointerEvents || box.style.pointerEvents === 'auto') {
            box.style.borderColor = '#86efac';
            box.style.background = '#f0fdf4';
        }
    });
}

// Toggle platform view in Analysis Input Details
function togglePlatformViewInAnalysis(platformKey, platformElement) {
    const resultsContainer = document.getElementById('analysisResultsContainer');
    if (!resultsContainer) return;
    
    // Check if this platform is already active
    const isActive = platformElement.classList.contains('active');
    
    // Remove active state from all platform boxes
    const allPlatformBoxes = document.querySelectorAll('#promptDetailsPlatforms > div');
    allPlatformBoxes.forEach(box => {
        box.classList.remove('active');
        box.style.borderColor = '#86efac';
        box.style.background = '#f0fdf4';
    });
    
    if (isActive) {
        // Restore analysis result
        if (window.storedAnalysisResultHtml) {
            resultsContainer.innerHTML = window.storedAnalysisResultHtml;
            // Re-initialize tooltips for radar charts
            setTimeout(() => {
                if (typeof initializeAllRadarChartTooltips === 'function') {
                    initializeAllRadarChartTooltips();
                }
            }, 100);
        }
        currentDisplayedPlatform = null;
    } else {
        // Store current analysis result HTML if not already stored
        if (!window.storedAnalysisResultHtml) {
            window.storedAnalysisResultHtml = resultsContainer.innerHTML;
        }
        
        // Show platform data
        showPlatformResults(platformKey, true);
        
        // Mark this platform as active
        platformElement.classList.add('active');
        platformElement.style.borderColor = '#667eea';
        platformElement.style.background = '#f8faff';
    }
}

function showPlatformResults(platform, isInAnalysisDetails = false) {
    if (!window.searchResultsData) {
        return;
    }
    
    const data = window.searchResultsData;
    const platformData = data.platforms[platform];
    const rightPanelContent = document.querySelector('.cursor-main-content');
    const didYouKnowSection = document.getElementById('didYouKnowSection');
    
    // Don't show results if platform not found
    if (!platformData || !platformData.found) {
        return;
    }
    
    // Get the right panel main container
    const rightPanelMain = document.querySelector('.cursor-main');
    const animatedBackground = document.getElementById('animatedBackground');
    
    // If in Analysis Input Details mode, handle differently
    if (isInAnalysisDetails) {
        const resultsContainer = document.getElementById('analysisResultsContainer');
        if (resultsContainer) {
            // Generate platform card HTML
            const platformNames = {
                'facebook': 'Facebook',
                'instagram': 'Instagram',
                'tiktok': 'TikTok',
                'twitter': 'X (Twitter)'
            };
            
            let html = '';
            if (platformData.data) {
                html = generatePlatformCard(platformNames[platform] || platform, platform, platformData.data, platform);
            }
            
            resultsContainer.innerHTML = html;
            
            // Re-initialize tooltips after displaying platform data
            setTimeout(() => {
                if (typeof window.attachTooltipListeners === 'function') {
                    window.attachTooltipListeners();
                }
            }, 100);
        }
        currentDisplayedPlatform = platform;
        return;
    }
    
    // Original behavior for non-Analysis Details mode
    // If clicking the same platform, toggle it off (show "Did You Know" again)
    if (currentDisplayedPlatform === platform && didYouKnowSection && didYouKnowSection.style.display === 'none') {
        didYouKnowSection.style.display = 'block';
        // Remove results container if it exists
        const resultsContainer = rightPanelContent.querySelector('#platformResultsContainer');
        if (resultsContainer) {
            resultsContainer.remove();
        }
        // Reset right panel layout to centered
        if (rightPanelContent) {
            rightPanelContent.style.alignItems = 'center';
            rightPanelContent.style.justifyContent = 'center';
            rightPanelContent.classList.remove('scrollable');
        }
        // Show animated background again
        if (rightPanelMain) {
            rightPanelMain.classList.add('animated-ai-background');
        }
        if (animatedBackground) {
            animatedBackground.style.display = 'block';
        }
        currentDisplayedPlatform = null;
        return;
    }
    
    // Hide "Did You Know" section
    if (didYouKnowSection) {
        didYouKnowSection.style.display = 'none';
    }
    
    // Hide animated background
    if (rightPanelMain) {
        rightPanelMain.classList.remove('animated-ai-background');
        rightPanelMain.style.background = '#ffffff';
    }
    if (animatedBackground) {
        animatedBackground.style.display = 'none';
    }
    
    // Remove existing results container if it exists
    const existingResults = rightPanelContent.querySelector('#platformResultsContainer');
    if (existingResults) {
        existingResults.remove();
    }
    
    // Update right panel to show platform results
    if (rightPanelContent) {
        // Make right panel scrollable
        rightPanelContent.classList.add('scrollable');
        
        // Change layout for results display
        rightPanelContent.style.alignItems = 'flex-start';
        rightPanelContent.style.justifyContent = 'flex-start';
        
        // Create results container
        const resultsContainer = document.createElement('div');
        resultsContainer.id = 'platformResultsContainer';
        resultsContainer.style.cssText = 'width: 100%; max-width: 100%;';
        
        // Show platform results (without "Results for:" text)
        let html = '';
    
    const platformNames = {
        'facebook': 'Facebook',
        'instagram': 'Instagram',
        'tiktok': 'TikTok',
        'twitter': 'X (Twitter)'
    };
    
    if (platformData.data) {
        html += generatePlatformCard(platformNames[platform], platform, platformData.data, platform);
    }
    
    resultsContainer.innerHTML = html;
        rightPanelContent.appendChild(resultsContainer);
    currentDisplayedPlatform = platform;
    
    // Initialize tooltips after results are displayed
        // Re-attach tooltip listeners after dynamic content is loaded
        setTimeout(function() {
            if (typeof window.attachTooltipListeners === 'function') {
                window.attachTooltipListeners();
            }
        }, 100);
    }
}

function displayResults(data) {
    let html = '<h3 style="font-size: 18px; font-weight: 600; color: #374151; margin-bottom: 20px;">Results for: <strong>' + data.username + '</strong></h3>';
    
    // Facebook
    if (data.platforms.facebook.found && data.platforms.facebook.data) {
        html += generatePlatformCard('Facebook', 'facebook', data.platforms.facebook.data, 'facebook');
    }
    
    // Instagram
    if (data.platforms.instagram.found && data.platforms.instagram.data) {
        html += generatePlatformCard('Instagram', 'instagram', data.platforms.instagram.data, 'instagram');
    }
    
    // TikTok
    if (data.platforms.tiktok.found && data.platforms.tiktok.data) {
        html += generatePlatformCard('TikTok', 'tiktok', data.platforms.tiktok.data, 'tiktok');
    }
    
    // Twitter/X
    if (data.platforms.twitter && data.platforms.twitter.found && data.platforms.twitter.data) {
        html += generatePlatformCard('X (Twitter)', 'twitter', data.platforms.twitter.data, 'twitter');
    }
    
    document.getElementById('resultsContainer').innerHTML = html;
}

function getPlatformIcon(platformType) {
    if (platformType === 'facebook') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>`;
    } else if (platformType === 'instagram') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px;"><defs><linearGradient id="instagram-gradient-result" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-result)"/></svg>`;
    } else if (platformType === 'tiktok') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>`;
    } else if (platformType === 'twitter') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`;
    }
    return '';
}

function generatePlatformCard(platformName, platformType, data, type) {
    let html = `<div style="margin-bottom: 20px;">`;
    html += `<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0;">`;
    html += `<div style="display: flex; align-items: center; justify-content: center;">${getPlatformIcon(platformType)}</div>`;
    html += `<div style="flex: 1;"><h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">${platformName}</h2>`;
    if (data.username) html += `<p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">@${data.username}</p>`;
    html += `</div></div>`;
    
    // Profile URL
    const profileUrl = data.profile_url || data.link || (data.username ? 
        (platformType === 'facebook' ? `https://www.facebook.com/${data.username}` :
         platformType === 'instagram' ? `https://www.instagram.com/${data.username}/` :
         platformType === 'tiktok' ? `https://www.tiktok.com/@${data.username}` :
         platformType === 'twitter' ? `https://twitter.com/${data.username}` : null) : null);
    
    if (profileUrl) {
        html += `<div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">`;
        html += `<a href="${profileUrl}" target="_blank" rel="noopener noreferrer" style="color: #667eea; text-decoration: none; font-size: 14px; word-break: break-all; display: inline-flex; align-items: center; gap: 6px;">`;
        html += `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>`;
        html += `<span>${profileUrl}</span>`;
        html += `</a></div>`;
    }
    
    // Profile info (skip for Instagram, TikTok, and Twitter to match Facebook layout)
    if (platformType !== 'instagram' && platformType !== 'tiktok' && platformType !== 'twitter') {
        if (data.profile_picture) {
            html += `<div style="margin-bottom: 20px;"><img src="${data.profile_picture}" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; border: 2px solid #e2e8f0;"></div>`;
        }
        
        if (data.name) {
            html += `<h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px 0;">${data.name}</h3>`;
        }
        
        if (data.bio || data.biography || data.about || data.description) {
            const bio = data.bio || data.biography || data.about || data.description;
            html += `<p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">${bio}</p>`;
        }
    }
    
    // Basic Stats - removed to match Facebook/Instagram layout (empty between URL and engagement metrics)
    
    // Engagement Metrics
    if (data.engagement) {
        html += `<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 24px; margin-bottom: 20px; color: white;">`;
        html += `<h3 style="font-size: 18px; font-weight: 700; margin: 0 0 20px 0; color: white;">Engagement Metrics</h3>`;
        html += `<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 24px;">`;
        
        if (data.engagement.engagement_rate !== undefined) {
            html += `<span class="info-tooltip" data-tooltip="Engagement Rate: The percentage of followers who interact with posts (likes + comments + shares) relative to total followers. Higher rates indicate better audience engagement."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Engagement Rate</div><div style="font-size: 24px; font-weight: 700;">${data.engagement.engagement_rate.toFixed(2)}%</div></div></span>`;
        }
        if (data.engagement.average_engagement_per_post !== undefined) {
            html += `<span class="info-tooltip" data-tooltip="Average Engagement per Post: The average number of total interactions (likes + comments + shares) each post receives. Shows how engaging your content is on average."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Engagement/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_engagement_per_post)}</div></div></span>`;
        }
        if (data.engagement.total_engagement !== undefined) {
            html += `<span class="info-tooltip" data-tooltip="Total Engagement: The sum of all interactions (likes + comments + shares) across all analyzed posts. Represents overall audience activity."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Total Engagement</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.total_engagement)}</div></div></span>`;
        }
        if (data.engagement.average_likes !== undefined) {
            html += `<span class="info-tooltip" data-tooltip="Average Likes per Post: The average number of likes each post receives. Indicates how much your audience appreciates your content."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Likes/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_likes)}</div></div></span>`;
        }
        if (data.engagement.average_comments !== undefined) {
            html += `<span class="info-tooltip" data-tooltip="Average Comments per Post: The average number of comments each post receives. Higher values indicate more active discussions and deeper engagement."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Comments/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_comments)}</div></div></span>`;
        }
        if (data.engagement.average_shares !== undefined) {
            html += `<span class="info-tooltip" data-tooltip="Average Shares per Post: The average number of times each post is shared. Shares indicate high-value content that audiences want to share with others."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Avg Shares/Post</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.average_shares)}</div></div></span>`;
        }
        if (data.engagement.total_posts_analyzed !== undefined) {
            html += `<span class="info-tooltip" data-tooltip="Recent Posts Analyzed: The total number of recent posts included in this analysis. More posts provide a more accurate representation of engagement patterns."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">Recent Posts Analyzed</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(data.engagement.total_posts_analyzed)}</div></div></span>`;
        }
        
        // Add Posts count card to engagement metrics
        const postsCount = data.stats?.total_media || data.stats?.total_videos || data.stats?.total_tweets || data.stats?.recent_posts_count || 0;
        if (postsCount > 0) {
            const postsLabel = data.stats?.total_media ? 'Recent Posts' : (data.stats?.total_videos ? 'Recent Videos' : (data.stats?.recent_posts_count ? 'Recent Posts' : 'Recent Tweets'));
            html += `<span class="info-tooltip" data-tooltip="Recent ${postsLabel.replace('Recent ', '')}: The number of recent ${postsLabel.toLowerCase().replace('recent ', '')} fetched from this account. This represents the recent content volume."><div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); cursor: help;"><div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">${postsLabel}</div><div style="font-size: 24px; font-weight: 700;">${formatNumber(postsCount)}</div></div></span>`;
        }
        
        html += `</div>`;
        
        // Add posts inside engagement metrics section - scrollable, no pagination
        const posts = data.recent_posts || data.recent_media || data.recent_videos || [];
        if (posts.length > 0) {
            html += `<div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.2);">`;
            html += `<h4 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0; color: white;">Recent Posts (${posts.length} total)</h4>`;
            html += `<div style="max-height: 500px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; padding-right: 8px;">`;
            
            // Render all posts in a scrollable container
            posts.forEach((post) => {
                html += `<div style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); flex-shrink: 0;">`;
                
                if (post.message || post.text || post.caption || post.description) {
                    const content = post.message || post.text || post.caption || post.description;
                    html += `<p style="color: white; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0; opacity: 0.95;">${content.substring(0, 200)}${content.length > 200 ? '...' : ''}</p>`;
                }
                
                if (post.created_time || post.timestamp) {
                    const date = new Date(post.created_time || post.timestamp);
                    html += `<div style="display: flex; gap: 16px; font-size: 12px; color: rgba(255,255,255,0.8); flex-wrap: wrap; margin-bottom: 8px;">`;
                    html += `<span>📅 ${date.toLocaleDateString()}</span>`;
                } else {
                    html += `<div style="display: flex; gap: 16px; font-size: 12px; color: rgba(255,255,255,0.8); flex-wrap: wrap; margin-bottom: 8px;">`;
                }
                
                if (post.likes || post.like_count) {
                    html += `<span>👍 ${formatNumber(post.likes || post.like_count || 0)}</span>`;
                }
                if (post.comments || post.comments_count) {
                    html += `<span>💬 ${formatNumber(post.comments || post.comments_count || 0)}</span>`;
                }
                if (post.shares || post.share_count) {
                    html += `<span>📤 ${formatNumber(post.shares || post.share_count || 0)}</span>`;
                }
                if (post.views || post.view_count) {
                    html += `<span>👁️ ${formatNumber(post.views || post.view_count || 0)}</span>`;
                }
                if (post.total_engagement) {
                    html += `<span style="font-weight: 600; opacity: 1;">Total: ${formatNumber(post.total_engagement)}</span>`;
                }
                html += `</div>`;
                
                if (post.url || post.permalink) {
                    html += `<a href="${post.url || post.permalink}" target="_blank" style="display: inline-block; margin-top: 8px; color: rgba(255,255,255,0.9); text-decoration: underline; font-size: 12px; font-weight: 500;">View Post →</a>`;
                }
                
                html += `</div>`;
            });
            
            html += `</div>`;
            html += `</div>`;
        }
        
        html += `</div>`;
    }
    
    html += `</div>`;
    return html;
}

function generatePostsSection(posts, title) {
    let html = `<div style="margin-bottom: 20px;">`;
    html += `<h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 16px 0;">${title}</h4>`;
    html += `<div style="display: grid; gap: 12px;">`;
    
    posts.forEach(post => {
        html += `<div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">`;
        
        if (post.message || post.text || post.caption || post.description) {
            const content = post.message || post.text || post.caption || post.description;
            html += `<p style="color: #1e293b; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">${content.substring(0, 200)}${content.length > 200 ? '...' : ''}</p>`;
        }
        
        if (post.created_time || post.timestamp) {
            const date = new Date(post.created_time || post.timestamp);
            html += `<div style="display: flex; gap: 16px; font-size: 12px; color: #64748b; flex-wrap: wrap; margin-bottom: 8px;">`;
            html += `<span>📅 ${date.toLocaleDateString()}</span>`;
        } else {
            html += `<div style="display: flex; gap: 16px; font-size: 12px; color: #64748b; flex-wrap: wrap; margin-bottom: 8px;">`;
        }
        
        if (post.likes || post.like_count) {
            html += `<span>👍 ${formatNumber(post.likes || post.like_count || 0)}</span>`;
        }
        if (post.comments || post.comments_count) {
            html += `<span>💬 ${formatNumber(post.comments || post.comments_count || 0)}</span>`;
        }
        if (post.shares || post.share_count) {
            html += `<span>📤 ${formatNumber(post.shares || post.share_count || 0)}</span>`;
        }
        if (post.views || post.view_count) {
            html += `<span>👁️ ${formatNumber(post.views || post.view_count || 0)}</span>`;
        }
        if (post.total_engagement) {
            html += `<span style="font-weight: 600; color: #667eea;">Total: ${formatNumber(post.total_engagement)}</span>`;
        }
        html += `</div>`;
        
        if (post.url || post.permalink) {
            html += `<a href="${post.url || post.permalink}" target="_blank" style="display: inline-block; margin-top: 8px; color: #667eea; text-decoration: none; font-size: 12px; font-weight: 500;">View Post →</a>`;
        }
        
        html += `</div>`;
    });
    
    html += `</div></div>`;
    return html;
}

function formatNumber(num) {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
}

// Clear results function
function clearResults() {
    // Clear results container
    const resultsContainer = document.getElementById('resultsContainer');
    if (resultsContainer) {
        resultsContainer.innerHTML = '';
        resultsContainer.style.display = 'none';
    }
    
    // Show "Did You Know" section again in right panel
    const didYouKnowSection = document.getElementById('didYouKnowSection');
    if (didYouKnowSection) {
        didYouKnowSection.style.display = 'block';
    }
    
    // Remove platform results from right panel
    const rightPanelContent = document.querySelector('.cursor-main-content');
    const rightPanelMain = document.querySelector('.cursor-main');
    const animatedBackground = document.getElementById('animatedBackground');
    
    if (rightPanelContent) {
        const platformResultsContainer = rightPanelContent.querySelector('#platformResultsContainer');
        if (platformResultsContainer) {
            platformResultsContainer.remove();
        }
        // Reset right panel layout to centered
        rightPanelContent.style.alignItems = 'center';
        rightPanelContent.style.justifyContent = 'center';
        rightPanelContent.classList.remove('scrollable');
    }
    
    // Show animated background again
    if (rightPanelMain) {
        rightPanelMain.classList.add('animated-ai-background');
    }
    if (animatedBackground) {
        animatedBackground.style.display = 'block';
    }
    
    // Clear stored search results
    window.searchResultsData = null;
    currentDisplayedPlatform = null;
    existingDataInfo = null;
    useExistingDataFlag = false;
    
    // Hide existing data notification
    const notification = document.getElementById('existingDataNotification');
    if (notification) {
        notification.style.display = 'none';
    }
    
    // Hide platform status
    const platformStatus = document.getElementById('platformStatus');
    if (platformStatus) {
        platformStatus.style.display = 'none';
    }
    
    // Reset platform statuses
    ['facebook', 'instagram', 'tiktok', 'twitter'].forEach(platform => {
        const statusEl = document.getElementById(`status-${platform}`);
        if (statusEl) {
            statusEl.querySelector('.status-text').textContent = 'Searching...';
            statusEl.style.background = '#f8fafc';
            statusEl.style.borderColor = '#e2e8f0';
            statusEl.style.cursor = 'pointer';
            // Restore onclick handler
            statusEl.setAttribute('onclick', `showPlatformResults('${platform}')`);
        }
    });
    
    // Clear username input
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.value = '';
    }
    
    // Show search section again
    const searchSection = document.getElementById('searchSection');
    if (searchSection) {
        searchSection.style.display = 'block';
    }
    
    // Make sure search button is visible
    const searchButton = document.getElementById('searchButton');
    if (searchButton) {
        searchButton.style.display = 'block';
        searchButton.disabled = false;
        searchButton.textContent = 'Generate';
    }
    
    // Make sure platform text is visible
    const platformText = document.querySelector('#searchForm p');
    if (platformText) {
        platformText.style.display = 'block';
    }
    
    // Reset proceed flag
    proceedWithSearchFlag = false;
    isCheckingExistingData = false;
}

// Pagination function for posts
function changePostsPage(uniqueId, direction, postsPerPage, totalPosts) {
    const currentPage = window[`${uniqueId}_page`] || 1;
    const totalPages = Math.ceil(totalPosts / postsPerPage);
    let newPage = currentPage;
    
    if (direction === 0) {
        // Previous
        newPage = Math.max(1, currentPage - 1);
    } else {
        // Next
        newPage = Math.min(totalPages, currentPage + 1);
    }
    
    if (newPage === currentPage) return;
    
    window[`${uniqueId}_page`] = newPage;
    
    // Hide all posts
    const posts = document.querySelectorAll(`.post-item-${uniqueId}`);
    posts.forEach(post => {
        post.style.display = 'none';
    });
    
    // Show posts for current page
    const startIndex = (newPage - 1) * postsPerPage;
    const endIndex = startIndex + postsPerPage;
    
    for (let i = startIndex; i < endIndex && i < posts.length; i++) {
        if (posts[i]) {
            posts[i].style.display = 'block';
        }
    }
    
    // Update pagination controls
    const prevBtn = document.getElementById(`${uniqueId}-prev`);
    const nextBtn = document.getElementById(`${uniqueId}-next`);
    const currentSpan = document.getElementById(`${uniqueId}-current`);
    
    if (prevBtn) prevBtn.disabled = newPage === 1;
    if (nextBtn) nextBtn.disabled = newPage === totalPages;
    if (currentSpan) currentSpan.textContent = newPage;
    
    // Scroll to top of posts container
    const container = document.getElementById(`${uniqueId}-container`);
    if (container) {
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Initialize tooltips for mobile support (matching create predictions page)
function initTooltips() {
    const tooltips = document.querySelectorAll('.info-tooltip');
    // Remove existing listeners to avoid duplicates
    tooltips.forEach(tooltip => {
        const newTooltip = tooltip.cloneNode(true);
        tooltip.parentNode.replaceChild(newTooltip, tooltip);
    });
    
    // Re-query after cloning
    const freshTooltips = document.querySelectorAll('.info-tooltip');
    freshTooltips.forEach(tooltip => {
        tooltip.addEventListener('click', function(e) {
            // Only handle click on mobile devices
            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation();
                // Toggle active class
                const isActive = this.classList.contains('active');
                // Remove active from all tooltips
                freshTooltips.forEach(t => t.classList.remove('active'));
                // Toggle current tooltip
                if (!isActive) {
                    this.classList.add('active');
                }
            }
        });
    });
}

// Note: Tooltip initialization is now handled in the main DOMContentLoaded handler above
// This prevents duplicate event listeners and ensures hover functionality works correctly

// Analysis Modal Functions
let currentAnalysisData = null;
let currentPlatform = null;
let selectedAnalysisPlatforms = {}; // Store selected platforms for analysis

function openAnalysisModal(platform, dataIdOrData) {
    currentPlatform = platform;
    
    // Always use ALL platform data from searchResultsData if available
    // This ensures we store complete data from all platforms (Facebook, Instagram, TikTok)
    if (window.searchResultsData && window.searchResultsData.platforms) {
        // Use the complete platform data structure from searchAll
        currentAnalysisData = {};
        Object.keys(window.searchResultsData.platforms).forEach(p => {
            // Store all platforms, whether found or not, to preserve complete search results
            currentAnalysisData[p] = {
                found: window.searchResultsData.platforms[p].found || false,
                data: window.searchResultsData.platforms[p].data || null,
                error: window.searchResultsData.platforms[p].error || null
            };
        });
    } else {
        // Fallback: if no searchResultsData, use the single platform data
        let platformData;
        if (typeof dataIdOrData === 'string' && window.analysisDataStore && window.analysisDataStore[dataIdOrData]) {
            platformData = window.analysisDataStore[dataIdOrData];
        } else {
            platformData = dataIdOrData;
        }
        
        currentAnalysisData = { [platform]: { found: true, data: platformData } };
    }
    
    // If analysis type is already selected, start analysis directly
    if (window.selectedAnalysisTypeForAnalysis) {
        startAnalysis(window.selectedAnalysisTypeForAnalysis);
        return;
    }
    
    // Show modal with platform selection (fallback for old flow)
    const modal = document.getElementById('analysisModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Show platform selection UI
        showPlatformSelection();
    }
}

function showPlatformSelection() {
    const modalContent = document.getElementById('analysisModalContent');
    if (!modalContent || !currentAnalysisData) return;
    
    // Get available platforms (only those that were found and have data)
    const availablePlatforms = [];
    const platformNames = {
        'facebook': 'Facebook',
        'instagram': 'Instagram',
        'tiktok': 'TikTok',
        'twitter': 'X (Twitter)'
    };
    
    Object.keys(currentAnalysisData).forEach(platform => {
        if (currentAnalysisData[platform].found && currentAnalysisData[platform].data) {
            availablePlatforms.push({
                key: platform,
                name: platformNames[platform] || platform.charAt(0).toUpperCase() + platform.slice(1)
            });
        }
    });
    
    // If no platforms available, show error
    if (availablePlatforms.length === 0) {
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px 20px;">
                <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">No Platforms Available</h3>
                <p style="color: #64748b; margin-bottom: 24px;">No platform data found to analyze. Please search for platforms first.</p>
                <button onclick="closeAnalysisModal()" style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease;">Close</button>
            </div>
        `;
        return;
    }
    
    // Initialize selected platforms - all selected by default
    selectedAnalysisPlatforms = {};
    availablePlatforms.forEach(platform => {
        selectedAnalysisPlatforms[platform.key] = true;
    });
    
    // Build platform selection UI
    let html = `
        <div style="padding: 32px 24px;">
            <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 8px; text-align: center;">Select Platforms for Analysis</h3>
            <p style="color: #64748b; margin-bottom: 24px; text-align: center; font-size: 14px;">Choose which platforms to include in the NUJUM analysis</p>
            
            <!-- Analysis Type Selection -->
            <div style="margin-bottom: 32px;">
                <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 12px; font-size: 15px;">Analysis Type</label>
                <div style="display: flex; gap: 12px;">
                    <div style="flex: 1; padding: 16px; background: #ffffff; border: 2px solid #667eea; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
                         onclick="selectAnalysisType('professional')" 
                         id="analysisTypeProfessional"
                         onmouseover="if(window.selectedAnalysisType !== 'professional') this.style.borderColor='#9ca3af';" 
                         onmouseout="if(window.selectedAnalysisType !== 'professional') this.style.borderColor='#e2e8f0';">
                        <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                            <input type="radio" name="analysisType" id="analysisType_professional" value="professional" checked onchange="selectAnalysisType('professional')" 
                                   style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #1e293b; font-size: 15px; margin-bottom: 4px;">Professional</div>
                                <div style="font-size: 13px; color: #64748b;">For recruitment and hiring evaluation</div>
                            </div>
                        </label>
                    </div>
                    <div style="flex: 1; padding: 16px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
                         onclick="selectAnalysisType('political')" 
                         id="analysisTypePolitical"
                         onmouseover="if(window.selectedAnalysisType !== 'political') this.style.borderColor='#9ca3af';" 
                         onmouseout="if(window.selectedAnalysisType !== 'political') this.style.borderColor='#e2e8f0';">
                        <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                            <input type="radio" name="analysisType" id="analysisType_political" value="political" onchange="selectAnalysisType('political')" 
                                   style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #1e293b; font-size: 15px; margin-bottom: 4px;">Political</div>
                                <div style="font-size: 13px; color: #64748b;">For political profile and campaign analysis</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
    `;
    
    // Add "Select All" option
    html += `
        <div style="padding: 16px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
             onclick="toggleSelectAll()" 
             onmouseover="this.style.borderColor='#667eea'; this.style.background='#f0f4ff';" 
             onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
            <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                <input type="checkbox" id="selectAllCheckbox" checked onchange="toggleSelectAll()" 
                       style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                <span style="font-weight: 600; color: #1e293b; font-size: 15px;">Select All Platforms</span>
            </label>
        </div>
    `;
    
    // Add individual platform checkboxes
    availablePlatforms.forEach(platform => {
        let platformIconSVG = '';
        if (platform.key === 'facebook') {
            platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
        } else if (platform.key === 'instagram') {
            platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px;"><defs><linearGradient id="instagram-gradient-select" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-select)"/></svg>';
        } else if (platform.key === 'tiktok') {
            platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>';
        } else if (platform.key === 'twitter') {
            platformIconSVG = '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
        }
        
        html += `
            <div style="padding: 16px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;" 
                 onclick="togglePlatform('${platform.key}')" 
                 onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.1)';" 
                 onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="platform_${platform.key}" checked onchange="togglePlatform('${platform.key}')" 
                           style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                    <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">${platformIconSVG}</div>
                    <span style="font-weight: 600; color: #1e293b; font-size: 15px; flex: 1;">${platform.name}</span>
                </label>
            </div>
        `;
    });
    
    html += `
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 24px;">
                <button onclick="closeAnalysisModal()" 
                        style="padding: 12px 24px; background: transparent; color: #64748b; border: 2px solid #d1d5db; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease;"
                        onmouseover="this.style.borderColor='#9ca3af'; this.style.color='#374151';"
                        onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#64748b';">
                    Cancel
                </button>
                <button onclick="proceedWithAnalysis()" 
                        style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                        onmouseover="this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)';"
                        onmouseout="this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)';">
                    Start Analysis
                </button>
            </div>
        </div>
    `;
    
    modalContent.innerHTML = html;
    
    // Initialize analysis type selection
    window.selectedAnalysisType = 'professional';
    selectAnalysisType('professional');
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const isChecked = selectAllCheckbox.checked;
    
    // Update all platform checkboxes
    Object.keys(selectedAnalysisPlatforms).forEach(platform => {
        selectedAnalysisPlatforms[platform] = isChecked;
        const checkbox = document.getElementById(`platform_${platform}`);
        if (checkbox) {
            checkbox.checked = isChecked;
        }
    });
}

function togglePlatform(platform) {
    const checkbox = document.getElementById(`platform_${platform}`);
    if (checkbox) {
        selectedAnalysisPlatforms[platform] = checkbox.checked;
        
        // Update "Select All" checkbox based on individual selections
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox) {
            const allSelected = Object.values(selectedAnalysisPlatforms).every(selected => selected);
            selectAllCheckbox.checked = allSelected;
        }
    }
}

function selectAnalysisType(type) {
    window.selectedAnalysisType = type;
    
    // Update radio button
    const professionalRadio = document.getElementById('analysisType_professional');
    const politicalRadio = document.getElementById('analysisType_political');
    if (professionalRadio) professionalRadio.checked = (type === 'professional');
    if (politicalRadio) politicalRadio.checked = (type === 'political');
    
    // Update visual styling for selected analysis type
    const professionalDiv = document.getElementById('analysisTypeProfessional');
    const politicalDiv = document.getElementById('analysisTypePolitical');
    const modalTitle = document.getElementById('analysisModalTitle');
    
    if (professionalDiv && politicalDiv) {
        if (type === 'professional') {
            professionalDiv.style.borderColor = '#667eea';
            professionalDiv.style.background = '#f0f4ff';
            politicalDiv.style.borderColor = '#e2e8f0';
            politicalDiv.style.background = '#ffffff';
            if (modalTitle) modalTitle.textContent = 'NUJUM Professional Analysis';
        } else {
            politicalDiv.style.borderColor = '#667eea';
            politicalDiv.style.background = '#f0f4ff';
            professionalDiv.style.borderColor = '#e2e8f0';
            professionalDiv.style.background = '#ffffff';
            if (modalTitle) modalTitle.textContent = 'NUJUM Political Analysis';
        }
    }
}

// Initialize selected analysis type
window.selectedAnalysisType = 'professional';

function proceedWithAnalysis() {
    // Check if at least one platform is selected
    const hasSelection = Object.values(selectedAnalysisPlatforms).some(selected => selected);
    if (!hasSelection) {
        alert('Please select at least one platform to analyze.');
        return;
    }
    
    // Get selected analysis type
    const analysisType = window.selectedAnalysisType || document.querySelector('input[name="analysisType"]:checked')?.value || 'professional';
    
    // Filter currentAnalysisData to only include selected platforms
    const filteredData = {};
    Object.keys(currentAnalysisData).forEach(platform => {
        if (selectedAnalysisPlatforms[platform]) {
            filteredData[platform] = currentAnalysisData[platform];
        }
    });
    
    // Update currentAnalysisData with filtered data
    currentAnalysisData = filteredData;
    
    // Show loading state
    const modalContent = document.getElementById('analysisModalContent');
    if (modalContent) {
        const analysisTypeLabel = analysisType === 'political' ? 'Political' : 'Professional';
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px 20px;">
                <div style="font-size: 48px; margin-bottom: 16px;">🤖</div>
                <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">NUJUM ${analysisTypeLabel} Analysis</h3>
                <p style="color: #64748b; margin-bottom: 24px;">Analyzing social media profiles...</p>
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            </div>
        `;
    }
    
    // Start analysis with selected type
    startAnalysis(analysisType);
}

function closeAnalysisModal() {
    const modal = document.getElementById('analysisModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    currentAnalysisData = null;
    currentPlatform = null;
    selectedPlatforms = {};
}

// Start analysis directly with stored analysis type
function startAnalysisDirectly() {
    // Ensure we have the analysis data
    if (!currentAnalysisData && window.searchResultsData && window.searchResultsData.platforms) {
        currentAnalysisData = {};
        Object.keys(window.searchResultsData.platforms).forEach(p => {
            currentAnalysisData[p] = {
                found: window.searchResultsData.platforms[p].found || false,
                data: window.searchResultsData.platforms[p].data || null,
                error: window.searchResultsData.platforms[p].error || null
            };
        });
    }
    
    if (!currentAnalysisData) {
        alert('No platform data available for analysis. Please search platforms first.');
        return;
    }
    
    const analysisType = window.selectedAnalysisTypeForAnalysis || 'professional';
    startAnalysis(analysisType);
}

async function startAnalysis(analysisType = 'professional') {
    if (!currentAnalysisData) {
        return;
    }
    
    // Reset toast flags for new analysis
    window.analysisSecondToastShown = false;
    // Reset analysis failed flag for new analysis
    window.analysisFailed = false;
    
    // Get username from search results
    const username = window.searchResultsData?.username || document.getElementById('username')?.value.trim() || 'unknown';
    
    // Check if we should use existing data
    const useExisting = existingDataInfo && existingDataInfo.exists && !existingDataInfo.has_ai_analysis;
    
    try {
        const response = await fetch('{{ route("social-media.ai-analysis") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                username: username,
                platform_data: useExisting ? null : currentAnalysisData, // Don't send if using existing
                use_existing: useExisting,
                analysis_type: analysisType
            })
        });
        
        const result = await response.json();
        
        if (result.success && result.analysis) {
            // If analysis was saved, fetch the full rendered HTML with all charts
            if (result.analysis_id) {
                window.lastAnalysisId = result.analysis_id;
                
                // Show loading message
                const modalContent = document.getElementById('analysisModalContent');
                if (modalContent) {
                    modalContent.innerHTML = `
                        <div style="text-align: center; padding: 40px 20px;">
                            <div style="font-size: 48px; margin-bottom: 16px; animation: spin 1s linear infinite;">⏳</div>
                            <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Loading Full Analysis...</h3>
                            <p style="color: #64748b;">Preparing complete analysis with all charts and details</p>
                        </div>
                    `;
                }
                
                // Wait a moment for the analysis to be fully saved, then fetch the HTML
                setTimeout(async () => {
                    try {
                        const analysisHtmlUrl = `${APP_BASE_PATH}/social-media/${result.analysis_id}/analysis-html`;
                        const htmlResponse = await fetch(analysisHtmlUrl, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
                            }
                        });
                        const htmlResult = await htmlResponse.json();
                        if (htmlResult.success && htmlResult.html) {
                            // Update progress to 100%
                            const progressBar = document.getElementById('progressBar');
                            const progressText = document.getElementById('progressText');
                            const progressStatus = document.getElementById('progressStatus');
                            if (progressBar) progressBar.style.width = '100%';
                            if (progressText) progressText.textContent = '100%';
                            if (progressStatus) progressStatus.textContent = 'Analysis complete!';
                            
                            // Clear progress intervals
                            const progressCard = document.getElementById('progressCard');
                            if (progressCard) {
                                if (progressCard.dataset.progressInterval) {
                                    clearInterval(parseInt(progressCard.dataset.progressInterval));
                                }
                                if (progressCard.dataset.statusInterval) {
                                    clearInterval(parseInt(progressCard.dataset.statusInterval));
                                }
                            }
                            
                            displayAnalysisResultHtml(htmlResult.html, result.analysis_id);
                        } else {
                            // Fallback: retry after a longer delay
                            setTimeout(async () => {
                                try {
                                    const retryResponse = await fetch(analysisHtmlUrl, {
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
                                        }
                                    });
                                    const retryResult = await retryResponse.json();
                                    if (retryResult.success && retryResult.html) {
                                        displayAnalysisResultHtml(retryResult.html, result.analysis_id);
                                    } else {
                                        // Final fallback to summary
                                        console.warn('Could not fetch full HTML, showing summary instead');
                                        displayAnalysisSummary(result.analysis, result.analysis_id);
                                    }
                                } catch (retryError) {
                                    console.error('Error fetching analysis HTML (retry):', retryError);
                                    displayAnalysisSummary(result.analysis, result.analysis_id);
                                }
                            }, 2000);
                        }
                    } catch (error) {
                        console.error('Error fetching analysis HTML:', error);
                        // Fallback to summary if fetch fails
                        displayAnalysisSummary(result.analysis, result.analysis_id);
                    }
                }, 1000);
            } else {
                // If no analysis_id, show summary without link
                displayAnalysisSummary(result.analysis, null);
            }
        } else {
            displayAnalysisError(result.error || 'Analysis failed');
        }
    } catch (error) {
        console.error('Analysis error:', error);
        displayAnalysisError('An error occurred during analysis. Please try again.');
    }
}

function displayAnalysisResult(analysis) {
    const modalContent = document.getElementById('analysisModalContent');
    if (!modalContent) return;
    
    let html = '<div style="max-height: 80vh; overflow-y: auto; padding-right: 8px;">';
    
    // Title
    if (analysis.title) {
        html += `<h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px;">${analysis.title}</h2>`;
    }
    
    // Executive Summary
    if (analysis.executive_summary) {
        html += `<div style="margin-bottom: 32px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white;">`;
        html += `<h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px; color: white;">Executive Summary & Risk Assessment</h3>`;
        html += `<p style="color: rgba(255,255,255,0.95); line-height: 1.8; margin: 0;">${analysis.executive_summary}</p>`;
        html += `</div>`;
    }
    
    // Risk Assessment
    if (analysis.risk_assessment) {
        html += `<div style="margin-bottom: 32px; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">`;
        html += `<h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">Risk Assessment</h3>`;
        
        if (analysis.risk_assessment.overall_risk_level) {
            const riskColor = analysis.risk_assessment.overall_risk_level === 'High' ? '#ef4444' : 
                            analysis.risk_assessment.overall_risk_level === 'Medium' ? '#f59e0b' : '#10b981';
            html += `<div style="margin-bottom: 16px;"><strong style="color: #374151;">Overall Risk Level:</strong> <span style="color: ${riskColor}; font-weight: 600;">${analysis.risk_assessment.overall_risk_level}</span></div>`;
        }
        
        if (analysis.risk_assessment.risk_factors && analysis.risk_assessment.risk_factors.length > 0) {
            html += `<div style="margin-bottom: 16px;"><strong style="color: #374151;">Risk Factors:</strong><ul style="margin: 8px 0 0 20px; padding: 0;">`;
            analysis.risk_assessment.risk_factors.forEach(risk => {
                html += `<li style="margin-bottom: 8px; color: #64748b; line-height: 1.6;">`;
                html += `<strong>${risk.risk || risk}</strong>`;
                if (risk.level) html += ` <span style="color: #ef4444;">(${risk.level})</span>`;
                if (risk.description) html += `<br><span style="font-size: 13px;">${risk.description}</span>`;
                html += `</li>`;
            });
            html += `</ul></div>`;
        }
        
        if (analysis.risk_assessment.red_flags && analysis.risk_assessment.red_flags.length > 0) {
            html += `<div style="margin-bottom: 16px; padding: 12px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 6px;"><strong style="color: #991b1b;">Red Flags:</strong><ul style="margin: 8px 0 0 20px; padding: 0;">`;
            analysis.risk_assessment.red_flags.forEach(flag => {
                html += `<li style="margin-bottom: 4px; color: #991b1b;">${flag}</li>`;
            });
            html += `</ul></div>`;
        }
        
        if (analysis.risk_assessment.positive_indicators && analysis.risk_assessment.positive_indicators.length > 0) {
            html += `<div style="padding: 12px; background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 6px;"><strong style="color: #166534;">Positive Indicators:</strong><ul style="margin: 8px 0 0 20px; padding: 0;">`;
            analysis.risk_assessment.positive_indicators.forEach(indicator => {
                html += `<li style="margin-bottom: 4px; color: #166534;">${indicator}</li>`;
            });
            html += `</ul></div>`;
        }
        
        html += `</div>`;
    }
    
    // Professional Footprint
    if (analysis.professional_footprint) {
        html += generateSection('Professional Footprint Analysis', analysis.professional_footprint);
    }
    
    // Work Ethic Indicators
    if (analysis.work_ethic_indicators) {
        html += generateSection('Work Ethic Indicators', analysis.work_ethic_indicators);
    }
    
    // Cultural Fit Indicators
    if (analysis.cultural_fit_indicators) {
        html += generateSection('Cultural Fit Indicators', analysis.cultural_fit_indicators);
    }
    
    // Professional Growth Signals
    if (analysis.professional_growth_signals) {
        html += generateSection('Professional Growth Signals', analysis.professional_growth_signals);
    }
    
    // Activity Overview
    if (analysis.activity_overview) {
        html += generateSection('Activity Overview & Behavioral Patterns', analysis.activity_overview);
    }
    
    // Personality & Communication
    if (analysis.personality_communication) {
        html += generateSection('Personality & Communication Snapshot', analysis.personality_communication);
    }
    
    // Career Profile
    if (analysis.career_profile) {
        html += generateSection('Career Profile & Growth Signals', analysis.career_profile);
    }
    
    // Overall Assessment
    if (analysis.overall_assessment) {
        html += `<div style="margin-bottom: 32px; padding: 24px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; border: 2px solid #667eea;">`;
        html += `<h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Overall Assessment</h3>`;
        html += `<p style="color: #374151; line-height: 1.8; margin: 0;">${analysis.overall_assessment}</p>`;
        html += `</div>`;
    }
    
    // Recommendations
    if (analysis.recommendations && analysis.recommendations.length > 0) {
        html += `<div style="margin-bottom: 32px; padding: 24px; background: #f0fdf4; border-radius: 12px; border: 1px solid #86efac;">`;
        html += `<h3 style="font-size: 18px; font-weight: 600; color: #166534; margin-bottom: 16px;">Recommendations</h3>`;
        html += `<ul style="margin: 0; padding-left: 20px;">`;
        analysis.recommendations.forEach(rec => {
            html += `<li style="margin-bottom: 8px; color: #166534; line-height: 1.6;">${rec}</li>`;
        });
        html += `</ul></div>`;
    }
    
    // Metadata
    html += `<div style="margin-top: 32px; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">`;
    if (analysis.confidence_level) {
        html += `<div style="margin-bottom: 4px;"><strong>Confidence Level:</strong> ${analysis.confidence_level}</div>`;
    }
    if (analysis.analysis_date) {
        html += `<div style="margin-bottom: 4px;"><strong>Analysis Date:</strong> ${analysis.analysis_date}</div>`;
    }
    if (analysis.data_quality) {
        html += `<div style="margin-bottom: 4px;"><strong>Data Quality:</strong> ${analysis.data_quality}</div>`;
    }
    if (analysis.limitations) {
        html += `<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0;"><strong>Limitations:</strong> ${analysis.limitations}</div>`;
    }
    html += `</div>`;
    
    html += '</div>';
    
    modalContent.innerHTML = html;
}

function generateSection(title, data) {
    let html = `<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">`;
    html += `<h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">${title}</h3>`;
    
    Object.keys(data).forEach(key => {
        if (key === 'recommendations' || key === 'evidence' || key === 'concerns' || key === 'strengths' || key === 'indicators' || key === 'notable_patterns' || key === 'key_characteristics') {
            if (Array.isArray(data[key]) && data[key].length > 0) {
                html += `<div style="margin-bottom: 12px;"><strong style="color: #374151;">${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</strong><ul style="margin: 8px 0 0 20px; padding: 0;">`;
                data[key].forEach(item => {
                    html += `<li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">${item}</li>`;
                });
                html += `</ul></div>`;
            }
        } else if (typeof data[key] === 'string' && data[key].trim() !== '') {
            html += `<div style="margin-bottom: 12px;"><strong style="color: #374151;">${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</strong> <span style="color: #64748b; line-height: 1.6;">${data[key]}</span></div>`;
        }
    });
    
    html += `</div>`;
    return html;
}

function displayAnalysisResultHtml(html, analysisId) {
    // Hide progress card
    const progressCard = document.getElementById('progressCard');
    if (progressCard) {
        progressCard.style.display = 'none';
    }
    
    // Get main content element
    const mainContent = document.querySelector('.cursor-main-content');
    
    // Check if this is a fallback/error response BEFORE setting up the layout
    const isFallbackResponse = html.includes('Analysis Failed') && 
                               (html.includes('Fallback Response') || 
                                html.includes('Due to technical difficulties') ||
                                html.includes('comprehensive analysis could not be generated'));
    
    // Set global flag to indicate analysis failed
    window.analysisFailed = isFallbackResponse;
    
    // If analysis succeeded, restore platform boxes (they were converted to text during loading)
    if (!isFallbackResponse) {
        // Recreate platform boxes by calling showPromptDetails again
        // This will restore the clickable boxes since window.analysisFailed is now false
        const selectedType = document.querySelector('input[name="inlineAnalysisType"]:checked')?.value || window.selectedAnalysisTypeForAnalysis;
        if (selectedType) {
            showPromptDetails();
        }
    }
    
    // Re-enable platform boxes after analysis completes (only if not failed)
    if (!isFallbackResponse) {
        setPlatformBoxesEnabled(true);
    }
    
    // Update platform display in Analysis Input Details if analysis failed
    if (isFallbackResponse) {
        const platformsContainer = document.getElementById('promptDetailsPlatforms');
        if (platformsContainer && window.searchResultsData && window.searchResultsData.platforms) {
            const platformNames = {
                'facebook': 'Facebook',
                'instagram': 'Instagram',
                'tiktok': 'TikTok',
                'twitter': 'X (Twitter)'
            };
            const foundPlatforms = Object.keys(window.searchResultsData.platforms).filter(key => 
                window.searchResultsData.platforms[key] && window.searchResultsData.platforms[key].found
            );
            if (foundPlatforms.length > 0) {
                const platformText = foundPlatforms.map(key => platformNames[key] || key).join(', ');
                platformsContainer.style.display = 'block';
                platformsContainer.style.gridTemplateColumns = 'none';
                platformsContainer.style.gap = '0';
                platformsContainer.innerHTML = `<div style="font-size: 13px; color: #111827; line-height: 1.5; font-weight: 500;">${platformText}</div>`;
            }
        }
    }
    
    if (isFallbackResponse) {
        // Center the right panel for error display (matching search error style)
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
        const promptDetailsActions = document.getElementById('promptDetailsActions');
        if (promptDetailsActions) {
            promptDetailsActions.style.display = 'none';
        }
        
        // Display simple error message directly in mainContent (matching search error style)
        mainContent.innerHTML = `
            <div style="max-width: 500px; width: 100%; text-align: center; padding: 40px 24px;">
                <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Service Unavailable</h2>
                <p style="font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">The service is not available now. Please try again.</p>
                <p style="font-size: 14px; color: #94a3b8;">We are experiencing technical difficulties. Please try again later.</p>
            </div>
        `;
        
        // Show toast notification immediately (only once)
        let analysisErrorToastShown = false;
        if (!analysisErrorToastShown) {
            const toastMessage = 'The service is not available now. Please try again.';
            if (typeof showToast === 'function') {
                showToast(toastMessage, 'error');
                analysisErrorToastShown = true;
            }
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
                    isToastShowing = false;
                    
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
        // Update right panel to show results (normal flow)
        if (mainContent) {
            mainContent.style.alignItems = 'flex-start';
            mainContent.style.justifyContent = 'flex-start';
            mainContent.style.padding = '24px';
            mainContent.classList.add('scrollable');
        }
        
        // Make main panel scrollable
        const mainPanel = document.querySelector('.cursor-main');
        if (mainPanel) {
            mainPanel.classList.add('scrollable');
        }
        
        // Show prompt details action buttons after analysis completes
        const promptDetailsActions = document.getElementById('promptDetailsActions');
        if (promptDetailsActions) {
            promptDetailsActions.style.display = 'block';
        }
        
        // Store analysis ID for export
    if (analysisId) {
            window.lastAnalysisId = analysisId;
        }
        
        // Create results container
        let resultsContainer = document.getElementById('analysisResultsContainer');
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.id = 'analysisResultsContainer';
            resultsContainer.style.cssText = 'width: 100%; max-width: 100%;';
            if (mainContent) {
                mainContent.appendChild(resultsContainer);
            }
        }
        
        // Display the HTML directly (no button)
        resultsContainer.innerHTML = html;
    }
    
    // Execute all script tags in the inserted HTML to initialize tooltips
    // Scripts wrapped in DOMContentLoaded won't execute, so we need to run them manually
    setTimeout(() => {
        const scripts = resultsContainer.querySelectorAll('script');
        const extractedData = {}; // Store extracted data for tooltips
        
        scripts.forEach(oldScript => {
            // First, try to extract dimensions/traits data from the script
            const scriptContent = oldScript.innerHTML;
            
            // Look for const dimensions or const traits pattern
            // The json data will have been rendered as actual JSON
            const dataPattern = new RegExp('const\\s+(dimensions|traits)\\s*=\\s*(\\{[\\s\\S]*?\\});');
            const dataMatch = scriptContent.match(dataPattern);
            if (dataMatch) {
                try {
                    const dataType = dataMatch[1];
                    const jsonString = dataMatch[2];
                    const parsedData = JSON.parse(jsonString);
                    
                    // Store in a way we can access it later
                    // Use the tooltip ID to map the data
                    const tooltipIdPattern = new RegExp('getElementById\\([\'"]([^\'"]+-tooltip)[\'"]');
                    const tooltipIdMatch = scriptContent.match(tooltipIdPattern);
                    if (tooltipIdMatch) {
                        const tooltipId = tooltipIdMatch[1];
                        extractedData[tooltipId] = parsedData;
                    }
                } catch (e) {
                    console.log('Could not parse data from script:', e);
                }
            }
            
            // Create a new script element to execute
            const newScript = document.createElement('script');
            
            // Extract script content, removing DOMContentLoaded wrapper if present
            let modifiedContent = scriptContent;
            
            // Remove DOMContentLoaded wrapper - handle both single-line and multi-line patterns
            modifiedContent = modifiedContent.replace(
                /document\.addEventListener\(['"]DOMContentLoaded['"],\s*function\(\)\s*\{([\s\S]*?)\}\);?/g,
                '$1'
            );
            
            newScript.innerHTML = modifiedContent;
            
            // Replace old script with new one to execute
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
        
        // Store extracted data globally for tooltip initialization
        window.extractedRadarChartData = extractedData;
        
        // Also manually initialize tooltips for all radar charts to ensure they work
        // This is a fallback in case script execution doesn't work properly
        setTimeout(() => {
            initializeAllRadarChartTooltips();
        }, 150);
    }, 50);
}

// Function to manually initialize all radar chart tooltips after dynamic insertion
function initializeAllRadarChartTooltips() {
    const resultsContainer = document.getElementById('analysisResultsContainer');
    if (!resultsContainer) return;
    
    // Find all SVG elements (radar charts) in the results container
    const svgCharts = resultsContainer.querySelectorAll('svg.radar-chart-svg');
    
    svgCharts.forEach(svg => {
        // Find the tooltip element for this chart section
        // Check parent containers to find the tooltip
        let tooltip = null;
        let tooltipId = null;
        let pointSelector = null;
        let labelSelector = null;
        let dataAttribute = 'data-dimension';
        
        // Find the tooltip element by checking the section containing this SVG
        // Each chart section should have its tooltip nearby
        const chartContainer = svg.closest('.radar-chart-container') || svg.closest('div');
        const sectionContainer = chartContainer ? chartContainer.closest('div') : null;
        
        // Try to find tooltip by common IDs - search within the results container
        const possibleIds = [
            'radar-tooltip',
            'personality-tooltip',
            'cultural-fit-tooltip',
            'professional-growth-tooltip',
            'political-engagement-tooltip',
            'political-alignment-tooltip',
            'political-growth-tooltip',
            'political-communication-tooltip'
        ];
        
        // First, try to find tooltip in the same section as the SVG
        if (sectionContainer) {
            for (let id of possibleIds) {
                const tooltipEl = document.getElementById(id);
                if (tooltipEl) {
                    // Check if tooltip is in the same section (within reasonable distance in DOM)
                    const tooltipParent = tooltipEl.parentElement;
                    if (sectionContainer.contains(tooltipEl) || 
                        (tooltipParent && (sectionContainer.contains(tooltipParent) || 
                         tooltipParent.contains(sectionContainer)))) {
                        tooltip = tooltipEl;
                        tooltipId = id;
                        break;
                    }
                }
            }
        }
        
        // If not found, search globally within results container
        if (!tooltip) {
            for (let id of possibleIds) {
                const tooltipEl = document.getElementById(id);
                if (tooltipEl && resultsContainer.contains(tooltipEl)) {
                    tooltip = tooltipEl;
                    tooltipId = id;
                    break;
                }
            }
        }
        
        // Last resort: find any tooltip in results container
        if (!tooltip) {
            const allTooltips = resultsContainer.querySelectorAll('[id$="-tooltip"]');
            if (allTooltips.length > 0) {
                // Use the first one found, but this is not ideal
                tooltip = allTooltips[0];
                tooltipId = tooltip.id;
            }
        }
        
        if (!tooltip) return;
        
        // Determine selectors and data based on tooltip ID
        if (tooltipId === 'personality-tooltip') {
            pointSelector = '.personality-point';
            labelSelector = '.personality-label';
            dataAttribute = 'data-trait';
        } else {
            pointSelector = '.radar-point';
            labelSelector = '.radar-label';
            dataAttribute = 'data-dimension';
        }
        
        // Get data from script tags - try to extract full data first
        let data = null;
        
        // First, try to get data from the extracted data we stored earlier
        if (window.extractedRadarChartData && window.extractedRadarChartData[tooltipId]) {
            data = window.extractedRadarChartData[tooltipId];
        }
        
        // If not found, try to extract from script tags directly
        if (!data) {
            const scripts = resultsContainer.querySelectorAll('script');
            for (let script of scripts) {
                if (script.innerHTML.includes(tooltipId)) {
                    // Look for const dimensions or const traits pattern
                    const dataPattern = new RegExp('const\\s+(dimensions|traits)\\s*=\\s*(\\{[\\s\\S]*?\\});');
                    const dataMatch = script.innerHTML.match(dataPattern);
                    if (dataMatch) {
                        try {
                            const jsonString = dataMatch[2];
                            data = JSON.parse(jsonString);
                            break;
                        } catch (e) {
                            console.log('Could not parse data from script:', e);
                        }
                    }
                }
            }
        }
        
        // If still no data, build minimal data from labels as fallback
        if (!data || Object.keys(data).length === 0) {
            const chartLabels = svg.querySelectorAll(labelSelector);
            if (chartLabels.length > 0) {
                data = {};
                chartLabels.forEach(label => {
                    const key = label.getAttribute(dataAttribute);
                    if (key) {
                        const labelText = label.textContent.trim();
                        data[key] = {
                            label: labelText,
                            description: 'Hover to see details',
                            score: 0
                        };
                    }
                });
            }
        }
        
        if (!data || Object.keys(data).length === 0) return;
        
        // Initialize tooltip handlers
        const points = svg.querySelectorAll(pointSelector);
        const chartLabels3 = svg.querySelectorAll(labelSelector);
        
        function showTooltip(event, key) {
            if (!data || !data[key]) return;
            
            const item = data[key];
            const tooltipText = (item.label || key) + '\n\n' + (item.description || '') + '\n\nScore: ' + (item.score || 'N/A') + '/100';
            tooltip.textContent = tooltipText;
            
            tooltip.style.opacity = '0';
            tooltip.style.visibility = 'hidden';
            tooltip.style.display = 'block';
            
            let x, y;
            if (event && event.target) {
                const target = event.target;
                
                if (target.tagName === 'circle' || (target.parentElement && target.parentElement.classList.contains(pointSelector.replace('.', '')))) {
                    const circle = target.tagName === 'circle' ? target : target.querySelector('circle');
                    if (circle && svg) {
                        const svgRect = svg.getBoundingClientRect();
                        const scaleX = svgRect.width / 500;
                        const scaleY = svgRect.height / 500;
                        const cx = parseFloat(circle.getAttribute('cx'));
                        const cy = parseFloat(circle.getAttribute('cy'));
                        x = svgRect.left + (cx * scaleX);
                        y = svgRect.top + (cy * scaleY);
                    } else {
                        x = event.clientX || event.pageX;
                        y = event.clientY || event.pageY;
                    }
                } else if (target.tagName === 'text' || target.classList.contains(labelSelector.replace('.', ''))) {
                    if (event.clientX && event.clientY) {
                        x = event.clientX;
                        y = event.clientY;
                    } else {
                        const textRect = target.getBoundingClientRect();
                        x = textRect.left + (textRect.width / 2);
                        y = textRect.top + (textRect.height / 2);
                    }
                } else {
                    x = event.clientX || event.pageX;
                    y = event.clientY || event.pageY;
                }
            } else {
                x = event.clientX || event.pageX;
                y = event.clientY || event.pageY;
            }
            
            const tooltipWidth = 280;
            const tooltipHeight = tooltip.offsetHeight || 150;
            let left = x - (tooltipWidth / 2);
            let top = y + 20;
            
            if (left < 10) left = 10;
            if (left + tooltipWidth > window.innerWidth - 10) {
                left = window.innerWidth - tooltipWidth - 10;
            }
            if (top < 10) top = y + 20;
            if (top + tooltipHeight > window.innerHeight - 10) {
                top = window.innerHeight - tooltipHeight - 10;
            }
            
            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
            tooltip.style.visibility = 'visible';
            tooltip.style.opacity = '1';
        }
        
        function hideTooltip() {
            tooltip.style.opacity = '0';
            setTimeout(() => {
                tooltip.style.visibility = 'hidden';
            }, 200);
        }
        
        // Attach handlers to points
        points.forEach(point => {
            const key = point.getAttribute(dataAttribute) || point.closest(`[${dataAttribute}]`)?.getAttribute(dataAttribute);
            if (key && data[key]) {
                // Remove old handlers
                point.removeEventListener('mouseenter', point._showTooltip);
                point.removeEventListener('mouseleave', point._hideTooltip);
                point.removeEventListener('mousemove', point._moveTooltip);
                
                // Add new handlers
                point._showTooltip = (e) => showTooltip(e, key);
                point._hideTooltip = hideTooltip;
                point._moveTooltip = (e) => {
                    if (tooltip.style.opacity === '1') {
                        showTooltip(e, key);
    }
                };
                
                point.addEventListener('mouseenter', point._showTooltip);
                point.addEventListener('mouseleave', point._hideTooltip);
                point.addEventListener('mousemove', point._moveTooltip);
                point.style.cursor = 'help';
            }
        });
        
        // Attach handlers to labels
        chartLabels3.forEach(label => {
            const key = label.getAttribute(dataAttribute);
            if (key && data[key]) {
                // Remove old handlers
                label.removeEventListener('mouseenter', label._showTooltip);
                label.removeEventListener('mouseleave', label._hideTooltip);
                label.removeEventListener('mousemove', label._moveTooltip);
                
                // Add new handlers
                label._showTooltip = (e) => showTooltip(e, key);
                label._hideTooltip = hideTooltip;
                label._moveTooltip = (e) => {
                    if (tooltip.style.opacity === '1') {
                        showTooltip(e, key);
                    }
                };
                
                label.addEventListener('mouseenter', label._showTooltip);
                label.addEventListener('mouseleave', label._hideTooltip);
                label.addEventListener('mousemove', label._moveTooltip);
                label.style.cursor = 'help';
            }
        });
    });
}

function displayAnalysisSummary(analysis, analysisId) {
    const modalContent = document.getElementById('analysisModalContent');
    if (!modalContent) return;
    
    let html = '<div style="max-height: 80vh; overflow-y: auto; padding-right: 8px;">';
    
    // Title
    if (analysis.title) {
        html += `<h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px;">${analysis.title}</h2>`;
    }
    
    // Success message
    html += `<div style="margin-bottom: 24px; padding: 16px; background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 8px;">`;
    html += `<div style="display: flex; align-items: center; gap: 12px;">`;
    html += `<div style="font-size: 32px;">✅</div>`;
    html += `<div><strong style="color: #166534; font-size: 16px;">Analysis Completed Successfully!</strong><p style="color: #166534; margin: 4px 0 0 0; font-size: 14px;">Your analysis has been saved and is ready to view.</p></div>`;
    html += `</div></div>`;
    
    // Executive Summary
    if (analysis.executive_summary) {
        html += `<div style="margin-bottom: 24px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white;">`;
        html += `<h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px; color: white;">Executive Summary & Risk Assessment</h3>`;
        html += `<p style="color: rgba(255,255,255,0.95); line-height: 1.8; margin: 0; word-wrap: break-word; overflow-wrap: break-word;">${analysis.executive_summary}</p>`;
        html += `</div>`;
    }
    
    // Risk Assessment Summary
    if (analysis.risk_assessment) {
        html += `<div style="margin-bottom: 24px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">`;
        html += `<h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">Risk Assessment</h3>`;
        
        if (analysis.risk_assessment.overall_risk_level) {
            const riskColor = analysis.risk_assessment.overall_risk_level === 'High' ? '#ef4444' : 
                            analysis.risk_assessment.overall_risk_level === 'Medium' ? '#f59e0b' : '#10b981';
            html += `<div style="margin-bottom: 12px;"><strong style="color: #374151;">Overall Risk Level:</strong> <span style="color: ${riskColor}; font-weight: 600; font-size: 16px;">${analysis.risk_assessment.overall_risk_level}</span></div>`;
        }
        
        if (analysis.risk_assessment.red_flags && analysis.risk_assessment.red_flags.length > 0) {
            html += `<div style="margin-bottom: 12px; padding: 12px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 6px;">`;
            html += `<strong style="color: #991b1b;">Red Flags Found:</strong> <span style="color: #991b1b; font-weight: 600;">${analysis.risk_assessment.red_flags.length}</span>`;
            html += `</div>`;
        }
        
        if (analysis.risk_assessment.positive_indicators && analysis.risk_assessment.positive_indicators.length > 0) {
            html += `<div style="padding: 12px; background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 6px;">`;
            html += `<strong style="color: #166534;">Positive Indicators Found:</strong> <span style="color: #166534; font-weight: 600;">${analysis.risk_assessment.positive_indicators.length}</span>`;
            html += `</div>`;
        }
        
        html += `</div>`;
    }
    
    // Analysis Type
    const analysisType = analysis.analysis_type || 'professional';
    const typeLabel = analysisType === 'political' ? 'Political' : 'Professional';
    html += `<div style="margin-bottom: 24px; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">`;
    html += `<div style="display: flex; align-items: center; gap: 12px;">`;
    html += `<div style="font-size: 20px;">📊</div>`;
    html += `<div><strong style="color: #374151;">Analysis Type:</strong> <span style="color: #667eea; font-weight: 600; text-transform: capitalize;">${typeLabel}</span></div>`;
    html += `</div></div>`;
    
    // Sections summary
    html += `<div style="margin-bottom: 24px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">`;
    html += `<h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 16px;">Analysis Sections Included:</h3>`;
    html += `<ul style="margin: 0; padding-left: 20px; color: #64748b;">`;
    
    if (analysisType === 'professional') {
        if (analysis.professional_footprint) html += `<li>Professional Footprint Analysis (with circular gauge chart)</li>`;
        if (analysis.work_ethic_indicators) html += `<li>Work Ethic Indicators (with radar chart)</li>`;
        if (analysis.cultural_fit_indicators) html += `<li>Cultural Fit Indicators (with radar chart)</li>`;
        if (analysis.professional_growth_signals) html += `<li>Professional Growth Signals (with radar chart)</li>`;
        if (analysis.personality_communication) html += `<li>Personality & Communication (with radar chart)</li>`;
        if (analysis.career_profile) html += `<li>Career Profile & Growth Signals</li>`;
    } else {
        if (analysis.political_profile) html += `<li>Political Profile</li>`;
        if (analysis.political_engagement_indicators) html += `<li>Political Engagement Indicators (with radar chart)</li>`;
        if (analysis.political_alignment_indicators) html += `<li>Political Alignment Indicators (with radar chart)</li>`;
        if (analysis.political_growth_signals) html += `<li>Political Growth Signals (with radar chart)</li>`;
        if (analysis.political_communication_style) html += `<li>Political Communication Style (with radar chart)</li>`;
        if (analysis.political_career_profile) html += `<li>Political Career Profile</li>`;
    }
    
    if (analysis.activity_overview) html += `<li>Activity Overview & Behavioral Patterns</li>`;
    if (analysis.overall_assessment) html += `<li>Overall Assessment</li>`;
    if (analysis.recommendations) html += `<li>Recommendations</li>`;
    
    html += `</ul></div>`;
    
    // Button to view full analysis
    html += `<div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 2px solid #e2e8f0;">`;
    if (analysisId) {
        // Link to specific analysis page
        const analysisUrl = `${APP_BASE_PATH}/social-media/${analysisId}`;
        html += `<a href="${analysisUrl}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; border: none; border-radius: 10px; font-weight: 700; font-size: 16px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.5); cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(102, 126, 234, 0.5)';" onclick="closeAnalysisModal();">View Full Analysis with All Charts & Details</a>`;
        html += `<p style="margin-top: 16px; color: #64748b; font-size: 14px; line-height: 1.6;">View complete analysis including all interactive charts, detailed breakdowns, and export to PDF</p>`;
        html += `<p style="margin-top: 8px; color: #10b981; font-size: 13px; font-weight: 600;">✓ Analysis ID: ${analysisId} - Saved successfully</p>`;
    } else {
        // Fallback to history page if no analysis ID
        const historyUrl = `${APP_BASE_PATH}/social-media/history`;
        html += `<a href="${historyUrl}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; border: none; border-radius: 10px; font-weight: 700; font-size: 16px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.5); cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(102, 126, 234, 0.5)';" onclick="closeAnalysisModal();">View Analysis History</a>`;
        html += `<p style="margin-top: 16px; color: #64748b; font-size: 14px; line-height: 1.6;">View all your saved analyses with complete details and charts</p>`;
    }
    html += `</div>`;
    
    html += '</div>';
    
    modalContent.innerHTML = html;
}

function displayAnalysisError(error) {
    const modalContent = document.getElementById('analysisModalContent');
    if (!modalContent) return;
    
    modalContent.innerHTML = `
        <div style="text-align: center; padding: 40px 20px;">
            <div style="font-size: 48px; margin-bottom: 16px;">❌</div>
            <h3 style="font-size: 20px; font-weight: 600; color: #ef4444; margin-bottom: 12px;">Analysis Failed</h3>
            <p style="color: #64748b; margin-bottom: 24px;">${error}</p>
            <button onclick="startAnalysis()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Retry</button>
        </div>
    `;
}
</script>

<!-- Analysis Modal -->
<div id="analysisModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; max-width: 900px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="padding: 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 id="analysisModalTitle" style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">NUJUM Analysis</h2>
            <button onclick="closeAnalysisModal()" style="background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b';" onmouseout="this.style.background='none'; this.style.color='#64748b';">&times;</button>
        </div>
        <div id="analysisModalContent" style="flex: 1; overflow-y: auto; padding: 24px;">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<style>
    /* Info Tooltip Styles - Matching Create Predictions Page */

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    #analysisModal > div {
        max-width: 95% !important;
        max-height: 95vh !important;
        margin: 10px !important;
    }
    
    #analysisModalContent {
        padding: 16px !important;
    }
}
</style>

<script>
// Typing Facts for Social Media Analysis
document.addEventListener('DOMContentLoaded', function() {
    const typingText = document.getElementById('typingText');
    const typingCursor = document.getElementById('typingCursor');
    const didYouKnowSection = document.getElementById('didYouKnowSection');
    
    if (!typingText || !typingCursor) return;
    
    const socialMediaFacts = [
        "AI agents handle multi-step tasks end-to-end. AI will not just answer questions but plan, execute, verify, and report results across tools (email, spreadsheets, APIs).",
        "Data quality becomes more important than model size. Companies with clean, structured, proprietary data will outperform those using only bigger models.",
        "AI costs drop sharply for common tasks. Inference and fine-tuned models will become cheap enough to be embedded in small apps and startups.",
        "\"Human-in-the-loop\" becomes a legal requirement. Critical systems (finance, hiring, healthcare) will require documented human review for AI decisions.",
        "AI replaces repetitive junior-level tasks, not roles. Entry-level work changes shape: fewer repetitive tasks, more validation, oversight, and creative work.",
        "Synthetic data becomes widely accepted. AI-generated data will be used to train models where real data is scarce, sensitive, or expensive.",
        "AI-generated UI/UX becomes mainstream. Design systems will be auto-generated based on brand rules, accessibility needs, and user behavior.",
        "AI monitoring tools become mandatory in enterprises. Companies will track hallucinations, bias, drift, and cost the same way they track uptime today.",
        "AI becomes a normal part of cybersecurity defense. Real-time AI systems will detect abnormal behavior faster than human analysts.",
        "Voice AI becomes more human—but still identifiable. Speech AI will sound natural but remain clearly labeled to meet regulation and trust standards."
    ];
    
    let currentFactIndex = 0;
    let currentCharIndex = 0;
    let isDeleting = false;
    let typingSpeed = 50;
    
    function typeFact() {
        const currentFact = socialMediaFacts[currentFactIndex];
        
        if (!isDeleting && currentCharIndex < currentFact.length) {
            typingText.textContent = currentFact.substring(0, currentCharIndex + 1);
            currentCharIndex++;
            typingSpeed = 50;
        } else if (isDeleting && currentCharIndex > 0) {
            typingText.textContent = currentFact.substring(0, currentCharIndex - 1);
            currentCharIndex--;
            typingSpeed = 30;
        } else if (!isDeleting && currentCharIndex === currentFact.length) {
            // Pause at end of fact
            typingSpeed = 2000;
            isDeleting = true;
        } else if (isDeleting && currentCharIndex === 0) {
            // Move to next fact
            isDeleting = false;
            currentFactIndex = (currentFactIndex + 1) % socialMediaFacts.length;
            typingSpeed = 500;
        }
        
        setTimeout(typeFact, typingSpeed);
    }
    
    // Start typing animation
    typeFact();
    
    // Hide "Did You Know" section when search starts
    const originalPerformSearch = window.performSearch;
    if (originalPerformSearch) {
        window.performSearch = function() {
            if (didYouKnowSection) {
                didYouKnowSection.style.display = 'none';
            }
            return originalPerformSearch.apply(this, arguments);
        };
    }
});

// Toast notification function
// Global flag to prevent duplicate toast notifications
let isToastShowing = false;
let toastTimeout = null;
let secondToastShown = false; // Global flag to prevent duplicate second toast

function showToast(message, type = 'success') {
    // Prevent duplicate toasts - if flag is set and toast exists, return early
    if (isToastShowing) {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            return; // Don't show duplicate toast
        } else {
            // Toast was removed but flag wasn't reset, reset it
            isToastShowing = false;
        }
    }
    
    // Set flag immediately to prevent duplicates
    isToastShowing = true;
    
    // Remove existing toasts with animation (if any exist)
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
    
    // Auto remove after duration (longer for error toasts)
    const displayDuration = type === 'error' ? 6000 : 4000; // 6 seconds for errors, 4 seconds for success
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            // Reset flag after toast is removed
            isToastShowing = false;
        }, 300);
    }, displayDuration);
}

// Export function for social media analysis
let currentExportId = null;

window.confirmExportFromSocialMedia = function() {
    // Get analysis ID from global variable
    const analysisId = window.lastAnalysisId;
    
    if (!analysisId) {
        showToast('Analysis ID not available. Please wait for the analysis to complete.', 'error');
        return;
    }
    
    // Get username from the form
    const username = document.getElementById('username')?.value.trim() || 'Social Media Analysis';
    
    // Store the analysis ID for export
    currentExportId = analysisId;
    
    const exportTopicElement = document.getElementById('exportTopic');
    if (exportTopicElement) {
        exportTopicElement.textContent = username.length > 50 ? username.substring(0, 50) + '...' : username;
    }
    const exportModal = document.getElementById('exportModal');
    if (exportModal) {
        exportModal.style.display = 'flex';
        
        // Ensure button is set up when modal is shown
        const confirmExportBtn = document.getElementById('confirmExportBtn');
        if (confirmExportBtn && !confirmExportBtn.onclick) {
            confirmExportBtn.onclick = exportSocialMediaAnalysis;
        }
    }
};

window.closeExportModal = function() {
    const exportModal = document.getElementById('exportModal');
    if (exportModal) {
        exportModal.style.display = 'none';
    }
    currentExportId = null;
};

window.exportSocialMediaAnalysis = function() {
    // Use currentExportId if available, otherwise try window.lastAnalysisId
    const analysisId = currentExportId || window.lastAnalysisId;
    
    if (!analysisId || analysisId === 'null' || analysisId === null || analysisId === 'undefined') {
        showToast('Error: Analysis ID not found', 'error');
        closeExportModal();
        return;
    }
    
    // Validate analysis ID is a valid number
    const analysisIdNum = parseInt(analysisId);
    if (isNaN(analysisIdNum) || analysisIdNum <= 0) {
        console.error('Invalid analysis ID for export:', analysisId);
        showToast('Error: Invalid analysis ID', 'error');
        closeExportModal();
        return;
    }
    
    // Store the ID before closing the modal
    const analysisIdToExport = analysisIdNum;
    
    // Close the modal first
    closeExportModal();
    
    // Show loading message
    showToast('Exporting PDF...', 'success');
    
    // Generate absolute URL to handle subdirectory deployments (cPanel)
    const baseUrl = '{{ url("/") }}';
    const exportUrl = `${baseUrl}/social-media/${analysisIdToExport}/export`;
    
    // Redirect to the export route
    // The download will start automatically
    // Show success message after a short delay (optimistic)
    setTimeout(() => {
        showToast('PDF exported successfully!', 'success');
    }, 1000);
    
    window.location.href = exportUrl;
};

// Set up the confirm export button and modal handlers
document.addEventListener('DOMContentLoaded', function() {
    const confirmExportBtn = document.getElementById('confirmExportBtn');
    if (confirmExportBtn) {
        confirmExportBtn.onclick = exportSocialMediaAnalysis;
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
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this social media analysis as a PDF report?</p>
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


