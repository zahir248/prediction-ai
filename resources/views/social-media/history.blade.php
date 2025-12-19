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
            @if($stats['total'] > 0)
                <div style="margin-bottom: 32px;" class="social-stats-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; cursor: pointer;" onclick="toggleSection('socialStatsContent', 'socialStatsToggle')">
                        <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0;">History Overview</h2>
                        <i id="socialStatsToggle" class="bi bi-chevron-down" style="font-size: 18px; color: #64748b; transition: transform 0.3s ease;"></i>
                    </div>
                    <div id="socialStatsContent" class="collapsible-content" style="overflow: hidden; transition: max-height 0.3s ease, opacity 0.3s ease;">
                        <div class="social-stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                            <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div class="social-stat-number" style="font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">{{ $stats['total'] ?? 0 }}</div>
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
                </div>
            @endif

            <!-- Search and Filter Section -->
            <div style="margin-bottom: 24px;">
                <div style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; cursor: pointer; border-bottom: 1px solid #e2e8f0;" onclick="toggleSection('socialFilterContent', 'socialFilterToggle')">
                        <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0;">Search & Filter</h2>
                        <i id="socialFilterToggle" class="bi bi-chevron-down" style="font-size: 18px; color: #64748b; transition: transform 0.3s ease;"></i>
                    </div>
                    <div id="socialFilterContent" class="collapsible-content" style="overflow: hidden; transition: max-height 0.3s ease, opacity 0.3s ease;">
                        <form method="GET" action="{{ route('social-media.history') }}" id="socialFilterForm" style="padding: 20px;">
                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 12px; align-items: end;" id="socialFilterGrid">
                                <!-- Search Input -->
                                <div>
                                    <label for="search" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 13px;">Search</label>
                                    <div style="position: relative;">
                                        <input type="text" 
                                               id="search" 
                                               name="search" 
                                               value="{{ request('search') }}"
                                               placeholder="Search by username..."
                                               style="width: 100%; padding: 10px 16px 10px 40px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: white;">
                                        <i class="bi bi-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px;"></i>
                                    </div>
                                </div>
                                
                                <!-- Status Filter -->
                                <div>
                                    <label for="status" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 13px;">Status</label>
                                    <select id="status" 
                                            name="status" 
                                            style="width: 100%; padding: 10px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: white; cursor: pointer;">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                                
                                <!-- Date From -->
                                <div>
                                    <label for="date_from" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 13px;">From Date</label>
                                    <input type="date" 
                                           id="date_from" 
                                           name="date_from" 
                                           value="{{ request('date_from') }}"
                                           style="width: 100%; padding: 10px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: white;">
                                </div>
                                
                                <!-- Date To -->
                                <div>
                                    <label for="date_to" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 13px;">To Date</label>
                                    <input type="date" 
                                           id="date_to" 
                                           name="date_to" 
                                           value="{{ request('date_to') }}"
                                           style="width: 100%; padding: 10px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: white;">
                                </div>
                                
                                <!-- Action Buttons -->
                                <div style="display: flex; gap: 8px; flex-direction: row; align-items: center;">
                                    <a href="{{ route('social-media.history') }}" 
                                       title="Clear Filters"
                                       onclick="sessionStorage.setItem('socialMediaHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);"
                                       style="padding: 10px; width: 40px; height: 40px; background: #ef4444; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; text-decoration: none; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- Analyses List Section -->
                <div style="margin-bottom: 32px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap; gap: 12px;">
                        <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0;">All Analyses</h2>
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="color: #64748b; font-size: 13px;">Showing {{ $analyses->total() }} result(s)</span>
                                <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                    <i class="bi bi-funnel-fill" style="margin-right: 4px;"></i>Filtered
                                </span>
                            </div>
                        @endif
                    </div>
                    
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
                                @forelse($analyses as $analysis)
                                <tr style="border-bottom: 1px solid #e2e8f0; transition: all 0.3s ease;" data-analysis-id="{{ $analysis->id }}">
                                    <td style="padding: 16px;">
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 15px; margin-bottom: 6px;">{{ $analysis->username }}</div>
                                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
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
                                @empty
                                <tr>
                                    <td colspan="5" style="padding: 60px 24px; text-align: center;">
                                        <div style="color: #64748b; margin-bottom: 12px; font-size: 18px;">
                                            <i class="bi bi-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                                        </div>
                                        <h4 style="color: #64748b; margin-bottom: 12px; font-size: 18px; font-weight: 600;">No analyses found</h4>
                                        <p style="color: #9ca3af; margin-bottom: 24px; line-height: 1.6; font-size: 14px;">
                                            @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                                Try adjusting your search or filter criteria.
                                            @else
                                                Start analyzing social media profiles to see your history here.
                                            @endif
                                        </p>
                                        @if(!request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        <a href="{{ route('social-media.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                                            <i class="bi bi-plus-lg" style="font-size: 16px;"></i>
                                            Create New Analysis
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-only" style="display: none;">
                        @forelse($analyses as $analysis)
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px;" data-analysis-id="{{ $analysis->id }}">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 16px; margin-bottom: 4px;">{{ $analysis->username }}</div>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
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
                        @empty
                        <div style="background: #f8fafc; border-radius: 12px; padding: 40px 24px; border: 1px solid #e2e8f0; text-align: center;">
                            <div style="color: #64748b; margin-bottom: 16px;">
                                <i class="bi bi-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                            </div>
                            <h4 style="color: #64748b; margin-bottom: 12px; font-size: 18px; font-weight: 600;">No analyses found</h4>
                            <p style="color: #9ca3af; margin-bottom: 24px; line-height: 1.6; font-size: 14px;">
                                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                    Try adjusting your search or filter criteria.
                                @else
                                    Start analyzing social media profiles to see your history here.
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('social-media.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                                <i class="bi bi-plus-lg" style="font-size: 16px;"></i>
                                Create New Analysis
                            </a>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>
            
            @if($analyses->count() > 0)
                <div style="padding-top: 24px; border-top: 2px solid #e2e8f0; margin-top: 32px;">
                    <!-- Clean Pagination -->
                    @if($analyses->hasPages())
                        <div class="pagination-container" style="display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 16px;">
                            <div class="pagination-nav" style="display: flex; align-items: center;">
                                @if ($analyses->onFirstPage())
                                    <span style="color: #9ca3af; margin-right: 12px;">« Previous</span>
                                @else
                                    <a href="{{ $analyses->previousPageUrl() }}" class="pagination-link" onclick="sessionStorage.setItem('socialMediaHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);" style="color: #64748b; text-decoration: none; margin-right: 12px; transition: color 0.3s ease;">« Previous</a>
                                @endif

                                @if ($analyses->hasMorePages())
                                    <a href="{{ $analyses->nextPageUrl() }}" class="pagination-link" onclick="sessionStorage.setItem('socialMediaHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);" style="color: #64748b; text-decoration: none; transition: color 0.3s ease;">Next »</a>
                                @else
                                    <span style="color: #9ca3af;">Next »</span>
                                @endif
                            </div>
                            
                            <div class="pagination-info" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                <div class="pagination-text" style="color: #64748b; font-size: 14px;">
                                    Showing {{ $analyses->firstItem() }} to {{ $analyses->lastItem() }} of {{ $analyses->total() }} results
                                </div>
                                
                                <div class="pagination-numbers" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    @if (!$analyses->onFirstPage())
                                        <a href="{{ $analyses->previousPageUrl() }}" class="pagination-arrow" onclick="sessionStorage.setItem('socialMediaHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 16px;">‹</span>
                                        </a>
                                    @endif
                                    
                                    <div class="pagination-pages" style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        @foreach ($analyses->getUrlRange(1, $analyses->lastPage()) as $page => $url)
                                            @if ($page == $analyses->currentPage())
                                                <span class="pagination-current" style="background: #667eea; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}" class="pagination-page" onclick="sessionStorage.setItem('socialMediaHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);" style="color: #64748b; text-decoration: none; padding: 8px 12px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">{{ $page }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    @if ($analyses->hasMorePages())
                                        <a href="{{ $analyses->nextPageUrl() }}" class="pagination-arrow" onclick="sessionStorage.setItem('socialMediaHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;">
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
let socialSearchTimeout = null;

// Collapsible section functionality
function toggleSection(contentId, toggleId) {
    const content = document.getElementById(contentId);
    const toggle = document.getElementById(toggleId);
    
    if (!content || !toggle) return;
    
    // Check if currently expanded by checking computed style or class
    const isExpanded = content.style.maxHeight && content.style.maxHeight !== '0px' && content.style.maxHeight !== '';
    const contentHeight = content.scrollHeight;
    
    if (isExpanded) {
        // Collapse
        content.style.maxHeight = '0px';
        content.style.opacity = '0';
        toggle.style.transform = 'rotate(0deg)';
        toggle.classList.remove('bi-chevron-up');
        toggle.classList.add('bi-chevron-down');
        localStorage.setItem(contentId + '_expanded', 'false');
    } else {
        // Expand
        content.style.maxHeight = contentHeight + 'px';
        content.style.opacity = '1';
        toggle.style.transform = 'rotate(180deg)';
        toggle.classList.remove('bi-chevron-down');
        toggle.classList.add('bi-chevron-up');
        localStorage.setItem(contentId + '_expanded', 'true');
    }
}

// Initialize collapsible sections on page load
function initializeCollapsibleSections() {
    const sections = [
        { contentId: 'socialStatsContent', toggleId: 'socialStatsToggle', defaultExpanded: true },
        { contentId: 'socialFilterContent', toggleId: 'socialFilterToggle', defaultExpanded: true }
    ];
    
    sections.forEach(function(section) {
        const content = document.getElementById(section.contentId);
        const toggle = document.getElementById(section.toggleId);
        
        if (!content || !toggle) return;
        
        // Check localStorage for saved state
        const savedState = localStorage.getItem(section.contentId + '_expanded');
        const isExpanded = savedState !== null ? savedState === 'true' : section.defaultExpanded;
        
        // Set initial state
        if (isExpanded) {
            // Expanded state
            const height = content.scrollHeight;
            content.style.maxHeight = height + 'px';
            content.style.opacity = '1';
            toggle.style.transform = 'rotate(180deg)';
            toggle.classList.remove('bi-chevron-down');
            toggle.classList.add('bi-chevron-up');
        } else {
            // Collapsed state
            content.style.maxHeight = '0px';
            content.style.opacity = '0';
            toggle.style.transform = 'rotate(0deg)';
            toggle.classList.remove('bi-chevron-up');
            toggle.classList.add('bi-chevron-down');
        }
        
        // Force a reflow to ensure styles are applied
        void content.offsetHeight;
    });
}

// Real-time filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize collapsible sections
    initializeCollapsibleSections();
    
    // Recalculate heights on window resize
    window.addEventListener('resize', function() {
        const sections = ['socialStatsContent', 'socialFilterContent'];
        sections.forEach(function(contentId) {
            const content = document.getElementById(contentId);
            if (content && content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });
    
    // Restore scroll position if it was saved
    const savedScrollPosition = sessionStorage.getItem('socialMediaHistoryScrollPosition');
    if (savedScrollPosition) {
        // Small delay to ensure page is fully rendered
        setTimeout(function() {
            window.scrollTo(0, parseInt(savedScrollPosition));
            sessionStorage.removeItem('socialMediaHistoryScrollPosition');
        }, 100);
    }
    
    const filterForm = document.getElementById('socialFilterForm');
    const searchInput = document.getElementById('search');
    const statusSelect = document.getElementById('status');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    
    // Function to save scroll position before form submission
    function saveScrollPosition() {
        sessionStorage.setItem('socialMediaHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);
    }
    
    // Add loading state on form submit
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Save scroll position before submitting
            saveScrollPosition();
            
            // Add a subtle loading indicator
            const filterGrid = document.getElementById('socialFilterGrid');
            if (filterGrid) {
                filterGrid.style.opacity = '0.7';
                filterGrid.style.pointerEvents = 'none';
            }
        });
        
        // Debounced search function (wait 500ms after user stops typing)
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(socialSearchTimeout);
                socialSearchTimeout = setTimeout(function() {
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
    }
});

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
    
    /* Collapsible content styles */
    .collapsible-content {
        max-height: 0;
        opacity: 0;
        transition: max-height 0.3s ease, opacity 0.3s ease;
    }
    
    .collapsible-content[style*="max-height"] {
        transition: max-height 0.3s ease, opacity 0.3s ease;
    }
    
    /* Filter Form Responsive Styles */
    #socialFilterForm {
        display: block;
    }
    
    #socialFilterForm > div {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }
    
    @media (max-width: 1024px) {
        #socialFilterForm > div {
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
        }
        
        #socialFilterForm > div > div:first-child {
            grid-column: span 3;
        }
        
        #socialFilterForm > div > div:last-child {
            grid-column: span 3;
            flex-direction: row !important;
            justify-content: flex-end;
        }
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
        
        /* Filter Form Mobile */
        #socialFilterForm {
            padding: 16px !important;
        }
        
        #socialFilterForm > div {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }
        
        #socialFilterForm > div > div:first-child {
            grid-column: span 1 !important;
        }
        
        #socialFilterForm > div > div:last-child {
            grid-column: span 1 !important;
            flex-direction: row !important;
            justify-content: flex-end;
        }
        
        #socialFilterForm label {
            font-size: 12px !important;
        }
        
        #socialFilterForm input,
        #socialFilterForm select {
            font-size: 16px !important; /* Prevent zoom on iOS */
            padding: 12px 16px !important;
        }
        
        #socialFilterForm a {
            width: 44px !important;
            height: 44px !important;
            padding: 10px !important;
            min-height: 44px !important;
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
        .pagination-link:hover,
        .pagination-arrow:hover,
        .pagination-page:hover {
            background: #f1f5f9 !important;
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

