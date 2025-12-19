@extends('layouts.app')

@section('content')
<div style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div>
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Social Media Analysis</h1>
                <p style="color: #64748b; font-size: 14px; margin: 0;">
                    Enter a username to search across all social media platforms
                </p>
                    </div>
                    <div>
                        <a href="{{ route('social-media.history') }}" style="display: inline-block; padding: 12px 24px; background: #f8fafc; color: #374151; text-decoration: none; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">
                            View History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any() || session('analysis_error'))
                <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                    <strong style="display: block; margin-bottom: 12px; font-size: 16px;">❌ Error:</strong>
                    @if(session('analysis_error'))
                        @php $error = session('analysis_error'); @endphp
                        <p style="margin: 0 0 12px 0; line-height: 1.6; font-weight: 500;">{{ $error['error'] ?? 'Unknown error' }}</p>
                    @else
                        <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
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
                <form id="searchForm" style="margin-bottom: 24px;">
                    @csrf
                    
                    <div style="margin-bottom: 24px;">
                        <label for="username" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                            Username <span style="color: #ef4444;">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="{{ old('username') }}"
                            placeholder="Enter username (e.g., 'username' without @)"
                            required
                            style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                            onkeyup="checkExistingData(this.value)"
                        >
                    </div>

                    <!-- Existing Data Notification -->
                    <div id="existingDataNotification" style="display: none; margin-bottom: 16px; padding: 16px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #166534; margin-bottom: 4px;">Previous Search Found</div>
                                <div style="color: #166534; font-size: 13px; margin-bottom: 4px;">We found previous search data for this username. You can use it to skip searching.</div>
                                <div class="date-info" style="color: #059669; font-size: 12px; font-style: italic;"></div>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button 
                                    type="button" 
                                    onclick="useExistingData()"
                                    style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#059669';"
                                    onmouseout="this.style.background='#10b981';"
                                >
                                    Use Previous Data
                                </button>
                                <button 
                                    type="button" 
                                    onclick="searchAgain()"
                                    style="padding: 8px 16px; background: transparent; color: #166534; border: 1px solid #86efac; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#dcfce7';"
                                    onmouseout="this.style.background='transparent';"
                                >
                                    Search Again
                                </button>
                            </div>
                        </div>
                    </div>

                    <button 
                        type="submit"
                        id="searchButton"
                        onclick="return checkBeforeSearch(event);"
                        style="width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';"
                    >
                        Start Analyze
                    </button>
                    
                    <!-- Available Platforms -->
                    <p style="text-align: center; margin-top: 12px; color: #64748b; font-size: 13px;">
                        (Facebook, Instagram, TikTok, X)
                    </p>
                </form>
            </div>

            <!-- Platform Status -->
            <div id="platformStatus" style="display: none; margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px;">Searching Platforms...</h3>
                <div id="platformStatusGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                    <div id="status-facebook" class="platform-status-item" onclick="showPlatformResults('facebook')" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #1877F2;">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">Facebook</div>
                            <div class="status-text" style="color: #64748b; font-size: 11px;">Searching...</div>
                        </div>
                    </div>
                    <div id="status-instagram" class="platform-status-item" onclick="showPlatformResults('instagram')" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                                <defs>
                                    <linearGradient id="instagram-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient)"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">Instagram</div>
                            <div class="status-text" style="color: #64748b; font-size: 11px;">Searching...</div>
                        </div>
                    </div>
                    <div id="status-tiktok" class="platform-status-item" onclick="showPlatformResults('tiktok')" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">TikTok</div>
                            <div class="status-text" style="color: #64748b; font-size: 11px;">Searching...</div>
                        </div>
                    </div>
                    <div id="status-twitter" class="platform-status-item" onclick="showPlatformResults('twitter')" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; font-size: 13px;">X (Twitter)</div>
                            <div class="status-text" style="color: #64748b; font-size: 11px;">Searching...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Button Container -->
            <div id="analysisButtonContainer" style="display: none; margin-top: 24px; margin-bottom: 24px;">
                <button onclick="openAnalysisModal('all', null)" style="width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';">
                    Start Analyzing Profile
                </button>
            </div>
            
            <!-- Clear Button Container (separate from analysis button) -->
            <div id="clearButtonContainer" style="display: none; margin-top: 24px; margin-bottom: 24px;">
                <button 
                    type="button"
                    id="clearButton"
                    onclick="clearResults()"
                    style="width: 100%; padding: 12px 24px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease;"
                    onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';"
                    onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';"
                >
                    Clear
                </button>
            </div>

            <!-- Results Container -->
            <div id="resultsContainer" style="display: none;"></div>
        </div>
    </div>
