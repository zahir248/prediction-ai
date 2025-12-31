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
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
    }
    
    .cursor-sidebar-content {
        flex: 1;
        padding: 16px;
        padding-bottom: 16px;
        overflow-y: auto;
        overflow-x: visible;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    
    .cursor-main-content {
        flex: 1;
        padding: 24px;
        max-width: 100%;
        width: 100%;
        position: relative;
        z-index: 1;
        min-height: 100%;
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
    
    .prediction-card {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }
    
    .prediction-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    .prediction-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    
    .prediction-title {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        flex: 1;
        line-height: 1.4;
    }
    
    .prediction-status {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        margin-left: 12px;
    }
    
    .prediction-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 12px;
        color: #64748b;
        margin-bottom: 12px;
    }
    
    .prediction-actions {
        display: flex;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }
    
    .action-btn {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .action-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .action-btn-secondary {
        background: transparent;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .action-btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .action-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .prediction-tile {
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
    
    .prediction-tile:hover {
        background: #f8fafc;
        border-color: #667eea;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    
    .prediction-tile-title {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        line-height: 1.4;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .prediction-tile-menu {
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
    
    .prediction-tile-wrapper:hover .prediction-tile-menu {
        opacity: 1;
        visibility: visible;
    }
    
    .prediction-tile-menu:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    .prediction-tile-wrapper {
        position: relative;
        margin-bottom: 8px;
    }
    
    .prediction-tile-context-menu {
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
    
    .prediction-tile-context-menu.show {
        display: block;
    }
    
    .prediction-tile-context-menu-item {
        padding: 10px 16px;
        font-size: 13px;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .prediction-tile-context-menu-item:hover {
        background: #f8fafc;
    }
    
    .prediction-tile-context-menu-item.delete {
        color: #ef4444;
    }
    
    .prediction-tile-context-menu-item.delete:hover {
        background: #fef2f2;
    }
    
    .prediction-tile.active {
        background: #eff6ff;
        border-color: #667eea;
    }
    
    /* Loading spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Custom scrollbar for sidebar */
    .cursor-sidebar-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .cursor-sidebar-content::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    .cursor-sidebar-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .cursor-sidebar-content::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
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
        
        .prediction-header {
            flex-direction: column;
            gap: 8px;
        }
        
        .prediction-status {
            margin-left: 0;
            align-self: flex-start;
        }
        
        .prediction-actions {
            flex-wrap: wrap;
        }
        
        .action-btn {
            flex: 1;
            min-width: 100px;
            justify-content: center;
        }
    }
</style>

<div class="cursor-layout">
    <!-- Left Panel: Filters & Stats -->
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-header">
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Prediction History</h2>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Filter and manage your analyses</p>
                    </div>
        
        <div class="cursor-sidebar-content">
            <!-- Search and Filter Section -->
            <div class="cursor-section">
                <div class="cursor-section-title" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;" onclick="toggleFilterSection()">
                    <span>Search & Filter</span>
                    <i class="bi bi-chevron-down" id="filterToggleIcon" style="font-size: 12px; color: #6b7280; transition: transform 0.3s ease;"></i>
                        </div>
                <div id="filterSectionContent" style="overflow: hidden; transition: max-height 0.3s ease;">
                    <form method="GET" action="{{ route('predictions.history') }}" id="filterForm">
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
                                    <option value="completed_with_warnings" {{ request('status') == 'completed_with_warnings' ? 'selected' : '' }}>Completed with Warnings</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                <a href="{{ route('predictions.history') }}" 
                                   onclick="sessionStorage.setItem('predictionsHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);"
                           style="flex: 1; padding: 6px 10px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 4px; font-weight: 500; font-size: 11px; text-decoration: none; text-align: center; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                            <i class="bi bi-x-circle" style="font-size: 11px;"></i>Clear
                                </a>
                        </div>
                            </form>
                    </div>
                </div>

            <!-- Predictions Tiles -->
            <div class="cursor-section">
                <div class="cursor-section-title">All Predictions</div>
                <div style="padding-right: 4px;">
                                @forelse($predictions as $prediction)
                                            <div class="prediction-tile-wrapper" data-prediction-id="{{ $prediction->id }}" style="position: relative;">
                                            <a href="javascript:void(0);" 
                           class="prediction-tile {{ request()->route('prediction') == $prediction->id ? 'active' : '' }}"
                           onclick="event.preventDefault(); loadPredictionResults({{ $prediction->id }}, this);">
                            <p class="prediction-tile-title">{{ $prediction->topic }}</p>
                        </a>
                        <i class="bi bi-three-dots prediction-tile-menu" 
                           onclick="event.stopPropagation(); toggleContextMenu(event, {{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}', {{ $prediction->status === 'completed' ? 'true' : 'false' }})"></i>
                        <div class="prediction-tile-context-menu" id="contextMenu{{ $prediction->id }}">
                                        @if($prediction->status === 'completed')
                            <div class="prediction-tile-context-menu-item" onclick="event.stopPropagation(); closeContextMenu(); confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')">
                                <i class="bi bi-download"></i>
                                <span>Export</span>
                                        </div>
                                            @endif
                            <div class="prediction-tile-context-menu-item delete" onclick="event.stopPropagation(); closeContextMenu(); confirmDelete({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')">
                                <i class="bi bi-trash"></i>
                                <span>Delete</span>
                    </div>
                            </div>
                        </div>
                        @empty
                    <div style="text-align: center; padding: 24px 12px; color: #9ca3af; font-size: 12px;">
                        <i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
                        No predictions found
                        </div>
                        @endforelse
            
                    <!-- Pagination for tiles -->
                    @if($predictions->hasPages())
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                                @if ($predictions->onFirstPage())
                                <span style="color: #9ca3af; font-size: 11px;">« Prev</span>
                                @else
                                <a href="{{ $predictions->previousPageUrl() }}" 
                                   onclick="sessionStorage.setItem('predictionsHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);"
                                   style="color: #64748b; text-decoration: none; font-size: 11px; transition: color 0.2s ease;">« Prev</a>
                                @endif

                            <span style="color: #64748b; font-size: 11px;">Page {{ $predictions->currentPage() }} of {{ $predictions->lastPage() }}</span>

                                @if ($predictions->hasMorePages())
                                <a href="{{ $predictions->nextPageUrl() }}" 
                                   onclick="sessionStorage.setItem('predictionsHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);"
                                   style="color: #64748b; text-decoration: none; font-size: 11px; transition: color 0.2s ease;">Next »</a>
                                @else
                                <span style="color: #9ca3af; font-size: 11px;">Next »</span>
                                @endif
                            </div>
                                </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>

    <!-- Right Panel: Prediction Results -->
    <div class="cursor-main scrollable" id="predictionResultsPanel">
        <div class="cursor-main-content" id="predictionResultsContent" style="display: none;">
            <!-- Content will be loaded here via AJAX -->
                </div>
        <div id="predictionResultsEmpty" style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af; font-size: 14px;">
            <div style="text-align: center;">
                <i class="bi bi-file-earmark-text" style="font-size: 48px; display: block; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>Select a prediction to view analysis results</p>
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
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you sure you want to delete this prediction analysis?</p>
        <p id="deleteTopic" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
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

<!-- Export Confirmation Modal -->
<div id="exportModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
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

<script>
let currentDeleteId = null;
let currentExportId = null;
let searchTimeout = null;

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
    const savedScrollPosition = sessionStorage.getItem('predictionsHistoryScrollPosition');
    if (savedScrollPosition) {
        setTimeout(function() {
            window.scrollTo(0, parseInt(savedScrollPosition));
            sessionStorage.removeItem('predictionsHistoryScrollPosition');
        }, 100);
    }
    
    // Function to save scroll position before form submission
    function saveScrollPosition() {
        sessionStorage.setItem('predictionsHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);
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

function confirmDelete(predictionId, topic) {
    currentDeleteId = predictionId;
    document.getElementById('deleteTopic').textContent = topic;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentDeleteId = null;
}

function deletePrediction() {
    if (!currentDeleteId) {
        showToast('Error: Prediction ID not found', 'error');
        return;
    }
    
    const predictionId = currentDeleteId;
    
    // Close the modal first
    closeDeleteModal();
    
    // Show loading message
    showToast('Deleting prediction...', 'success');
    
    // Make AJAX request to delete
    fetch('{{ url("/predictions") }}/' + predictionId, {
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
                throw new Error(err.message || 'Failed to delete prediction');
            });
        }
        return response.json();
    })
    .then(data => {
        // Show success message
        showToast('Prediction deleted successfully!', 'success');
        
        // Remove the prediction tile from the UI
        const tileWrapper = document.querySelector(`[data-prediction-id="${predictionId}"]`);
        if (tileWrapper) {
            tileWrapper.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            tileWrapper.style.opacity = '0';
            tileWrapper.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                tileWrapper.remove();
                
                // Check if there are no more predictions
                const remainingTiles = document.querySelectorAll('.prediction-tile-wrapper');
                if (remainingTiles.length === 0) {
                    const emptyState = document.querySelector('.cursor-sidebar-content').querySelector('div[style*="text-align: center"]');
                    if (!emptyState || !emptyState.textContent.includes('No predictions found')) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.style.cssText = 'text-align: center; padding: 24px 12px; color: #9ca3af; font-size: 12px;';
                        emptyDiv.innerHTML = '<i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>No predictions found';
                        document.querySelector('.cursor-sidebar-content').appendChild(emptyDiv);
                    }
                }
            }, 300);
        }
        
        // Clear the right panel if this prediction was being displayed
        if (currentDisplayedPredictionId === predictionId) {
            currentDisplayedPredictionId = null;
            const resultsContent = document.getElementById('predictionResultsContent');
            const resultsEmpty = document.getElementById('predictionResultsEmpty');
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
        showToast(error.message || 'Failed to delete prediction. Please try again.', 'error');
    });
}

// Set up the confirm delete button
document.getElementById('confirmDeleteBtn').onclick = deletePrediction;

// Close modal when clicking outside
document.getElementById('deleteModal').onclick = function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
};

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeExportModal();
        closeContextMenu();
    }
});

// Export modal functions
function confirmExport(predictionId, topic) {
    currentExportId = predictionId;
    document.getElementById('exportTopic').textContent = topic;
    document.getElementById('exportModal').style.display = 'flex';
}

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
    currentExportId = null;
}

