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

            <!-- Search Form -->
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
                    style="width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';"
                >
                    Search All Platforms
                </button>
                
                <!-- Available Platforms -->
                <p style="text-align: center; margin-top: 12px; color: #64748b; font-size: 13px;">
                    (Facebook, Instagram, TikTok)
                </p>
                
                <button 
                    type="button"
                    id="clearButton"
                    onclick="clearResults()"
                    style="width: 100%; margin-top: 12px; padding: 12px 24px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; display: none;"
                    onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';"
                    onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';"
                >
                    Clear
                </button>
            </form>

            <!-- Platform Status -->
            <div id="platformStatus" style="display: none; margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px;">Searching Platforms...</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
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
                </div>
            </div>

            <!-- Analysis Button Container -->
            <div id="analysisButtonContainer" style="display: none; margin-top: 24px; margin-bottom: 24px;">
                <button onclick="openAnalysisModal('all', null)" style="width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';">
                    Start Analyzing Profile
                </button>
            </div>

            <!-- Results Container -->
            <div id="resultsContainer" style="display: none;"></div>
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

// Check for existing data when user types (with debounce)
function checkExistingData(username) {
    // Clear previous timeout
    if (checkDataTimeout) {
        clearTimeout(checkDataTimeout);
    }

    if (!username || username.length < 2) {
        document.getElementById('existingDataNotification').style.display = 'none';
        existingDataInfo = null;
        return;
    }

    // Debounce: wait 500ms after user stops typing
    checkDataTimeout = setTimeout(async () => {
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
                existingDataInfo = null;
                document.getElementById('existingDataNotification').style.display = 'none';
            }
        } catch (error) {
            console.error('Error checking existing data:', error);
            // Don't show error to user, just continue
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

    // Update platform statuses
    ['facebook', 'instagram', 'tiktok'].forEach(platform => {
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

    // Show platform status
    document.getElementById('platformStatus').style.display = 'block';
    
    // Hide notification
    document.getElementById('existingDataNotification').style.display = 'none';
    
    // Show clear button
    const clearButton = document.getElementById('clearButton');
    if (clearButton) {
        clearButton.style.display = 'block';
    }
}

// Search again (ignore existing data)
function searchAgain() {
    useExistingDataFlag = false;
    proceedWithSearchFlag = true; // Allow search to proceed
    existingDataInfo = null;
    document.getElementById('existingDataNotification').style.display = 'none';
    
    // Reset platform status
    ['facebook', 'instagram', 'tiktok'].forEach(platform => {
        const statusEl = document.getElementById(`status-${platform}`);
        if (statusEl) {
            statusEl.querySelector('.status-text').textContent = 'Searching...';
            statusEl.style.background = '#f8fafc';
            statusEl.style.borderColor = '#e2e8f0';
            statusEl.style.cursor = 'default';
        }
    });
    
    // Clear results
    const resultsContainer = document.getElementById('resultsContainer');
    if (resultsContainer) {
        resultsContainer.innerHTML = '';
        resultsContainer.style.display = 'none';
    }
    
    // Show platform status
    const platformStatus = document.getElementById('platformStatus');
    if (platformStatus) {
        platformStatus.style.display = 'block';
    }
    
    // Hide analysis button
    const analysisButtonContainer = document.getElementById('analysisButtonContainer');
    if (analysisButtonContainer) {
        analysisButtonContainer.style.display = 'none';
    }
    
    // Trigger search
    const searchButton = document.getElementById('searchButton');
    if (searchButton) {
        searchButton.disabled = true;
        searchButton.textContent = 'Searching...';
    }
    
    document.getElementById('searchForm').dispatchEvent(new Event('submit'));
}

document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    if (!username) return;

    const searchButton = document.getElementById('searchButton');
    const platformStatus = document.getElementById('platformStatus');
    const resultsContainer = document.getElementById('resultsContainer');
    
    // If using existing data, skip search
    if (useExistingDataFlag && existingDataInfo) {
        // Data already loaded by useExistingData(), just return
        return;
    }

    // Check if existing data notification is shown - if so, wait for user response
    const notification = document.getElementById('existingDataNotification');
    if (notification && notification.style.display !== 'none' && existingDataInfo && !useExistingDataFlag && !proceedWithSearchFlag) {
        // Existing data found but user hasn't chosen yet - don't proceed with search
        // Reset button state
        searchButton.disabled = false;
        searchButton.textContent = 'Search All Platforms';
        return;
    }
    
    // Reset proceed flag after check
    proceedWithSearchFlag = false;

    // Reset UI for new search
    searchButton.disabled = true;
    searchButton.textContent = 'Searching...';
    platformStatus.style.display = 'block';
    resultsContainer.style.display = 'none';
    resultsContainer.innerHTML = '';
    
    // Reset all platform statuses
    ['facebook', 'instagram', 'tiktok'].forEach(platform => {
        const statusEl = document.getElementById(`status-${platform}`);
        if (statusEl) {
        statusEl.querySelector('.status-text').textContent = 'Searching...';
        statusEl.style.background = '#f8fafc';
        statusEl.style.borderColor = '#e2e8f0';
        }
    });

    try {
        const response = await fetch('{{ route("social-media.search-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ username: username })
        });

        const data = await response.json();
        
        // Store search results globally for platform card clicks
        window.searchResultsData = data;

        // Update platform statuses and check if any platform is found
        let hasFoundPlatform = false;
        ['facebook', 'instagram', 'tiktok'].forEach(platform => {
            const statusEl = document.getElementById(`status-${platform}`);
            const platformData = data.platforms[platform];
            
            if (platformData.found) {
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
                statusEl.title = ''; // Remove error tooltip
                // Remove onclick for not found platforms
                statusEl.removeAttribute('onclick');
            }
        });

        // Show analysis button if at least one platform is found
        const analysisButtonContainer = document.getElementById('analysisButtonContainer');
        if (analysisButtonContainer) {
            if (hasFoundPlatform) {
                analysisButtonContainer.style.display = 'block';
            } else {
                analysisButtonContainer.style.display = 'none';
            }
        }

        // Don't automatically show results - wait for platform card clicks
        // Clear any existing results
        resultsContainer.innerHTML = '';
        resultsContainer.style.display = 'none';

    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while searching. Please try again.');
    } finally {
        searchButton.disabled = false;
        searchButton.textContent = 'Search All Platforms';
        // Show clear button after search is done
        const clearButton = document.getElementById('clearButton');
        if (clearButton) {
            clearButton.style.display = 'block';
        }
    }
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
        'tiktok': 'TikTok'
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
    
    document.getElementById('resultsContainer').innerHTML = html;
}

function getPlatformIcon(platformType) {
    if (platformType === 'facebook') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #1877F2;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>`;
    } else if (platformType === 'instagram') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px;"><defs><linearGradient id="instagram-gradient-result" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#833AB4;stop-opacity:1" /><stop offset="50%" style="stop-color:#FD1D1D;stop-opacity:1" /><stop offset="100%" style="stop-color:#FCAF45;stop-opacity:1" /></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient-result)"/></svg>`;
    } else if (platformType === 'tiktok') {
        return `<svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: #000000;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>`;
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
         platformType === 'tiktok' ? `https://www.tiktok.com/@${data.username}` : null) : null);
    
    if (profileUrl) {
        html += `<div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">`;
        html += `<a href="${profileUrl}" target="_blank" rel="noopener noreferrer" style="color: #667eea; text-decoration: none; font-size: 14px; word-break: break-all; display: inline-flex; align-items: center; gap: 6px;">`;
        html += `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>`;
        html += `<span>${profileUrl}</span>`;
        html += `</a></div>`;
    }
    
    // Profile info (skip for Instagram and TikTok to match Facebook layout)
    if (platformType !== 'instagram' && platformType !== 'tiktok') {
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
    ['facebook', 'instagram', 'tiktok'].forEach(platform => {
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
    
    // Hide clear button
    const clearButton = document.getElementById('clearButton');
    if (clearButton) {
        clearButton.style.display = 'none';
    }
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
let selectedPlatforms = {}; // Store selected platforms for analysis

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
        'tiktok': 'TikTok'
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
    selectedPlatforms = {};
    availablePlatforms.forEach(platform => {
        selectedPlatforms[platform.key] = true;
    });
    
    // Build platform selection UI
    let html = `
        <div style="padding: 32px 24px;">
            <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 8px; text-align: center;">Select Platforms for Analysis</h3>
            <p style="color: #64748b; margin-bottom: 32px; text-align: center; font-size: 14px;">Choose which platforms to include in the AI analysis</p>
            
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
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const isChecked = selectAllCheckbox.checked;
    
    // Update all platform checkboxes
    Object.keys(selectedPlatforms).forEach(platform => {
        selectedPlatforms[platform] = isChecked;
        const checkbox = document.getElementById(`platform_${platform}`);
        if (checkbox) {
            checkbox.checked = isChecked;
        }
    });
}

function togglePlatform(platform) {
    const checkbox = document.getElementById(`platform_${platform}`);
    if (checkbox) {
        selectedPlatforms[platform] = checkbox.checked;
        
        // Update "Select All" checkbox based on individual selections
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox) {
            const allSelected = Object.values(selectedPlatforms).every(selected => selected);
            selectAllCheckbox.checked = allSelected;
        }
    }
}

function proceedWithAnalysis() {
    // Check if at least one platform is selected
    const hasSelection = Object.values(selectedPlatforms).some(selected => selected);
    if (!hasSelection) {
        alert('Please select at least one platform to analyze.');
        return;
    }
    
    // Filter currentAnalysisData to only include selected platforms
    const filteredData = {};
    Object.keys(currentAnalysisData).forEach(platform => {
        if (selectedPlatforms[platform]) {
            filteredData[platform] = currentAnalysisData[platform];
        }
    });
    
    // Update currentAnalysisData with filtered data
    currentAnalysisData = filteredData;
    
    // Show loading state
    const modalContent = document.getElementById('analysisModalContent');
    if (modalContent) {
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px 20px;">
                <div style="font-size: 48px; margin-bottom: 16px;">🤖</div>
                <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">AI Professional Analysis</h3>
                <p style="color: #64748b; margin-bottom: 24px;">Analyzing social media profiles...</p>
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            </div>
        `;
    }
    
    // Start analysis
    startAnalysis();
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

async function startAnalysis() {
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
                use_existing: useExisting
            })
        });
        
        const result = await response.json();
        
        if (result.success && result.analysis) {
            displayAnalysisResult(result.analysis);
            // If analysis was saved, show option to view saved analysis
            if (result.analysis_id) {
                window.lastAnalysisId = result.analysis_id;
                // Add a button to view saved analysis
                const modalContent = document.getElementById('analysisModalContent');
                if (modalContent) {
                    const viewSavedBtn = document.createElement('div');
                    viewSavedBtn.style.cssText = 'margin-top: 24px; text-align: center; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.2);';
                    viewSavedBtn.innerHTML = `<a href="/social-media/${result.analysis_id}" style="display: inline-block; padding: 12px 24px; background: rgba(255,255,255,0.25); color: white; border: 2px solid rgba(255,255,255,0.5); border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; transition: all 0.3s ease;">View Saved Analysis</a>`;
                    modalContent.appendChild(viewSavedBtn);
                }
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
<div id="analysisModal" onclick="if(event.target.id === 'analysisModal') closeAnalysisModal();" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
    <div onclick="event.stopPropagation();" style="background: white; border-radius: 16px; max-width: 900px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="padding: 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">AI Professional Analysis</h2>
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