</div>

<!-- Platform Selection Modal -->
<div id="platformSelectionModal" onclick="if(event.target === this) closePlatformSelectionModal();" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div onclick="event.stopPropagation();" style="background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Select Platforms</h2>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">Choose which platforms you want to search:</p>
        
        <div id="platformSelectionList" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
            <!-- Platforms will be added here by JavaScript -->
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button 
                onclick="closePlatformSelectionModal()" 
                style="padding: 12px 24px; background: transparent; color: #64748b; border: 2px solid #d1d5db; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease;"
                onmouseover="this.style.borderColor='#9ca3af'; this.style.color='#374151';"
                onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#64748b';"
            >
                Cancel
            </button>
            <button 
                onclick="proceedWithSelectedPlatforms()" 
                id="proceedSearchButton"
                style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';"
            >
                Search Selected
            </button>
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
        display: block;
        width: 100%;
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

// Check before allowing search to proceed
async function checkBeforeSearch(event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
    
    // First, check if username is entered
    const usernameInput = document.getElementById('username');
    const username = usernameInput ? usernameInput.value.trim() : '';
    
    if (!username || username.length < 2) {
        showUsernameValidationModal();
        // Reset button
        const searchButton = document.getElementById('searchButton');
        if (searchButton) {
            searchButton.disabled = false;
            searchButton.textContent = 'Search All Platforms';
        }
        return false; // Prevent form submission - username required
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
        // Reset button
        const searchButton = document.getElementById('searchButton');
        if (searchButton) {
            searchButton.disabled = false;
            searchButton.textContent = 'Search All Platforms';
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
    
    // If no previous data or user chose to search again, show platform selection modal
    // Reset the flag since this is a normal search (not from "Search Again")
    modalOpenedFromSearchAgain = false;
    openPlatformSelectionModal();
    return false; // Prevent form submission
}

// Open platform selection modal
function openPlatformSelectionModal() {
    const modal = document.getElementById('platformSelectionModal');
    const platformList = document.getElementById('platformSelectionList');
    
    if (!modal || !platformList) return;
    
    // Reset selection to empty each time modal opens
    selectedPlatforms = [];
    
    // Define platforms with their display names and icons
    const platforms = [
        { key: 'facebook', name: 'Facebook', icon: '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>' },
        { key: 'instagram', name: 'Instagram', icon: '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px;"><defs><linearGradient id="instagram-gradient-modal-select" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-modal-select)"/></svg>' },
        { key: 'tiktok', name: 'TikTok', icon: '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>' },
        { key: 'twitter', name: 'X (Twitter)', icon: '<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #000000;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' }
    ];
    
    // Clear previous content
    platformList.innerHTML = '';
    
    // Create checkboxes for each platform
    platforms.forEach(platform => {
        const isChecked = selectedPlatforms.includes(platform.key);
        const platformItem = document.createElement('div');
        platformItem.style.cssText = 'padding: 16px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease;';
        platformItem.onmouseover = function() {
            this.style.borderColor = '#667eea';
            this.style.boxShadow = '0 2px 8px rgba(102, 126, 234, 0.1)';
        };
        platformItem.onmouseout = function() {
            if (!isChecked) {
                this.style.borderColor = '#e2e8f0';
                this.style.boxShadow = 'none';
            }
        };
        platformItem.onclick = function() {
            togglePlatformSelection(platform.key);
        };
        
        platformItem.innerHTML = `
            <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                <input type="checkbox" id="platform_check_${platform.key}" ${isChecked ? 'checked' : ''} 
                       onchange="togglePlatformSelection('${platform.key}')" 
                       style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #667eea;">
                <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">${platform.icon}</div>
                <span style="font-weight: 600; color: #1e293b; font-size: 15px; flex: 1;">${platform.name}</span>
            </label>
        `;
        
        if (isChecked) {
            platformItem.style.borderColor = '#667eea';
            platformItem.style.background = '#f8faff';
        }
        
        platformList.appendChild(platformItem);
    });
    
    // Show modal
    modal.style.display = 'flex';
    updateProceedButtonState();
}

// Close platform selection modal
function closePlatformSelectionModal() {
    const modal = document.getElementById('platformSelectionModal');
    if (modal) {
        modal.style.display = 'none';
    }
    
    // If modal was opened from "Search Again" and user clicked Cancel, clear the username field
    if (modalOpenedFromSearchAgain) {
        const usernameInput = document.getElementById('username');
        if (usernameInput) {
            usernameInput.value = '';
            // Also hide the existing data notification if it's still visible
            const notification = document.getElementById('existingDataNotification');
            if (notification) {
                notification.style.display = 'none';
            }
            // Reset existing data info
            existingDataInfo = null;
        }
        // Reset the flag
        modalOpenedFromSearchAgain = false;
    }
}

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

// Toggle platform selection
function togglePlatformSelection(platformKey) {
    const checkbox = document.getElementById(`platform_check_${platformKey}`);
    if (!checkbox) return;
    
    if (checkbox.checked) {
        if (!selectedPlatforms.includes(platformKey)) {
            selectedPlatforms.push(platformKey);
        }
    } else {
        selectedPlatforms = selectedPlatforms.filter(p => p !== platformKey);
    }
    
    // Update visual state
    const platformItem = checkbox.closest('div');
    if (platformItem) {
        if (checkbox.checked) {
            platformItem.style.borderColor = '#667eea';
            platformItem.style.background = '#f8faff';
        } else {
            platformItem.style.borderColor = '#e2e8f0';
            platformItem.style.background = '#ffffff';
        }
    }
    
    updateProceedButtonState();
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
            proceedButton.textContent = `Search ${selectedPlatforms.length} Platform${selectedPlatforms.length > 1 ? 's' : ''}`;
        }
    }
}

