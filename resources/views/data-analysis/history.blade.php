@extends('layouts.app')

@section('content')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
    
    .cursor-sidebar-header {
        padding: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
        margin-bottom: 16px;
    }
    
    .cursor-sidebar-content {
        flex: 1;
        padding: 0;
        padding-bottom: 16px;
        overflow-y: auto;
        overflow-x: visible;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    
    .cursor-main-content {
        flex: 1;
        padding: 24px 16px;
        max-width: 100%;
        width: 100%;
        position: relative;
        z-index: 1;
        min-height: 100%;
    }
    
    .cursor-section {
        margin-bottom: 24px;
        padding: 0 16px;
    }
    
    .cursor-section:first-of-type {
        padding-top: 0;
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
    
    .analysis-tile {
        padding: 12px;
        padding-right: 36px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: block;
        position: relative;
    }
    
    .analysis-tile:hover {
        background: #f8fafc;
        border-color: #667eea;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    
    .analysis-tile-title {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        line-height: 1.4;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .analysis-tile-menu {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        color: #64748b;
        font-size: 16px;
        cursor: pointer;
        z-index: 10;
        padding: 4px;
        border-radius: 4px;
    }
    
    .analysis-tile-wrapper:hover .analysis-tile-menu {
        opacity: 1;
        visibility: visible;
    }
    
    .analysis-tile-menu:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    .analysis-tile-wrapper {
        position: relative;
        margin-bottom: 8px;
    }
    
    .analysis-tile-context-menu {
        position: absolute;
        bottom: calc(100% + 4px);
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 140px;
        z-index: 1000;
        display: none;
        overflow: hidden;
    }
    
    .analysis-tile-context-menu.show {
        display: block;
    }
    
    .analysis-tile-context-menu-item {
        padding: 10px 16px;
        font-size: 13px;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .analysis-tile-context-menu-item:hover {
        background: #f8fafc;
    }
    
    .analysis-tile-context-menu-item.active {
        background: #eff6ff;
        color: #2563eb;
        font-weight: 500;
    }
    
    .analysis-tile-context-menu-item.active:hover {
        background: #dbeafe;
    }
    
    .analysis-tile-context-menu-item.delete {
        color: #ef4444;
    }
    
    .analysis-tile-context-menu-item.delete:hover {
        background: #fef2f2;
    }
    
    .analysis-tile.active {
        background: #eff6ff;
        border-color: #667eea;
    }
    
    /* Loading spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @media (max-width: 1024px) {
        .cursor-layout {
            flex-direction: column;
            height: auto;
        }
        
        .cursor-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
            order: 1;
            max-height: 50vh;
        }
        
        .cursor-main {
            border-left: none;
            border-top: 1px solid #e5e7eb;
            order: 2;
        }
    }
    
    @media (max-width: 768px) {
        .cursor-main-content {
            padding: 20px 16px;
        }
    }
</style>

<div class="cursor-layout">
    <!-- Left Panel: Filters & Stats -->
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-content">
            <div class="cursor-sidebar-header">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Data History</h2>
                <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Filter and manage your analyses</p>
            </div>

            <!-- Search and Filter Section -->
            <div class="cursor-section">
                <div class="cursor-section-title" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;" onclick="toggleFilterSection()">
                    <span>Search & Filter</span>
                    <i class="bi bi-chevron-down" id="filterToggleIcon" style="font-size: 12px; color: #6b7280; transition: transform 0.3s ease;"></i>
                </div>
                <div id="filterSectionContent" style="overflow: hidden; transition: max-height 0.3s ease;">
                    <form method="GET" action="{{ route('data-analysis.history') }}" id="filterForm">
                        <!-- Search and Status Row -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                            <!-- Search Input -->
                            <div>
                                <label for="search" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">Search</label>
                                <div style="position: relative;">
                                    <input type="text" 
                                           id="search" 
                                           name="search" 
                                           value="{{ request('search') }}"
                                           placeholder="Search..."
                                           style="width: 100%; padding: 6px 8px 6px 28px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                                    <i class="bi bi-search" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 12px;"></i>
                                </div>
                            </div>
                            
                            <!-- Status Filter -->
                            <div>
                                <label for="status" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">Status</label>
                                <select id="status" 
                                        name="status" 
                                        style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff; cursor: pointer;">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Date From and To Row -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                            <!-- Date From -->
                            <div>
                                <label for="date_from" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">From Date</label>
                                <input type="date" 
                                       id="date_from" 
                                       name="date_from" 
                                       value="{{ request('date_from') }}"
                                       style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                            </div>
                            
                            <!-- Date To -->
                            <div>
                                <label for="date_to" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">To Date</label>
                                <input type="date" 
                                       id="date_to" 
                                       name="date_to" 
                                       value="{{ request('date_to') }}"
                                       style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('data-analysis.history') }}" 
                               onclick="sessionStorage.setItem('dataAnalysisHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);"
                               style="flex: 1; padding: 6px 10px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 4px; font-weight: 500; font-size: 11px; text-decoration: none; text-align: center; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                <i class="bi bi-x-circle" style="font-size: 11px;"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Analyses Tiles -->
            <div class="cursor-section">
                <div class="cursor-section-title">All Analyses</div>
                <div style="padding-right: 4px;">
                    @forelse($analyses as $analysis)
                        <div class="analysis-tile-wrapper" data-analysis-id="{{ $analysis->id }}" style="position: relative;">
                            <a href="javascript:void(0);" 
                               class="analysis-tile {{ request()->route('dataAnalysis') == $analysis->id ? 'active' : '' }}"
                               onclick="event.preventDefault(); loadAnalysisResults({{ $analysis->id }}, this);">
                                <p class="analysis-tile-title">{{ $analysis->file_name }}</p>
                            </a>
                            <i class="bi bi-three-dots analysis-tile-menu" 
                               onclick="event.stopPropagation(); toggleContextMenu(event, {{ $analysis->id }}, '{{ Str::limit($analysis->file_name, 50) }}', {{ $analysis->status === 'completed' ? 'true' : 'false' }})"></i>
                            <div class="analysis-tile-context-menu" id="contextMenu{{ $analysis->id }}">
                                <div class="analysis-tile-context-menu-item" onclick="event.stopPropagation(); closeContextMenu(); previewExcelData({{ $analysis->id }}, this.closest('.analysis-tile-wrapper').querySelector('.analysis-tile'))">
                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                    <span>Raw Data</span>
                                </div>
                                <div class="analysis-tile-context-menu-item delete" onclick="event.stopPropagation(); closeContextMenu(); confirmDelete({{ $analysis->id }}, '{{ Str::limit($analysis->file_name, 50) }}')">
                                    <i class="bi bi-trash"></i>
                                    <span>Delete</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 24px 12px; color: #9ca3af; font-size: 12px;">
                            <i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
                            No analyses found
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
                                
    <!-- Right Panel: Analysis Results -->
    <div class="cursor-main scrollable" id="analysisResultsPanel">
        <div class="cursor-main-content" id="analysisResultsContent" style="display: none;">
            <!-- Content will be loaded here via AJAX -->
        </div>
        <div id="analysisResultsEmpty" style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af; font-size: 14px;">
            <div style="text-align: center;">
                <i class="bi bi-file-earmark-text" style="font-size: 48px; display: block; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>Select an analysis to view results</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #ef4444;">⚠️</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Confirm Deletion</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you sure you want to delete this data analysis?</p>
        <p id="deleteFileName" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
        <p style="color: #ef4444; margin-bottom: 24px; font-size: 14px; font-weight: 500;">This action cannot be undone.</p>
        
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button onclick="closeDeleteModal()" 
                    style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer;">
                Cancel
            </button>
            <button id="confirmDeleteBtn" 
                    style="padding: 12px 24px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);">
                Delete Analysis
            </button>
        </div>
    </div>
</div>

<script>
let currentDeleteId = null;
let searchTimeout = null;
let currentDisplayedAnalysisId = null;
let currentViewType = null; // 'preview' or 'analysis'

// Toggle filter section collapse/expand
function toggleFilterSection() {
    const content = document.getElementById('filterSectionContent');
    const icon = document.getElementById('filterToggleIcon');
    const isCollapsed = content.style.maxHeight === '0px' || content.style.maxHeight === '';
    
    if (isCollapsed) {
        // Expand
        content.style.maxHeight = content.scrollHeight + 'px';
        icon.style.transform = 'rotate(0deg)';
        sessionStorage.setItem('filterSectionCollapsed', 'false');
    } else {
        // Collapse
        content.style.maxHeight = '0px';
        icon.style.transform = 'rotate(-90deg)';
        sessionStorage.setItem('filterSectionCollapsed', 'true');
    }
}

// Real-time filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filter section collapse state - always start collapsed
    const filterContent = document.getElementById('filterSectionContent');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    // Always start collapsed on page load
    filterContent.style.maxHeight = '0px';
    filterIcon.style.transform = 'rotate(-90deg)';
    
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('search');
    const statusSelect = document.getElementById('status');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    
    // Restore scroll position if it was saved
    const savedScrollPosition = sessionStorage.getItem('dataAnalysisHistoryScrollPosition');
    if (savedScrollPosition) {
        setTimeout(function() {
            window.scrollTo(0, parseInt(savedScrollPosition));
            sessionStorage.removeItem('dataAnalysisHistoryScrollPosition');
        }, 100);
    }
    
    // Function to save scroll position before form submission
    function saveScrollPosition() {
        sessionStorage.setItem('dataAnalysisHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);
    }
        
    // Debounced search function (wait 500ms after user stops typing)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                saveScrollPosition();
                filterForm.submit();
            }, 500);
        });
    }
        
    // Immediate filter for status and date changes
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            saveScrollPosition();
            filterForm.submit();
        });
    }
        
    if (dateFromInput) {
        dateFromInput.addEventListener('change', function() {
            saveScrollPosition();
            filterForm.submit();
        });
    }
        
    if (dateToInput) {
        dateToInput.addEventListener('change', function() {
            saveScrollPosition();
            filterForm.submit();
        });
    }
});

function confirmDelete(analysisId, fileName) {
    currentDeleteId = analysisId;
    document.getElementById('deleteFileName').textContent = fileName;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentDeleteId = null;
}

function deleteAnalysis() {
    if (!currentDeleteId) {
        showToast('Error: Analysis ID not found', 'error');
        return;
    }
    
    const analysisId = currentDeleteId;
    
    // Close the modal first
    closeDeleteModal();
    
    // Show loading message
    showToast('Deleting analysis...', 'success');
    
    // Make AJAX request to delete
    fetch('{{ url("/data-analysis") }}/' + analysisId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Failed to delete analysis');
            });
        }
        return response.json();
    })
    .then(data => {
        // Show success message
        showToast('Analysis deleted successfully!', 'success');
        
        // Remove the analysis tile from the UI
        const tileWrapper = document.querySelector(`[data-analysis-id="${analysisId}"]`);
        if (tileWrapper) {
            tileWrapper.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            tileWrapper.style.opacity = '0';
            tileWrapper.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                tileWrapper.remove();
                
                // Check if there are no more analyses
                const remainingTiles = document.querySelectorAll('.analysis-tile-wrapper');
                if (remainingTiles.length === 0) {
                    const emptyState = document.querySelector('.cursor-sidebar-content').querySelector('div[style*="text-align: center"]');
                    if (!emptyState || !emptyState.textContent.includes('No analyses found')) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.style.cssText = 'text-align: center; padding: 24px 12px; color: #9ca3af; font-size: 12px;';
                        emptyDiv.innerHTML = '<i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>No analyses found';
                        document.querySelector('.cursor-sidebar-content').appendChild(emptyDiv);
                    }
                }
            }, 300);
        }
        
        // Clear the right panel if this analysis was being displayed
        if (currentDisplayedAnalysisId === analysisId) {
            currentDisplayedAnalysisId = null;
            currentViewType = null;
            const resultsContent = document.getElementById('analysisResultsContent');
            const resultsEmpty = document.getElementById('analysisResultsEmpty');
            if (resultsContent) {
                resultsContent.style.display = 'none';
            }
            if (resultsEmpty) {
                resultsEmpty.style.display = 'flex';
            }
        }
    })
    .catch(error => {
        // Show error message
        showToast(error.message || 'Failed to delete analysis. Please try again.', 'error');
    });
}

// Set up the confirm delete button
document.getElementById('confirmDeleteBtn').onclick = deleteAnalysis;

// Close modal when clicking outside
document.getElementById('deleteModal').onclick = function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
};

// Excel Data Preview functions
function previewExcelData(analysisId, tileElement) {
    // Check if this tile is already active and displaying preview - if so, switch to analysis
    if (tileElement && tileElement.classList.contains('active') && currentDisplayedAnalysisId === analysisId && currentViewType === 'preview') {
        // Switch to analysis view
        loadAnalysisResults(analysisId, tileElement);
        return;
    }
    
    // Set active tile
    setActiveTile(tileElement);
    
    // Update current displayed analysis ID and view type
    currentDisplayedAnalysisId = analysisId;
    currentViewType = 'preview';
    
    // Show loading state in right panel
    const resultsContent = document.getElementById('analysisResultsContent');
    const resultsEmpty = document.getElementById('analysisResultsEmpty');
    
    resultsEmpty.style.display = 'none';
    resultsContent.style.display = 'flex';
    resultsContent.style.alignItems = 'center';
    resultsContent.style.justifyContent = 'center';
    resultsContent.style.minHeight = '100%';
    resultsContent.innerHTML = '<div style="text-align: center; color: #64748b;"><i class="bi bi-hourglass-split" style="font-size: 48px; display: block; margin-bottom: 16px; animation: spin 1s linear infinite;"></i><p>Loading Excel data...</p></div>';
    
    // Fetch Excel data
    fetch('{{ url("/data-analysis") }}/' + analysisId + '/excel-preview', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load Excel data');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.excel_data) {
            displayExcelPreview(data.excel_data, resultsContent);
        } else {
            resultsContent.style.display = 'flex';
            resultsContent.style.alignItems = 'center';
            resultsContent.style.justifyContent = 'center';
            resultsContent.style.minHeight = '100%';
            resultsContent.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 48px;"><i class="bi bi-exclamation-triangle" style="font-size: 48px; display: block; margin-bottom: 16px;"></i><p>Failed to load Excel data</p></div>';
        }
    })
    .catch(error => {
        console.error('Error loading Excel data:', error);
        resultsContent.style.display = 'flex';
        resultsContent.style.alignItems = 'center';
        resultsContent.style.justifyContent = 'center';
        resultsContent.style.minHeight = '100%';
        resultsContent.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 48px;"><i class="bi bi-exclamation-triangle" style="font-size: 48px; display: block; margin-bottom: 16px;"></i><p>Failed to load Excel data</p><p style="font-size: 12px; color: #9ca3af; margin-top: 8px;">' + error.message + '</p></div>';
    });
}

function displayExcelPreview(excelData, container) {
    if (!excelData || !excelData.sheets || excelData.sheets.length === 0) {
        container.style.display = 'flex';
        container.style.alignItems = 'center';
        container.style.justifyContent = 'center';
        container.style.minHeight = '100%';
        container.innerHTML = '<div style="text-align: center; padding: 48px; color: #64748b;"><i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 16px; opacity: 0.5;"></i><p>No Excel data available</p></div>';
        return;
    }
    
    let html = '<div style="width: 100%;">';
    
    excelData.sheets.forEach((sheet, sheetIndex) => {
        html += `<div style="margin-bottom: 32px;">`;
        html += `<h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0;">${sheet.sheet_name || 'Sheet ' + (sheetIndex + 1)}</h4>`;
        
        if (sheet.headers && sheet.headers.length > 0) {
            html += `<div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 8px;">`;
            html += `<table style="width: 100%; border-collapse: collapse; font-size: 13px;">`;
            
            // Header row
            html += `<thead><tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">`;
            sheet.headers.forEach(header => {
                html += `<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e2e8f0;">${header || ''}</th>`;
            });
            html += `</tr></thead>`;
            
            // Data rows (limit to first 100 rows for performance)
            html += `<tbody>`;
            const maxRows = Math.min(sheet.rows ? sheet.rows.length : 0, 100);
            for (let i = 0; i < maxRows; i++) {
                const row = sheet.rows[i];
                html += `<tr style="border-bottom: 1px solid #f1f5f9;">`;
                sheet.headers.forEach(header => {
                    const value = row && row[header] !== undefined ? row[header] : '';
                    const displayValue = value !== null && value !== undefined ? String(value) : '';
                    html += `<td style="padding: 10px 12px; color: #64748b; border-right: 1px solid #f1f5f9; white-space: nowrap; max-width: 200px; overflow: hidden; text-overflow: ellipsis;" title="${displayValue.replace(/"/g, '&quot;')}">${displayValue.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</td>`;
                });
                html += `</tr>`;
            }
            html += `</tbody>`;
            
            html += `</table>`;
            html += `</div>`;
            
            if (sheet.rows && sheet.rows.length > 100) {
                html += `<p style="color: #64748b; font-size: 12px; margin-top: 8px; font-style: italic;">Showing first 100 of ${sheet.rows.length} rows</p>`;
            }
        } else {
            html += `<p style="color: #64748b; padding: 24px; text-align: center;">No data in this sheet</p>`;
        }
        
        html += `</div>`;
    });
    
    html += '</div>';
    
    container.style.display = 'block';
    container.style.width = '100%';
    container.style.minHeight = 'auto';
    container.innerHTML = html;
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeContextMenu();
    }
});

// Load analysis results in right panel
function loadAnalysisResults(analysisId, tileElement) {
    // Check if this tile is already active and displaying the same analysis with same view
    if (tileElement && tileElement.classList.contains('active') && currentDisplayedAnalysisId === analysisId && currentViewType === 'analysis') {
        // Already displaying this analysis, don't reload
        return;
    }
    
    // Set active tile
    setActiveTile(tileElement);
    
    // Update current displayed analysis ID and view type
    currentDisplayedAnalysisId = analysisId;
    currentViewType = 'analysis';
    
    // Show loading state
    const resultsContent = document.getElementById('analysisResultsContent');
    const resultsEmpty = document.getElementById('analysisResultsEmpty');
    
    resultsEmpty.style.display = 'none';
    resultsContent.style.display = 'flex';
    resultsContent.style.alignItems = 'center';
    resultsContent.style.justifyContent = 'center';
    resultsContent.style.minHeight = '100%';
    resultsContent.innerHTML = '<div style="text-align: center; color: #64748b;"><i class="bi bi-hourglass-split" style="font-size: 48px; display: block; margin-bottom: 16px; animation: spin 1s linear infinite;"></i><p>Loading analysis results...</p></div>';
    
    // Fetch analysis HTML content
    fetch('{{ url("/data-analysis") }}/' + analysisId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load analysis');
        }
        return response.text();
    })
    .then(html => {
        // Always use consistent container styling
        resultsContent.style.display = 'block';
        resultsContent.style.width = '100%';
        resultsContent.style.minHeight = 'auto';
        
        // Insert HTML directly
        resultsContent.innerHTML = html;
        
        // Extract and execute script tags from the loaded HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const scripts = tempDiv.querySelectorAll('script');
        
        // Function to execute scripts sequentially
        function executeScripts(index) {
            if (index >= scripts.length) {
                // All scripts executed, now try to initialize charts
                setTimeout(() => {
                    // Find all functions that match initializeCharts pattern
                    const functionNames = Object.keys(window).filter(key => 
                        key.startsWith('initializeCharts') && typeof window[key] === 'function'
                    );
                    
                    // Call the most recent one (should be the one from loaded content)
                    if (functionNames.length > 0) {
                        const lastFunction = functionNames[functionNames.length - 1];
                        window[lastFunction]();
                    } else {
                        // Fallback: try to find and execute initializeCharts directly
                        if (typeof initializeCharts === 'function') {
                            initializeCharts();
                        }
                    }
                }, 200);
                return;
            }
            
            const oldScript = scripts[index];
            const newScript = document.createElement('script');
            
            // Copy attributes
            Array.from(oldScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });
            
            // Handle script content
            if (oldScript.src) {
                // External script - load it
                newScript.onload = function() {
                    executeScripts(index + 1);
                };
                newScript.onerror = function() {
                    executeScripts(index + 1);
                };
            } else {
                // Inline script - copy content and execute
                newScript.textContent = oldScript.textContent;
                // Execute inline script immediately
                try {
                    eval(oldScript.textContent);
                } catch (e) {
                    console.error('Error executing script:', e);
                }
                // Continue to next script
                setTimeout(() => executeScripts(index + 1), 0);
                return;
            }
            
            document.head.appendChild(newScript);
        }
        
        // Ensure Chart.js is loaded first
        if (typeof Chart === 'undefined') {
            const chartScript = document.createElement('script');
            chartScript.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
            chartScript.onload = function() {
                // Chart.js loaded, now execute other scripts
                executeScripts(0);
            };
            chartScript.onerror = function() {
                console.error('Failed to load Chart.js');
                executeScripts(0);
            };
            document.head.appendChild(chartScript);
        } else {
            // Chart.js already loaded, execute scripts
            executeScripts(0);
        }
    })
    .catch(error => {
        console.error('Error loading analysis:', error);
        resultsContent.style.display = 'flex';
        resultsContent.style.alignItems = 'center';
        resultsContent.style.justifyContent = 'center';
        resultsContent.style.minHeight = '100%';
        resultsContent.innerHTML = '<div style="text-align: center; color: #ef4444;"><i class="bi bi-exclamation-triangle" style="font-size: 48px; display: block; margin-bottom: 16px;"></i><p>Failed to load analysis results</p><p style="font-size: 12px; color: #9ca3af; margin-top: 8px;">' + error.message + '</p></div>';
    });
}

// Toast notification functions
function showToast(message, type = 'success') {
    // Remove existing toasts with animation
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
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

// Set active tile
function setActiveTile(element) {
    // Remove active class from all tiles
    document.querySelectorAll('.analysis-tile').forEach(tile => {
        tile.classList.remove('active');
    });
    // Add active class to clicked tile
    if (element) {
        element.classList.add('active');
    }
}

// Context menu functions
function toggleContextMenu(event, analysisId, fileName, isCompleted) {
    event.preventDefault();
    event.stopPropagation();
    
    // Close all other context menus
    closeContextMenu();
    
    // Show the clicked context menu
    const menu = document.getElementById('contextMenu' + analysisId);
    if (menu) {
        menu.classList.add('show');
        
        // Position the menu so bottom left aligns with the icon
        const iconRect = event.target.getBoundingClientRect();
        const menuRect = menu.getBoundingClientRect();
        const wrapper = event.target.closest('.analysis-tile-wrapper');
        const wrapperRect = wrapper.getBoundingClientRect();
        
        // Calculate the icon's left position relative to the wrapper
        const iconLeftRelative = iconRect.left - wrapperRect.left;
        
        // Position menu's left edge to align with icon's left edge
        menu.style.left = iconLeftRelative + 'px';
        menu.style.right = 'auto';
        
        // Check if menu would go off screen to the right, if so align to right edge
        if (iconLeftRelative + menuRect.width > wrapperRect.width) {
            menu.style.right = '8px';
            menu.style.left = 'auto';
        }
        
        // Check if menu would go off screen to the top, if so show below instead
        if (iconRect.top - menuRect.height < 0) {
            menu.style.bottom = 'auto';
            menu.style.top = 'calc(100% + 4px)';
        } else {
            menu.style.bottom = 'calc(100% + 4px)';
            menu.style.top = 'auto';
        }
    }
    
    // Close menu when clicking outside
    setTimeout(() => {
        document.addEventListener('click', closeContextMenu, { once: true });
    }, 0);
}

function closeContextMenu() {
    document.querySelectorAll('.analysis-tile-context-menu').forEach(menu => {
        menu.classList.remove('show');
    });
}
</script>
@endsection
