@extends('layouts.app')

@section('content')
<div class="history-page-container" style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div class="main-card" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Prediction History</h1>
                        <p style="color: #64748b; font-size: 14px; margin: 0;">Complete history of all your AI prediction analyses</p>
                    </div>
                    <div>
                        <a href="{{ route('predictions.create') }}" class="new-prediction-btn" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                            New Prediction
                        </a>
                    </div>
                </div>
            </div>

            <!-- History Stats Overview -->
            @if($predictions->total() > 0)
                <div style="margin-bottom: 32px;" class="stats-container">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">History Overview</h2>
                    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                        <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">{{ $stats['total'] ?? $predictions->total() }}</div>
                            <div class="stat-label" style="font-size: 13px; color: #64748b;">Total</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                            <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #166534; margin-bottom: 4px;">{{ $stats['completed'] ?? 0 }}</div>
                            <div class="stat-label" style="font-size: 13px; color: #64748b;">Completed</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                            <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #92400e; margin-bottom: 4px;">{{ $stats['processing'] ?? 0 }}</div>
                            <div class="stat-label" style="font-size: 13px; color: #64748b;">Processing</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                            <div class="stat-number" style="font-size: 28px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">{{ $stats['failed'] ?? 0 }}</div>
                            <div class="stat-label" style="font-size: 13px; color: #64748b;">Failed</div>
                        </div>
                    </div>
                </div>

                <!-- Predictions List Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">All Predictions</h2>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden-mobile" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Topic & Details
                                    </th>
                                    <th style="padding: 16px; text-align: center; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Sources
                                    </th>
                                    <th style="padding: 16px; text-align: center; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Status
                                    </th>
                                    <th style="padding: 16px; text-align: center; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Date
                                    </th>
                                    <th style="padding: 16px; text-align: center; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($predictions as $prediction)
                                <tr style="border-bottom: 1px solid #e2e8f0; transition: all 0.3s ease;">
                                    <td style="padding: 16px;">
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 6px; font-size: 15px;">{{ Str::limit($prediction->topic, 60) }}</div>
                                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                <span style="background: #f1f5f9; color: #374151; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                                                    #{{ $prediction->id }}
                                                </span>
                                                <span style="color: #64748b; font-size: 12px;">
                                                    {{ $prediction->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center; min-height: 40px; justify-content: center;">
                                            @if($prediction->source_urls && count($prediction->source_urls) > 0)
                                                <span style="color: #64748b; font-size: 12px; font-weight: 600;">{{ count($prediction->source_urls) }}</span>
                                                @foreach($prediction->source_urls as $index => $sourceUrl)
                                                <a href="{{ $sourceUrl }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   style="display: inline-block; padding: 4px 8px; background: #fef3c7; color: #92400e; text-decoration: none; border-radius: 6px; font-size: 10px; font-weight: 500; transition: all 0.3s ease;"
                                                   title="{{ $sourceUrl }}">
                                                    S{{ $index + 1 }}
                                                </a>
                                                @endforeach
                                            @else
                                                <span style="color: #9ca3af; font-size: 12px;">0</span>
                                                <span style="color: #9ca3af; font-size: 10px;">No sources</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        @if($prediction->status === 'completed')
                                            <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                Completed
                                            </span>
                                        @elseif($prediction->status === 'processing')
                                            <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                Processing
                                            </span>
                                        @elseif($prediction->status === 'failed')
                                            <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                Failed
                                            </span>
                                        @else
                                            <span style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                {{ ucfirst($prediction->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $prediction->created_at->format('M d, Y') }}</div>
                                        <div style="color: #64748b; font-size: 12px;">{{ $prediction->created_at->format('H:i') }}</div>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: nowrap; min-height: 36px; align-items: center; white-space: nowrap;">
                                            <a href="{{ route('predictions.show', $prediction) }}" 
                                               style="padding: 8px 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 12px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3); white-space: nowrap;">
                                                View
                                            </a>
                                            @if($prediction->status === 'completed')
                                                <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                                        style="padding: 8px 12px; background: transparent; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 500; font-size: 12px; transition: all 0.3s ease; cursor: pointer; white-space: nowrap;">
                                                    Export
                                                </button>
                                            @else
                                                <span style="padding: 8px 12px; background: transparent; color: #9ca3af; border: 1px solid #e5e7eb; border-radius: 6px; font-weight: 500; font-size: 12px; opacity: 0.5; white-space: nowrap;">
                                                    Export
                                                </span>
                                            @endif
                                            <button onclick="confirmDelete({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                                    style="padding: 8px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 12px; transition: all 0.3s ease; cursor: pointer; white-space: nowrap;">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-only" style="display: none; flex-direction: column; gap: 16px;">
                        @foreach($predictions as $prediction)
                        <div style="background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                            <!-- Card Header -->
                            <div style="margin-bottom: 16px;">
                                <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px; font-size: 15px; line-height: 1.4;">{{ Str::limit($prediction->topic, 80) }}</div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="background: #f1f5f9; color: #374151; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                                        #{{ $prediction->id }}
                                    </span>
                                    <span style="color: #64748b; font-size: 12px;">
                                        {{ $prediction->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Content - Grid Layout -->
                            <div class="mobile-card-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                <!-- Sources -->
                                <div style="text-align: center;">
                                    <div class="mobile-label" style="font-size: 11px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Sources</div>
                                    @if($prediction->source_urls && count($prediction->source_urls) > 0)
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                            <span style="color: #64748b; font-size: 12px; font-weight: 600;">{{ count($prediction->source_urls) }}</span>
                                            @foreach($prediction->source_urls as $index => $sourceUrl)
                                            <a href="{{ $sourceUrl }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="source-link"
                                               style="display: inline-block; padding: 4px 8px; background: #fef3c7; color: #92400e; text-decoration: none; border-radius: 6px; font-size: 10px; font-weight: 500;">
                                                S{{ $index + 1 }}
                                            </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color: #9ca3af; font-size: 12px;">0</span>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div style="text-align: center;">
                                    <div class="mobile-label" style="font-size: 11px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
                                    @if($prediction->status === 'completed')
                                        <span class="status-badge" style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            Completed
                                        </span>
                                    @elseif($prediction->status === 'processing')
                                        <span class="status-badge" style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            Processing
                                        </span>
                                    @elseif($prediction->status === 'failed')
                                        <span class="status-badge" style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            Failed
                                        </span>
                                    @else
                                        <span class="status-badge" style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            {{ ucfirst($prediction->status) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Date -->
                                <div style="text-align: center;">
                                    <div class="mobile-label" style="font-size: 11px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                                    <div class="mobile-date-main" style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $prediction->created_at->format('M d, Y') }}</div>
                                    <div class="mobile-date-time" style="color: #64748b; font-size: 12px;">{{ $prediction->created_at->format('H:i') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mobile-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('predictions.show', $prediction) }}" 
                                   class="mobile-action-btn"
                                   style="flex: 1; min-width: 100px; display: inline-block; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; text-align: center; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                    View
                                </a>
                                @if($prediction->status === 'completed')
                                    <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                            class="mobile-action-btn"
                                            style="flex: 1; min-width: 100px; display: inline-block; padding: 10px 16px; background: transparent; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; cursor: pointer; text-align: center;">
                                        Export
                                    </button>
                                @else
                                    <span class="mobile-action-btn" style="flex: 1; min-width: 100px; display: inline-block; padding: 10px 16px; background: transparent; color: #9ca3af; border: 1px solid #e5e7eb; border-radius: 6px; font-weight: 500; font-size: 13px; opacity: 0.5; text-align: center;">
                                        Export
                                    </span>
                                @endif
                                <button onclick="confirmDelete({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                        class="mobile-action-btn"
                                        style="flex: 1; min-width: 100px; display: inline-block; padding: 10px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; cursor: pointer; text-align: center;">
                                    Delete
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 60px 24px;">
                        <h4 style="color: #64748b; margin-bottom: 12px; font-size: 18px;">No prediction history yet</h4>
                        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6; font-size: 14px;">Create your first analysis to start building your prediction history!</p>
                        <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                            Create First Analysis
                        </a>
                    </div>
                @endif
            </div>
            
            @if($predictions->count() > 0)
                <div style="padding-top: 24px; border-top: 2px solid #e2e8f0; margin-top: 32px;">
                    <!-- Clean Pagination -->
                    @if($predictions->hasPages())
                        <div class="pagination-container" style="display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 16px;">
                            <div class="pagination-nav" style="display: flex; align-items: center;">
                                @if ($predictions->onFirstPage())
                                    <span style="color: #9ca3af; margin-right: 12px;">« Previous</span>
                                @else
                                    <a href="{{ $predictions->previousPageUrl() }}" class="pagination-link" style="color: #64748b; text-decoration: none; margin-right: 12px; transition: color 0.3s ease;">« Previous</a>
                                @endif

                                @if ($predictions->hasMorePages())
                                    <a href="{{ $predictions->nextPageUrl() }}" class="pagination-link" style="color: #64748b; text-decoration: none; transition: color 0.3s ease;">Next »</a>
                                @else
                                    <span style="color: #9ca3af;">Next »</span>
                                @endif
                            </div>
                            
                            <div class="pagination-info" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                <div class="pagination-text" style="color: #64748b; font-size: 14px;">
                                    Showing {{ $predictions->firstItem() }} to {{ $predictions->lastItem() }} of {{ $predictions->total() }} results
                                </div>
                                
                                <div class="pagination-numbers" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    @if (!$predictions->onFirstPage())
                                        <a href="{{ $predictions->previousPageUrl() }}" class="pagination-arrow" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 16px;">‹</span>
                                        </a>
                                    @endif
                                    
                                    <div class="pagination-pages" style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        @foreach ($predictions->getUrlRange(1, $predictions->lastPage()) as $page => $url)
                                            @if ($page == $predictions->currentPage())
                                                <span class="pagination-current" style="background: #667eea; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}" class="pagination-page" style="color: #64748b; text-decoration: none; padding: 8px 12px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">{{ $page }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    @if ($predictions->hasMorePages())
                                        <a href="{{ $predictions->nextPageUrl() }}" class="pagination-arrow" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 16px;">›</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
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
        <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">The report will include all analysis details and AI insights.</p>
        
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
    if (!currentDeleteId) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ url("/predictions") }}/' + currentDeleteId;
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = '{{ csrf_token() }}';
    
    form.appendChild(methodInput);
    form.appendChild(tokenInput);
    document.body.appendChild(form);
    form.submit();
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
    }
});

// Export modal functions
let currentExportId = null;

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
    if (!currentExportId) return;
    
    // Redirect to the export route
    window.location.href = '{{ url("/predictions") }}/' + currentExportId + '/export';
}

// Set up the confirm export button
document.getElementById('confirmExportBtn').onclick = exportPrediction;

// Close export modal when clicking outside
document.getElementById('exportModal').onclick = function(e) {
    if (e.target === this) {
        closeExportModal();
    }
};
</script>

<style>
    /* Hide/show classes for responsive design */
    .hidden-mobile {
        display: block;
    }
    
    .mobile-only {
        display: none;
    }
    
    /* Responsive Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    
    @media (max-width: 768px) {
        .hidden-mobile {
            display: none !important;
        }
        
        .mobile-only {
            display: block !important;
        }
        
        /* Stats grid: 2x2 on tablet/mobile */
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        
        .stat-number {
            font-size: 24px !important;
        }
        
        .stat-label {
            font-size: 12px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Stats grid: stacked on very small screens */
        .stats-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        .stat-number {
            font-size: 22px !important;
        }
    }
    
    /* Hover effects for table rows */
    tbody tr:hover {
        background: #f8fafc !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    /* Hover effects for buttons and links */
    a:hover, button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2) !important;
    }
    
    /* Smooth transitions */
    * {
        transition: all 0.3s ease;
    }
    
    /* Pagination hover effects */
    .pagination a:hover {
        background: #f1f5f9 !important;
    }
    
    /* Responsive design */
    @media (max-width: 1024px) {
        div[style*="padding: 32px 16px"] {
            padding: 24px 12px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 32px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 28px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 20px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 20px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 24px !important;
        }
    }
    
    @media (max-width: 768px) {
        /* Page container padding */
        .history-page-container {
            padding: 16px 8px !important;
        }
        
        /* Main card padding */
        .main-card {
            padding: 20px 16px !important;
            border-radius: 12px !important;
        }
        
        /* Header section */
        h1 {
            font-size: 20px !important;
        }
        
        /* New prediction button */
        .new-prediction-btn {
            width: 100% !important;
            padding: 14px 20px !important;
            font-size: 14px !important;
            text-align: center !important;
        }
        
        /* Stats container */
        .stats-container {
            margin-bottom: 24px !important;
        }
        
        .stats-container h2 {
            font-size: 14px !important;
            margin-bottom: 16px !important;
        }
        
        /* Mobile card improvements */
        .mobile-only > div {
            padding: 14px !important;
            border-radius: 10px !important;
        }
        
        /* Keep Sources, Status, Date in one row */
        .mobile-card-grid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
            margin-bottom: 12px !important;
        }
        
        .mobile-card-grid > div {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 8px 6px !important;
            background: white !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            text-align: center !important;
        }
        
        .mobile-label {
            margin-bottom: 4px !important;
            margin-right: 0 !important;
            font-size: 10px !important;
        }
        
        .status-badge {
            padding: 5px 8px !important;
            font-size: 10px !important;
        }
        
        .mobile-date-main {
            font-size: 11px !important;
        }
        
        .mobile-date-time {
            font-size: 10px !important;
        }
        
        /* Mobile actions - keep in one row */
        .mobile-actions {
            flex-direction: row !important;
            gap: 8px !important;
            flex-wrap: nowrap !important;
        }
        
        .mobile-action-btn {
            flex: 1 !important;
            min-width: 0 !important;
            padding: 12px 8px !important;
            font-size: 12px !important;
            min-height: 44px !important;
        }
        
        /* Pagination improvements */
        .pagination-container {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 16px !important;
            padding-top: 20px !important;
        }
        
        .pagination-info {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        
        .pagination-text {
            text-align: center !important;
            font-size: 12px !important;
        }
        
        .pagination-numbers {
            justify-content: center !important;
            flex-wrap: wrap !important;
        }
        
        .pagination-pages {
            flex-wrap: wrap !important;
            justify-content: center !important;
        }
        
        /* Modal improvements */
        .modal-overlay {
            padding: 16px !important;
            align-items: flex-start !important;
            padding-top: 20vh !important;
        }
        
        .modal-content {
            padding: 24px 20px !important;
            max-width: 100% !important;
        }
        
        .modal-content h3 {
            font-size: 18px !important;
        }
        
        .modal-content p {
            font-size: 14px !important;
        }
        
        .modal-content button {
            padding: 12px 20px !important;
            font-size: 14px !important;
            min-height: 44px !important;
        }
        
        /* Make table responsive */
        div[style*="overflow-x: auto"] {
            margin: 0 -8px !important;
        }
        
        table {
            min-width: 600px !important;
        }
        
        /* Adjust table padding for mobile */
        th[style*="padding: 20px 24px"],
        td[style*="padding: 20px 24px"] {
            padding: 16px 12px !important;
        }
        
        /* Adjust emoji sizes for mobile */
        span[style*="font-size: 64px"] {
            font-size: 48px !important;
        }
        
        span[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Page container padding */
        .history-page-container {
            padding: 12px 4px !important;
        }
        
        /* Main card padding */
        .main-card {
            padding: 16px 12px !important;
            border-radius: 10px !important;
        }
        
        /* Header section */
        h1 {
            font-size: 18px !important;
            margin-bottom: 6px !important;
        }
        
        /* Stats container */
        .stats-container {
            margin-bottom: 20px !important;
        }
        
        .stats-container h2 {
            font-size: 13px !important;
            margin-bottom: 12px !important;
        }
        
        /* Mobile card improvements for very small screens */
        .mobile-only > div {
            padding: 12px !important;
            border-radius: 8px !important;
        }
        
        /* Keep Sources, Status, Date in one row on very small screens too */
        .mobile-card-grid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 6px !important;
            margin-bottom: 10px !important;
        }
        
        .mobile-card-grid > div {
            padding: 6px 4px !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
        }
        
        .mobile-label {
            margin-right: 0 !important;
            margin-bottom: 3px !important;
            font-size: 9px !important;
        }
        
        .status-badge {
            padding: 4px 6px !important;
            font-size: 9px !important;
        }
        
        .mobile-date-main {
            font-size: 10px !important;
        }
        
        .mobile-date-time {
            font-size: 9px !important;
        }
        
        /* Mobile actions - keep in one row on very small screens too */
        .mobile-actions {
            gap: 6px !important;
            flex-direction: row !important;
        }
        
        .mobile-action-btn {
            padding: 10px 6px !important;
            font-size: 11px !important;
            min-height: 42px !important;
        }
        
        /* Pagination improvements */
        .pagination-container {
            gap: 12px !important;
            padding-top: 16px !important;
        }
        
        .pagination-text {
            font-size: 11px !important;
        }
        
        .pagination-arrow,
        .pagination-page,
        .pagination-current {
            min-width: 32px !important;
            min-height: 32px !important;
            padding: 6px 8px !important;
            font-size: 12px !important;
        }
        
        /* Modal improvements */
        .modal-overlay {
            padding: 12px !important;
            padding-top: 15vh !important;
        }
        
        .modal-content {
            padding: 20px 16px !important;
        }
        
        .modal-content h3 {
            font-size: 16px !important;
        }
        
        .modal-content p {
            font-size: 13px !important;
        }
        
        .modal-content button {
            padding: 10px 16px !important;
            font-size: 13px !important;
            min-height: 42px !important;
        }
        
        /* Make table scrollable on very small screens */
        table {
            min-width: 400px !important;
        }
        
        /* Adjust table padding for very small screens */
        th[style*="padding: 20px 24px"],
        td[style*="padding: 20px 24px"] {
            padding: 8px 6px !important;
        }
        
        /* Adjust emoji sizes for very small screens */
        span[style*="font-size: 64px"] {
            font-size: 36px !important;
        }
        
        span[style*="font-size: 24px"] {
            font-size: 16px !important;
        }
    }
    
    /* Touch-friendly improvements */
    @media (max-width: 768px) {
        /* Ensure all interactive elements are touch-friendly */
        a, button {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        /* Prevent zoom on iOS when focusing inputs */
        input, textarea, select {
            font-size: 16px !important;
        }
        
        /* Improve table scrolling on mobile */
        div[style*="overflow-x: auto"] {
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: thin !important;
        }
        
        div[style*="overflow-x: auto"]::-webkit-scrollbar {
            height: 6px !important;
        }
        
        div[style*="overflow-x: auto"]::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-radius: 3px !important;
        }
        
        div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 3px !important;
        }
        
        div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }
        
        /* Pagination link hover states */
        .pagination-link:hover,
        .pagination-arrow:hover,
        .pagination-page:hover {
            background: #f1f5f9 !important;
        }
        
        /* Source link improvements */
        .source-link {
            min-height: 28px !important;
            min-width: 28px !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Even smaller touch targets for very small screens */
        .pagination-arrow,
        .pagination-page,
        .pagination-current {
            min-height: 36px !important;
            min-width: 36px !important;
        }
    }
</style>
@endsection