// Proceed with selected platforms
function proceedWithSelectedPlatforms() {
    if (selectedPlatforms.length === 0) {
        alert('Please select at least one platform to search.');
        return;
    }
    
    // Reset the flag since user is proceeding with search
    modalOpenedFromSearchAgain = false;
    
    // Close modal
    closePlatformSelectionModal();
    
    // Proceed with search using selected platforms
    proceedWithSearchFlag = true;
    performSearch();
}

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
        // Only hide if notification is currently visible and username is being cleared
        if (isNotificationVisible) {
            document.getElementById('existingDataNotification').style.display = 'none';
            existingDataInfo = null;
            
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
                
                // IMPORTANT: Hide platform status section when notification appears
                const platformStatus = document.getElementById('platformStatus');
                if (platformStatus) {
                    platformStatus.style.display = 'none';
                }
                
                // Hide analysis button
                const analysisButtonContainer = document.getElementById('analysisButtonContainer');
                if (analysisButtonContainer) {
                    analysisButtonContainer.style.display = 'none';
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
    
    // Show platform status section
    const platformStatus = document.getElementById('platformStatus');
    if (platformStatus) {
        platformStatus.style.display = 'block';
    }

    // Update platform statuses
    ['facebook', 'instagram', 'tiktok', 'twitter'].forEach(platform => {
        const statusEl = document.getElementById(`status-${platform}`);
        const platformData = existingDataInfo.platform_data[platform];
        
        if (platformData && platformData.found) {
            statusEl.querySelector('.status-text').textContent = 'Found - Click to view';
            statusEl.style.background = '#f0fdf4';
            statusEl.style.borderColor = '#86efac';
            statusEl.style.cursor = 'pointer';
            statusEl.setAttribute('onclick', `showPlatformResults('${platform}')`);
        } else {
            statusEl.querySelector('.status-text').textContent = 'Not Found';
            statusEl.style.background = '#fef2f2';
            statusEl.style.borderColor = '#fecaca';
            statusEl.style.cursor = 'not-allowed';
            statusEl.removeAttribute('onclick');
        }
    });
    
    // Hide search section
    const searchSection = document.getElementById('searchSection');
    if (searchSection) {
        searchSection.style.display = 'none';
    }
    
    // Show analysis button if at least one platform is found
    const analysisButtonContainer = document.getElementById('analysisButtonContainer');
    if (analysisButtonContainer) {
        const hasFoundPlatform = Object.values(existingDataInfo.platform_data).some(p => p.found);
        if (hasFoundPlatform) {
            analysisButtonContainer.style.display = 'block';
        } else {
            analysisButtonContainer.style.display = 'none';
        }
    }
    
    // Always show clear button container (even if no platforms found)
    // This allows users to clear and search again
    const clearButtonContainer = document.getElementById('clearButtonContainer');
    if (clearButtonContainer) {
        clearButtonContainer.style.display = 'block';
    }
}

// Search again (ignore existing data)
function searchAgain() {
    useExistingDataFlag = false;
    proceedWithSearchFlag = true; // Allow search to proceed
    existingDataInfo = null;
    modalOpenedFromSearchAgain = true; // Mark that modal is being opened from "Search Again"
    document.getElementById('existingDataNotification').style.display = 'none';
    
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
    
    // Reset platform status
    ['facebook', 'instagram', 'tiktok', 'twitter'].forEach(platform => {
        const statusEl = document.getElementById(`status-${platform}`);
        if (statusEl) {
            statusEl.querySelector('.status-text').textContent = 'Searching...';
            statusEl.style.background = '#f8fafc';
            statusEl.style.borderColor = '#e2e8f0';
            statusEl.style.cursor = 'default';
        }
    });
    
    // Show platform selection modal after hiding notification
    openPlatformSelectionModal();
}

async function performSearch() {
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

    const searchButton = document.getElementById('searchButton');
    const platformStatus = document.getElementById('platformStatus');
    const resultsContainer = document.getElementById('resultsContainer');
    
    // Show platform status and initialize only selected platforms
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
    
    console.log('performSearch: Making backend call to search-all with platforms:', selectedPlatforms);
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
        
        // Store search results globally for platform card clicks
        window.searchResultsData = data;

        // Update platform statuses and check if any platform is found
        // Only show status for selected platforms
        let hasFoundPlatform = false;
        const allPlatforms = ['facebook', 'instagram', 'tiktok', 'twitter'];
        
        allPlatforms.forEach(platform => {
            const statusEl = document.getElementById(`status-${platform}`);
            if (!statusEl) return;
            
            // Hide platforms that weren't selected
            if (!selectedPlatforms.includes(platform)) {
                statusEl.style.display = 'none';
                return;
            }
            
            // Show platforms that were selected
            statusEl.style.display = 'flex';
            
            const platformData = data.platforms[platform];
            
            if (platformData && platformData.found) {
                hasFoundPlatform = true;
                statusEl.querySelector('.status-text').textContent = 'Found - Click to view';
                statusEl.style.background = '#f0fdf4';
                statusEl.style.borderColor = '#86efac';
                statusEl.style.cursor = 'pointer';
            } else {
                statusEl.querySelector('.status-text').textContent = 'Not Found';
                statusEl.style.background = '#fef2f2';
                statusEl.style.borderColor = '#fecaca';
                statusEl.style.cursor = 'not-allowed';
                statusEl.title = '';
                statusEl.removeAttribute('onclick');
            }
        });

        // Hide search section after search completes
        const searchSection = document.getElementById('searchSection');
        if (searchSection) {
            searchSection.style.display = 'none';
        }
        
        // Show analysis button if at least one platform is found
        const analysisButtonContainer = document.getElementById('analysisButtonContainer');
        if (analysisButtonContainer) {
            if (hasFoundPlatform) {
                analysisButtonContainer.style.display = 'block';
            } else {
                analysisButtonContainer.style.display = 'none';
            }
        }
        
        // Always show clear button after search completes (even if no platforms found)
        // This allows users to clear and search again
        const clearButtonContainer = document.getElementById('clearButtonContainer');
        if (clearButtonContainer) {
            clearButtonContainer.style.display = 'block';
        }

        // Don't automatically show results - wait for platform card clicks
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'none';
        }

    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while searching. Please try again.');
    } finally {
        if (searchButton) {
            searchButton.disabled = false;
            searchButton.textContent = 'Search All Platforms';
        }
        // Show clear button container after search is done
        const clearButtonContainer = document.getElementById('clearButtonContainer');
        if (clearButtonContainer) {
            clearButtonContainer.style.display = 'block';
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
        // Hide analysis button
        const analysisButtonContainer = document.getElementById('analysisButtonContainer');
        if (analysisButtonContainer) {
            analysisButtonContainer.style.display = 'none';
        }
        // Reset button state
        if (searchButton) {
            searchButton.disabled = false;
            searchButton.textContent = 'Search All Platforms';
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
            searchButton.textContent = 'Search All Platforms';
        }
        // Make sure platform status is hidden
        if (platformStatus) {
            platformStatus.style.display = 'none';
        }
        // Hide analysis button
        const analysisButtonContainer = document.getElementById('analysisButtonContainer');
        if (analysisButtonContainer) {
            analysisButtonContainer.style.display = 'none';
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
    console.log('PROCEEDING WITH SEARCH - All checks passed');
    await performSearch();
    return false; // Prevent any default form submission
});

// Store current displayed platform
let currentDisplayedPlatform = null;

function showPlatformResults(platform) {
    if (!window.searchResultsData) {
        return;
    }
    
    const data = window.searchResultsData;
    const platformData = data.platforms[platform];
    const resultsContainer = document.getElementById('resultsContainer');
    
    // Don't show results if platform not found
    if (!platformData || !platformData.found) {
        return;
    }
    
    // If clicking the same platform, toggle it off
    if (currentDisplayedPlatform === platform && resultsContainer.style.display === 'block') {
        resultsContainer.style.display = 'none';
        resultsContainer.innerHTML = '';
        currentDisplayedPlatform = null;
        return;
    }
    
    // Show platform results
    let html = '<h3 style="font-size: 18px; font-weight: 600; color: #374151; margin-bottom: 20px;">Results for: <strong>' + data.username + '</strong></h3>';
    
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
    resultsContainer.style.display = 'block';
    currentDisplayedPlatform = platform;
    
    // Initialize tooltips after results are displayed
    setTimeout(initTooltips, 100);
    
    // Scroll to results
    resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
    let html = `<div style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">`;
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
        
        // Add posts inside engagement metrics section with pagination
        const posts = data.recent_posts || data.recent_media || data.recent_videos || [];
        if (posts.length > 0) {
            const postsPerPage = 5;
            const totalPages = Math.ceil(posts.length / postsPerPage);
            const uniqueId = 'posts-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            
            html += `<div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.2);">`;
            html += `<h4 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0; color: white;">Recent Posts (${posts.length} total)</h4>`;
            html += `<div id="${uniqueId}-container" style="display: grid; gap: 12px;">`;
            
            // Render all posts but hide them initially (we'll show first page)
            posts.forEach((post, index) => {
                const isVisible = index < postsPerPage ? '' : 'display: none;';
                html += `<div class="post-item-${uniqueId}" style="background: rgba(255,255,255,0.15); padding: 16px; border-radius: 8px; backdrop-filter: blur(10px); ${isVisible}">`;
                
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
            
            // Pagination controls
            if (totalPages > 1) {
                html += `<div id="${uniqueId}-pagination" style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap;">`;
                html += `<button onclick="changePostsPage('${uniqueId}', 0, ${postsPerPage}, ${posts.length})" id="${uniqueId}-prev" style="padding: 8px 16px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s;" disabled onmouseover="this.style.background='rgba(255,255,255,0.3)';" onmouseout="this.style.background='rgba(255,255,255,0.2)';">Previous</button>`;
                html += `<span style="color: white; font-size: 14px; padding: 0 12px;">Page <span id="${uniqueId}-current">1</span> of ${totalPages}</span>`;
                html += `<button onclick="changePostsPage('${uniqueId}', 1, ${postsPerPage}, ${posts.length})" id="${uniqueId}-next" style="padding: 8px 16px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s;" ${totalPages === 1 ? 'disabled' : ''} onmouseover="if(!this.disabled) this.style.background='rgba(255,255,255,0.3)';" onmouseout="if(!this.disabled) this.style.background='rgba(255,255,255,0.2)';">Next</button>`;
                html += `</div>`;
                
                // Store pagination state
                window[`${uniqueId}_page`] = 1;
            }
            
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
    
    // Hide analysis button container
    const analysisButtonContainer = document.getElementById('analysisButtonContainer');
    if (analysisButtonContainer) {
        analysisButtonContainer.style.display = 'none';
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
    
    // Hide clear button container
    const clearButtonContainer = document.getElementById('clearButtonContainer');
    if (clearButtonContainer) {
        clearButtonContainer.style.display = 'none';
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
        searchButton.textContent = 'Search All Platforms';
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

document.addEventListener('DOMContentLoaded', function() {
    initTooltips();
    
    // Close tooltips when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!e.target.closest('.info-tooltip')) {
                document.querySelectorAll('.info-tooltip').forEach(t => t.classList.remove('active'));
            }
        }
    });
});

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
    
    // Show modal with platform selection
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
            <p style="color: #64748b; margin-bottom: 24px; text-align: center; font-size: 14px;">Choose which platforms to include in the AI analysis</p>
            
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
            if (modalTitle) modalTitle.textContent = 'AI Professional Analysis';
        } else {
            politicalDiv.style.borderColor = '#667eea';
            politicalDiv.style.background = '#f0f4ff';
            professionalDiv.style.borderColor = '#e2e8f0';
            professionalDiv.style.background = '#ffffff';
            if (modalTitle) modalTitle.textContent = 'AI Political Analysis';
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
                <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">AI ${analysisTypeLabel} Analysis</h3>
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

async function startAnalysis(analysisType = 'professional') {
    if (!currentAnalysisData) {
        return;
    }
    
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
    const modalContent = document.getElementById('analysisModalContent');
    if (!modalContent) return;
    
    // Wrap the HTML in a scrollable container
    let fullHtml = '<div style="max-height: 80vh; overflow-y: auto; padding-right: 8px;">' + html;
    
    // Add button to view full analysis page at the bottom
    if (analysisId) {
        const analysisUrl = `${APP_BASE_PATH}/social-media/${analysisId}`;
        fullHtml += `<div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 2px solid #e2e8f0;">`;
        fullHtml += `<a href="${analysisUrl}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; border: none; border-radius: 8px; font-weight: 700; font-size: 16px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.5); cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(102, 126, 234, 0.5)';" onclick="closeAnalysisModal();">View Full Analysis Page</a>`;
        fullHtml += `<p style="margin-top: 12px; color: #64748b; font-size: 13px;">Open in a new page for better viewing and PDF export</p>`;
        fullHtml += `</div>`;
    }
    
    fullHtml += '</div>';
    
    modalContent.innerHTML = fullHtml;
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
            <h2 id="analysisModalTitle" style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">AI Analysis</h2>
            <button onclick="closeAnalysisModal()" style="background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b';" onmouseout="this.style.background='none'; this.style.color='#64748b';">&times;</button>
        </div>
        <div id="analysisModalContent" style="flex: 1; overflow-y: auto; padding: 24px;">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<style>
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
@endsection
