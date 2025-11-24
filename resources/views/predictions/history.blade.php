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
                        <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Prediction History</h1>
                        <p style="color: #64748b; font-size: 14px; margin: 0;">Complete history of all your AI prediction analyses</p>
                    </div>
                    <div>
                        <a href="{{ route('predictions.create') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                            New Prediction
                        </a>
                    </div>
                </div>
            </div>

            <!-- History Stats Overview -->
            @if($predictions->count() > 0)
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">History Overview</h2>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                        <div style="text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">{{ $predictions->total() }}</div>
                            <div style="font-size: 13px; color: #64748b;">Total</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                            <div style="font-size: 28px; font-weight: 700; color: #166534; margin-bottom: 4px;">{{ $predictions->where('status', 'completed')->count() }}</div>
                            <div style="font-size: 13px; color: #64748b;">Completed</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                            <div style="font-size: 28px; font-weight: 700; color: #92400e; margin-bottom: 4px;">{{ $predictions->where('status', 'processing')->count() }}</div>
                            <div style="font-size: 13px; color: #64748b;">Processing</div>
                        </div>
                        <div style="text-align: center; padding: 16px; background: #fef2f2; border-radius: 8px; border: 1px solid #fecaca;">
                            <div style="font-size: 28px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">{{ $predictions->where('status', 'failed')->count() }}</div>
                            <div style="font-size: 13px; color: #64748b;">Failed</div>
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
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                <!-- Sources -->
                                <div style="text-align: center;">
                                    <div style="font-size: 11px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Sources</div>
                                    @if($prediction->source_urls && count($prediction->source_urls) > 0)
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                            <span style="color: #64748b; font-size: 12px; font-weight: 600;">{{ count($prediction->source_urls) }}</span>
                                            @foreach($prediction->source_urls as $index => $sourceUrl)
                                            <a href="{{ $sourceUrl }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
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
                                    <div style="font-size: 11px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
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
                                </div>

                                <!-- Date -->
                                <div style="text-align: center;">
                                    <div style="font-size: 11px; color: #64748b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $prediction->created_at->format('M d, Y') }}</div>
                                    <div style="color: #64748b; font-size: 12px;">{{ $prediction->created_at->format('H:i') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('predictions.show', $prediction) }}" 
                                   style="flex: 1; min-width: 100px; display: inline-block; padding: 10px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; text-align: center; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                    View
                                </a>
                                @if($prediction->status === 'completed')
                                    <button onclick="confirmExport({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
                                            style="flex: 1; min-width: 100px; display: inline-block; padding: 10px 16px; background: transparent; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 500; font-size: 13px; transition: all 0.3s ease; cursor: pointer; text-align: center;">
                                        Export
                                    </button>
                                @else
                                    <span style="flex: 1; min-width: 100px; display: inline-block; padding: 10px 16px; background: transparent; color: #9ca3af; border: 1px solid #e5e7eb; border-radius: 6px; font-weight: 500; font-size: 13px; opacity: 0.5; text-align: center;">
                                        Export
                                    </span>
                                @endif
                                <button onclick="confirmDelete({{ $prediction->id }}, '{{ Str::limit($prediction->topic, 50) }}')" 
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
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 16px;">
                            <div style="display: flex; align-items: center;">
                                @if ($predictions->onFirstPage())
                                    <span style="color: #9ca3af; margin-right: 12px;">« Previous</span>
                                @else
                                    <a href="{{ $predictions->previousPageUrl() }}" style="color: #64748b; text-decoration: none; margin-right: 12px; transition: color 0.3s ease;">« Previous</a>
                                @endif

                                @if ($predictions->hasMorePages())
                                    <a href="{{ $predictions->nextPageUrl() }}" style="color: #64748b; text-decoration: none; transition: color 0.3s ease;">Next »</a>
                                @else
                                    <span style="color: #9ca3af;">Next »</span>
                                @endif
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                <div style="color: #64748b; font-size: 14px;">
                                    Showing {{ $predictions->firstItem() }} to {{ $predictions->lastItem() }} of {{ $predictions->total() }} results
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if (!$predictions->onFirstPage())
                                        <a href="{{ $predictions->previousPageUrl() }}" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease;">
                                            <span style="font-size: 16px;">‹</span>
                                        </a>
                                    @endif
                                    
                                    <div style="display: flex; gap: 4px;">
                                        @foreach ($predictions->getUrlRange(1, $predictions->lastPage()) as $page => $url)
                                            @if ($page == $predictions->currentPage())
                                                <span style="background: #667eea; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px;">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}" style="color: #64748b; text-decoration: none; padding: 8px 12px; border-radius: 6px; transition: all 0.3s ease; hover:background: #f1f5f9;">{{ $page }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    @if ($predictions->hasMorePages())
                                        <a href="{{ $predictions->nextPageUrl() }}" style="color: #64748b; text-decoration: none; padding: 8px; border-radius: 6px; transition: all 0.3s ease;">
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
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
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
<div id="exportModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
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
    
    @media (max-width: 768px) {
        .hidden-mobile {
            display: none !important;
        }
        
        .mobile-only {
            display: block !important;
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
        div[style*="padding: 32px 16px"] {
            padding: 20px 8px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 24px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 24px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 16px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 18px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 16px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 20px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 12px !important;
        }
        
        /* Stack header elements vertically on mobile */
        div[style*="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px"] {
            flex-direction: column !important;
            align-items: stretch !important;
            text-align: center !important;
        }
        
        div[style*="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px"] > div:last-child {
            order: -1 !important;
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
        
        /* Stack stats vertically on mobile */
        div[style*="display: flex; gap: 32px; flex-wrap: wrap"] {
            justify-content: center !important;
            gap: 20px !important;
        }
        
        /* Adjust emoji sizes for mobile */
        span[style*="font-size: 64px"] {
            font-size: 48px !important;
        }
        
        span[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
    }
    
    @media (max-width: 640px) {
        div[style*="padding: 32px 16px"] {
            padding: 16px 4px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 20px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 22px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 15px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 14px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 14px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 16px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 10px !important;
        }
        
        /* Adjust table padding for very small screens */
        th[style*="padding: 20px 24px"],
        td[style*="padding: 20px 24px"] {
            padding: 12px 8px !important;
        }
        
        /* Make table scrollable on very small screens */
        table {
            min-width: 500px !important;
        }
        
        /* Adjust emoji sizes for very small screens */
        span[style*="font-size: 64px"] {
            font-size: 40px !important;
        }
        
        span[style*="font-size: 24px"] {
            font-size: 18px !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="padding: 32px 16px"] {
            padding: 12px 2px !important;
        }
        
        div[style*="margin-bottom: 40px"] {
            margin-bottom: 16px !important;
        }
        
        h1[style*="font-size: 36px"] {
            font-size: 20px !important;
        }
        
        div[style*="font-size: 18px"] {
            font-size: 14px !important;
        }
        
        div[style*="padding: 24px"] {
            padding: 12px !important;
        }
        
        div[style*="gap: 24px"] {
            gap: 12px !important;
        }
        
        div[style*="gap: 32px"] {
            gap: 12px !important;
        }
        
        div[style*="gap: 16px"] {
            gap: 8px !important;
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
        
        /* Make buttons full width on very small screens */
        div[style*="display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap; gap: 24px"] a {
            width: 100% !important;
            padding: 14px 16px !important;
        }
    }
    
            /* Touch-friendly improvements */
        @media (max-width: 768px) {
            a, button {
                min-height: 44px !important;
                min-width: 44px !important;
            }
            
            /* Modal responsive improvements */
            #deleteModal > div {
                margin: 16px !important;
                padding: 24px !important;
                max-width: calc(100% - 32px) !important;
            }
            
            #deleteModal h3 {
                font-size: 18px !important;
            }
            
            #deleteModal p {
                font-size: 14px !important;
            }
            
            #deleteModal button {
                padding: 14px 20px !important;
                font-size: 14px !important;
            }
            
            /* Prevent zoom on iOS */
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
            
            /* Mobile card improvements */
            .mobile-only {
                padding: 16px !important;
            }
            
            .mobile-only > div {
                gap: 12px !important;
            }
            
            .mobile-only > div > div {
                padding: 16px !important;
            }
        }
        
        @media (max-width: 480px) {
            /* Mobile card improvements for very small screens */
            .mobile-only {
                padding: 12px !important;
            }
            
            .mobile-only > div > div {
                padding: 12px !important;
            }
            
            .mobile-only > div > div > div:first-child {
                margin-bottom: 12px !important;
            }
            
            .mobile-only > div > div > div:nth-child(2) {
                margin-bottom: 16px !important;
            }
        }
</style>
@endsection