function exportPrediction() {
    if (!currentExportId) {
        showToast('Error: Prediction ID not found', 'error');
        return;
    }
    
    // Store the ID before closing the modal
    const predictionId = currentExportId;
    
    // Close the modal first
        closeExportModal();
    
    // Show loading message
    showToast('Exporting PDF...', 'success');
    
    // Redirect to the export route
    // The download will start automatically
    // Show success message after a short delay (optimistic)
    setTimeout(() => {
        showToast('PDF exported successfully!', 'success');
    }, 1000);
    
    window.location.href = '{{ url("/predictions") }}/' + predictionId + '/export';
}

// Set up the confirm export button
document.getElementById('confirmExportBtn').onclick = exportPrediction;

// Close export modal when clicking outside
document.getElementById('exportModal').onclick = function(e) {
    if (e.target === this) {
        closeExportModal();
    }
};

// Store currently displayed prediction ID
let currentDisplayedPredictionId = null;

// Load prediction results in right panel
function loadPredictionResults(predictionId, tileElement) {
    // Check if this tile is already active and displaying the same prediction
    if (tileElement && tileElement.classList.contains('active') && currentDisplayedPredictionId === predictionId) {
        // Already displaying this prediction, don't reload
        return;
    }
    
    // Set active tile
    setActiveTile(tileElement);
    
    // Update current displayed prediction ID
    currentDisplayedPredictionId = predictionId;
    
    // Show loading state
    const resultsContent = document.getElementById('predictionResultsContent');
    const resultsEmpty = document.getElementById('predictionResultsEmpty');
    
    resultsEmpty.style.display = 'none';
    resultsContent.style.display = 'flex';
    resultsContent.style.alignItems = 'center';
    resultsContent.style.justifyContent = 'center';
    resultsContent.style.minHeight = '100%';
    resultsContent.innerHTML = '<div style="text-align: center; color: #64748b;"><i class="bi bi-hourglass-split" style="font-size: 48px; display: block; margin-bottom: 16px; animation: spin 1s linear infinite;"></i><p>Loading analysis results...</p></div>';
    
    // Fetch prediction HTML content
    fetch('{{ url("/predictions") }}/' + predictionId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load prediction');
        }
        return response.text();
    })
    .then(html => {
        // Set the HTML content directly
        resultsContent.style.display = 'block';
        resultsContent.style.alignItems = 'flex-start';
        resultsContent.style.justifyContent = 'flex-start';
        resultsContent.style.minHeight = 'auto';
        
        // Process the HTML to remove card styling (matching create.blade.php behavior)
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Remove card-like backgrounds and borders from main content
        const allElements = tempDiv.querySelectorAll('*');
        allElements.forEach(el => {
            if (el.classList.contains('prediction-main-card')) {
                el.style.background = 'transparent';
                el.style.border = 'none';
                el.style.boxShadow = 'none';
                el.style.borderRadius = '0';
                el.style.padding = '0';
            }
        });
        
        resultsContent.innerHTML = tempDiv.innerHTML;
    })
    .catch(error => {
        console.error('Error loading prediction:', error);
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
    document.querySelectorAll('.prediction-tile').forEach(tile => {
        tile.classList.remove('active');
    });
    // Add active class to clicked tile
    if (element) {
        element.classList.add('active');
    }
}

// Context menu functions
function toggleContextMenu(event, predictionId, topic, isCompleted) {
    event.preventDefault();
    event.stopPropagation();
    
    // Close all other context menus
    closeContextMenu();
    
    // Show the clicked context menu
    const menu = document.getElementById('contextMenu' + predictionId);
    if (menu) {
        menu.classList.add('show');
        
        // Position the menu so bottom left aligns with the icon
        const iconRect = event.target.getBoundingClientRect();
        const menuRect = menu.getBoundingClientRect();
        const wrapper = event.target.closest('.prediction-tile-wrapper');
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
    document.querySelectorAll('.prediction-tile-context-menu').forEach(menu => {
        menu.classList.remove('show');
    });
}
</script>
@endsection
