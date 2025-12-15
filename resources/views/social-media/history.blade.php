@extends('layouts.app')

@section('content')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<div class="social-history-page-container" style="min-height: calc(100vh - 72px); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 24px 16px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Main Card -->
        <div class="social-history-main-card" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
            <!-- Header Section -->
            <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 32px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Social Media Analysis History</h1>
                        <p style="color: #64748b; font-size: 14px; margin: 0;">Complete history of all your social media profile analyses</p>
                    </div>
                    <div>
                        <a href="{{ route('social-media.index') }}" class="new-analysis-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                            <i class="bi bi-plus-lg" style="font-size: 16px;"></i>
                            New Analysis
                        </a>
                    </div>
                </div>
            </div>

            <!-- History Stats Overview -->
            @if($analyses->total() > 0)
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">History Overview</h2>
                    <div class="social-stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                        <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div class="social-stat-number" style="font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">{{ $stats['total'] ?? $analyses->total() }}</div>
                            <div class="social-stat-label" style="font-size: 13px; color: #64748b;">Total</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                            <div class="social-stat-number" style="font-size: 28px; font-weight: 700; color: #166534; margin-bottom: 4px;">{{ $stats['completed'] ?? 0 }}</div>
                            <div class="social-stat-label" style="font-size: 13px; color: #64748b;">Completed</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                            <div class="social-stat-number" style="font-size: 28px; font-weight: 700; color: #92400e; margin-bottom: 4px;">{{ $stats['processing'] ?? 0 }}</div>
                            <div class="social-stat-label" style="font-size: 13px; color: #64748b;">Processing</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                            <div class="social-stat-number" style="font-size: 28px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">{{ $stats['failed'] ?? 0 }}</div>
                            <div class="social-stat-label" style="font-size: 13px; color: #64748b;">Failed</div>
                        </div>
                    </div>
                </div>

                <!-- Analyses List Section -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">All Analyses</h2>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden-mobile" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Username
                                    </th>
                                    <th style="padding: 16px; text-align: center; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                                        Platforms
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
                                @foreach($analyses as $analysis)
                                <tr style="border-bottom: 1px solid #e2e8f0; transition: all 0.3s ease;" data-analysis-id="{{ $analysis->id }}">
                                    <td style="padding: 16px;">
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 15px; margin-bottom: 6px;">{{ $analysis->username }}</div>
                                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                <span style="background: #f1f5f9; color: #374151; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                                                    #{{ $analysis->id }}
                                                </span>
                                                @php
                                                    $analysisType = $analysis->ai_analysis['analysis_type'] ?? 'professional';
                                                @endphp
                                                <span style="padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; 
                                                    @if($analysisType === 'political') 
                                                        background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;
                                                    @else 
                                                        background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;
                                                    @endif">
                                                    {{ ucfirst($analysisType) }}
                                                </span>
                                                <span style="color: #64748b; font-size: 12px;">
                                                    {{ $analysis->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                            @foreach($analysis->found_platforms as $platform)
                                                <span style="padding: 4px 8px; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 10px; font-weight: 500; text-transform: capitalize;">
                                                    {{ $platform }}
                                                </span>
                                            @endforeach
                                            @if(count($analysis->found_platforms) === 0)
                                                <span style="color: #9ca3af; font-size: 12px;">No platforms</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        @if($analysis->status === 'completed')
                                            <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                Completed
                                            </span>
                                        @elseif($analysis->status === 'processing')
                                            <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                Processing
                                            </span>
                                        @elseif($analysis->status === 'failed')
                                            <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                Failed
                                            </span>
                                        @else
                                            <span style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                {{ ucfirst($analysis->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $analysis->created_at->format('M d, Y') }}</div>
                                        <div style="color: #64748b; font-size: 12px;">{{ $analysis->created_at->format('H:i') }}</div>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: nowrap; min-height: 36px; align-items: center; white-space: nowrap;">
                                            @if($analysis->platform_data)
                                                <a href="{{ route('social-media.show', $analysis) }}" 
                                                   title="View"
                                                   style="padding: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                                    <i class="bi bi-eye" style="font-size: 14px;"></i>
                                                </a>
                                            @else
                                                <span title="View (unavailable)"
                                                      style="padding: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: #e5e7eb; color: #9ca3af; border-radius: 6px; opacity: 0.5;">
                                                    <i class="bi bi-eye" style="font-size: 14px;"></i>
                                                </span>
                                            @endif
                                            @if($analysis->status === 'completed')
                                                <button onclick="confirmExport({{ $analysis->id }}, '{{ $analysis->username }}')" 
                                                        title="Export"
                                                        style="padding: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: transparent; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; transition: all 0.3s ease; cursor: pointer;">
                                                    <i class="bi bi-download" style="font-size: 14px;"></i>
                                                </button>
                                            @else
                                                <span title="Export (unavailable)"
                                                      style="padding: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: transparent; color: #9ca3af; border: 1px solid #e5e7eb; border-radius: 6px; opacity: 0.5;">
                                                    <i class="bi bi-download" style="font-size: 14px;"></i>
                                                </span>
                                            @endif
                                            <button onclick="confirmDelete({{ $analysis->id }}, '{{ $analysis->username }}')" 
                                                    title="Delete"
                                                    style="padding: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: #ef4444; color: white; border: none; border-radius: 6px; transition: all 0.3s ease; cursor: pointer;"
                                                    onmouseover="this.style.background='#dc2626';"
                                                    onmouseout="this.style.background='#ef4444';">
                                                <i class="bi bi-trash" style="font-size: 14px;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-only" style="display: none;">
                        @foreach($analyses as $analysis)
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px;" data-analysis-id="{{ $analysis->id }}">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 16px; margin-bottom: 4px;">{{ $analysis->username }}</div>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                                        <span style="color: #64748b; font-size: 12px;">#{{ $analysis->id }}</span>
                                        @php
                                            $analysisType = $analysis->ai_analysis['analysis_type'] ?? 'professional';
                                        @endphp
                                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; 
                                            @if($analysisType === 'political') 
                                                background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;
                                            @else 
                                                background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;
                                            @endif">
                                            {{ ucfirst($analysisType) }}
                                        </span>
                                        <span style="color: #64748b; font-size: 12px;">• {{ $analysis->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @if($analysis->status === 'completed')
                                    <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                        Completed
                                    </span>
                                @elseif($analysis->status === 'processing')
                                    <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                        Processing
                                    </span>
                                @elseif($analysis->status === 'failed')
                                    <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                        Failed
                                    </span>
                                @endif
                            </div>
                            <div style="margin-bottom: 12px;">
                                <div style="color: #64748b; font-size: 12px; margin-bottom: 6px;">Platforms:</div>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    @foreach($analysis->found_platforms as $platform)
                                        <span style="padding: 4px 8px; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 10px; font-weight: 500; text-transform: capitalize;">
                                            {{ $platform }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @if($analysis->platform_data)
                                <div class="social-mobile-actions" style="display: flex; gap: 8px;">
                                    <a href="{{ route('social-media.show', $analysis) }}" 
                                       class="social-mobile-action-btn"
                                       title="View"
                                       style="flex: 1; min-width: 100px; display: flex; align-items: center; justify-content: center; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                        <i class="bi bi-eye" style="font-size: 16px;"></i>
                                    </a>
                                    @if($analysis->status === 'completed')
                                        <button onclick="confirmExport({{ $analysis->id }}, '{{ $analysis->username }}')" 
                                                class="social-mobile-action-btn"
                                                title="Export"
                                                style="flex: 1; min-width: 100px; display: flex; align-items: center; justify-content: center; padding: 10px 16px; background: transparent; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; cursor: pointer;">
                                            <i class="bi bi-download" style="font-size: 16px;"></i>
                                        </button>
                                    @else
                                        <span class="social-mobile-action-btn" 
                                              title="Export (unavailable)"
                                              style="flex: 1; min-width: 100px; display: flex; align-items: center; justify-content: center; padding: 10px 16px; background: transparent; color: #9ca3af; border: 1px solid #e5e7eb; border-radius: 6px; font-weight: 500; font-size: 13px; opacity: 0.5;">
                                            <i class="bi bi-download" style="font-size: 16px;"></i>
                                        </span>
                                    @endif
                                    <button onclick="confirmDelete({{ $analysis->id }}, '{{ $analysis->username }}')" 
                                            class="social-mobile-action-btn"
                                            title="Delete"
                                            style="flex: 1; min-width: 100px; display: flex; align-items: center; justify-content: center; padding: 10px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; cursor: pointer;">
                                        <i class="bi bi-trash" style="font-size: 16px;"></i>
                                    </button>
                                </div>
                            @else
                                <div>
                                    <button onclick="confirmDelete({{ $analysis->id }}, '{{ $analysis->username }}')" 
                                            title="Delete"
                                            style="width: 100%; padding: 10px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px;"
                                            onmouseover="this.style.background='#dc2626';"
                                            onmouseout="this.style.background='#ef4444';">
                                        <i class="bi bi-trash" style="font-size: 16px;"></i>
                                        Delete
                                    </button>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($analyses->hasPages())
                        <div class="social-pagination-container" style="padding-top: 24px; border-top: 2px solid #e2e8f0; margin-top: 32px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 16px;">
                                <div class="social-pagination-nav" style="display: flex; align-items: center;">
                                    @if ($analyses->onFirstPage())
                                        <span style="color: #9ca3af; margin-right: 12px;">« Previous</span>
                                    @else
                                        <a href="{{ $analyses->previousPageUrl() }}" class="social-pagination-link" style="color: #64748b; text-decoration: none; margin-right: 12px; transition: color 0.3s ease;">« Previous</a>
                                    @endif

                                    @if ($analyses->hasMorePages())
                                        <a href="{{ $analyses->nextPageUrl() }}" class="social-pagination-link" style="color: #64748b; text-decoration: none; transition: color 0.3s ease;">Next »</a>
                                    @else
                                        <span style="color: #9ca3af;">Next »</span>
                                    @endif
                                </div>
                                
                                <div class="social-pagination-info" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                    <div class="social-pagination-text" style="color: #64748b; font-size: 14px;">
                                        Showing {{ $analyses->firstItem() }} to {{ $analyses->lastItem() }} of {{ $analyses->total() }} results
                                    </div>
                                    
                                    <div class="social-pagination-numbers" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        @if (!$analyses->onFirstPage())
                                            <a href="{{ $analyses->previousPageUrl() }}" class="social-pagination-arrow" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">
                                                <span style="font-size: 16px;">‹</span>
                                            </a>
                                        @endif
                                        
                                        <div class="social-pagination-pages" style="display: flex; gap: 4px; flex-wrap: wrap;">
                                            @foreach ($analyses->getUrlRange(1, $analyses->lastPage()) as $page => $url)
                                                @if ($page == $analyses->currentPage())
                                                    <span class="social-pagination-current" style="background: #667eea; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">{{ $page }}</span>
                                                @else
                                                    <a href="{{ $url }}" class="social-pagination-page" style="color: #64748b; text-decoration: none; padding: 8px 12px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">{{ $page }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                        
                                        @if ($analyses->hasMorePages())
                                            <a href="{{ $analyses->nextPageUrl() }}" class="social-pagination-arrow" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">
                                                <span style="font-size: 16px;">›</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Empty State -->
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 64px; margin-bottom: 24px;">📊</div>
                    <h2 style="font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">No Analyses Yet</h2>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
                        Start analyzing social media profiles to see your history here.
                    </p>
                    <a href="{{ route('social-media.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                        <i class="bi bi-plus-lg" style="font-size: 16px;"></i>
                        Create New Analysis
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Export Confirmation Modal -->
<div id="exportModal" class="social-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="social-modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #10b981;">📄</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Export PDF Report</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this social media analysis as a PDF report?</p>
        <p id="exportUsername" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="social-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="social-modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 48px; color: #ef4444;">⚠️</span>
        </div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Confirm Deletion</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you sure you want to delete this social media analysis?</p>
        <p id="deleteUsername" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
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

function confirmDelete(analysisId, username) {
    currentDeleteId = analysisId;
    document.getElementById('deleteUsername').textContent = username;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentDeleteId = null;
}

async function deleteAnalysis() {
    if (!currentDeleteId) return;
    
    const btn = document.getElementById('confirmDeleteBtn');
    const originalText = btn.textContent;
    
    // Disable button and show loading
    btn.disabled = true;
    btn.textContent = 'Deleting...';
    btn.style.opacity = '0.6';
    btn.style.cursor = 'not-allowed';

    try {
        const response = await fetch(`{{ url('social-media') }}/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            }
        });

        const result = await response.json();

        if (result.success) {
            // Close modal
            closeDeleteModal();
            
            // Remove the row/card from the DOM
            const row = document.querySelector(`tr[data-analysis-id="${currentDeleteId}"]`);
            const card = document.querySelector(`div[data-analysis-id="${currentDeleteId}"]`);
            const elementToRemove = row || card;
            
            if (elementToRemove) {
                elementToRemove.style.transition = 'opacity 0.3s ease';
                elementToRemove.style.opacity = '0';
                setTimeout(() => {
                    elementToRemove.remove();
                    // Reload page to refresh stats
                    window.location.reload();
                }, 300);
            } else {
                // Fallback: reload page
                window.location.reload();
            }
        } else {
            alert('Delete failed: ' + (result.error || 'Unknown error'));
            // Re-enable button
            btn.disabled = false;
            btn.textContent = originalText;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('An error occurred while deleting. Please try again.');
        // Re-enable button
        btn.disabled = false;
        btn.textContent = originalText;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    }
}

// Set up the confirm delete button
document.getElementById('confirmDeleteBtn').onclick = deleteAnalysis;

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

function confirmExport(analysisId, username) {
    currentExportId = analysisId;
    document.getElementById('exportUsername').textContent = username;
    document.getElementById('exportModal').style.display = 'flex';
}

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
    currentExportId = null;
}

function exportAnalysis() {
    if (!currentExportId) return;
    
    // Redirect to the export route
    window.location.href = '{{ url("/social-media") }}/' + currentExportId + '/export';
}

// Set up the confirm export button
document.getElementById('confirmExportBtn').onclick = exportAnalysis;

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
    
    @media (max-width: 768px) {
        .hidden-mobile {
            display: none !important;
        }
        
        .mobile-only {
            display: block !important;
        }
    }
    
    @media (min-width: 769px) {
        .mobile-only {
            display: none !important;
        }
    }
    
    /* Responsive Stats Grid */
    .social-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    
    @media (max-width: 768px) {
        /* Container and card padding */
        .social-history-page-container {
            padding: 16px 8px !important;
        }
        
        .social-history-main-card {
            padding: 20px 16px !important;
            border-radius: 12px !important;
        }
        
        /* Header section */
        h1 {
            font-size: 20px !important;
        }
        
        p[style*="color: #64748b; font-size: 14px"] {
            font-size: 12px !important;
        }
        
        /* New analysis button */
        .new-analysis-btn {
            width: 100% !important;
            padding: 14px 20px !important;
            font-size: 14px !important;
            text-align: center !important;
        }
        
        /* Stats grid: 2x2 on tablet/mobile */
        .social-stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        
        .social-stat-number {
            font-size: 24px !important;
        }
        
        .social-stat-label {
            font-size: 12px !important;
        }
        
        /* Mobile card improvements */
        .mobile-only > div {
            padding: 14px !important;
            border-radius: 10px !important;
        }
        
        /* Mobile actions - keep in one row */
        .social-mobile-actions {
            flex-direction: row !important;
            gap: 8px !important;
            flex-wrap: nowrap !important;
        }
        
        .social-mobile-action-btn {
            flex: 1 !important;
            min-width: 0 !important;
            padding: 12px 8px !important;
            font-size: 12px !important;
            min-height: 44px !important;
        }
        
        /* Pagination improvements */
        .social-pagination-container {
            padding-top: 20px !important;
        }
        
        .social-pagination-container > div {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 16px !important;
        }
        
        .social-pagination-info {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        
        .social-pagination-text {
            text-align: center !important;
            font-size: 12px !important;
        }
        
        .social-pagination-numbers {
            justify-content: center !important;
            flex-wrap: wrap !important;
        }
        
        .social-pagination-pages {
            flex-wrap: wrap !important;
            justify-content: center !important;
        }
        
        /* Modal improvements */
        .social-modal-overlay {
            padding: 16px !important;
            align-items: flex-start !important;
            padding-top: 20vh !important;
        }
        
        .social-modal-content {
            padding: 24px 20px !important;
            max-width: 100% !important;
        }
        
        .social-modal-content h3 {
            font-size: 18px !important;
        }
        
        .social-modal-content p {
            font-size: 14px !important;
        }
        
        .social-modal-content button {
            padding: 12px 20px !important;
            font-size: 14px !important;
            min-height: 44px !important;
        }
        
        .social-modal-content div[style*="display: flex; gap: 16px"] {
            flex-direction: column !important;
            gap: 10px !important;
        }
        
        .social-modal-content div[style*="display: flex; gap: 16px"] button {
            width: 100% !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Very small screens */
        .social-history-page-container {
            padding: 12px 4px !important;
        }
        
        .social-history-main-card {
            padding: 16px 12px !important;
            border-radius: 10px !important;
        }
        
        h1 {
            font-size: 18px !important;
        }
        
        /* Stats grid: stacked on very small screens */
        .social-stats-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        .social-stat-number {
            font-size: 22px !important;
        }
        
        /* Mobile actions */
        .social-mobile-actions {
            gap: 6px !important;
        }
        
        .social-mobile-action-btn {
            padding: 10px 6px !important;
            font-size: 11px !important;
            min-height: 42px !important;
        }
        
        /* Pagination improvements */
        .social-pagination-text {
            font-size: 11px !important;
        }
        
        .social-pagination-arrow,
        .social-pagination-page,
        .social-pagination-current {
            min-width: 32px !important;
            min-height: 32px !important;
            padding: 6px 8px !important;
            font-size: 12px !important;
        }
        
        /* Modal improvements */
        .social-modal-overlay {
            padding: 12px !important;
            padding-top: 15vh !important;
        }
        
        .social-modal-content {
            padding: 20px 16px !important;
        }
        
        .social-modal-content h3 {
            font-size: 16px !important;
        }
        
        .social-modal-content p {
            font-size: 13px !important;
        }
        
        .social-modal-content button {
            padding: 10px 16px !important;
            font-size: 13px !important;
            min-height: 42px !important;
        }
    }
    
    /* Touch-friendly improvements */
    @media (max-width: 768px) {
        /* Ensure all interactive elements are touch-friendly */
        a, button {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        /* Pagination link hover states */
        .social-pagination-link:hover,
        .social-pagination-arrow:hover,
        .social-pagination-page:hover {
            background: #f1f5f9 !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Even smaller touch targets for very small screens */
        .social-pagination-arrow,
        .social-pagination-page,
        .social-pagination-current {
            min-height: 36px !important;
            min-width: 36px !important;
        }
    }
</style>
@endsection

